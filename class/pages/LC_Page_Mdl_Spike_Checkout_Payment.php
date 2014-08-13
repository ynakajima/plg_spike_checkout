<?php
/**
 * Copyright(c) 2014 Metaps, Inc. All rights reserved.
 *
 * https://spike.cc/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
require_once PLUGIN_UPLOAD_REALDIR . 'plg_spike_checkout/define.php';
require_once PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR . 'helper_extends/SC_Plg_Spike_Checkout_Helper_Charge_Ex.php';
require_once PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR . 'helper_extends/SC_Plg_Spike_Checkout_Helper_Plugin_Ex.php';

/**
 * SPIKE Checkoutプラグイン決済ページクラス
 *
 * @package spike
 * @author Metaps, Inc.
 */
class LC_Page_Mdl_Spike_Checkout_Payment extends LC_Page_Ex
{
    /**
     * 初期化
     *
     * @return void
     */
    function init()
    {
        parent::init();

        $this->httpCacheControl('nocache');
    }

    /**
     * 実行
     *
     * @return void
     */
    function process()
    {
        parent::process();

        $this->action();
        $this->sendResponse();
    }

    /**
     * アクション
     *
     * @return void
     */
    function action()
    {
        // Check order
        if (!SC_Utils_Ex::isBlank($_SESSION['order_id'])) {
            $order_id = $_SESSION['order_id'];
        } else if (!SC_Utils_Ex::isBlank($_REQUEST['order_id'])
            && SC_Utils_Ex::sfIsInt($_REQUEST['order_id'])
            && $this->lfIsValidToken($_REQUEST['order_id'], $_REQUEST[TRANSACTION_ID_NAME])) {
            $order_id = $_REQUEST['order_id'];
            $_SESSION['order_id'] = $order_id;
        } else {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', true,
                '例外エラー<br />注文情報の取得が出来ませんでした。<br />この手続きは無効となりました。');
        }

        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($order_id);

        // Check order status
        if ($arrOrder['status'] != ORDER_PENDING) {
            switch ($arrOrder['status']) {
                case ORDER_PAY_WAIT:
                    SC_Response_Ex::sendRedirect(SHOPPING_COMPLETE_URLPATH);
                    SC_Response_Ex::actionExit();
                    break;
                default:
                    SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', true,
                        '例外エラー<br />注文情報が無効です。<br />この手続きは無効となりました。');
                    SC_Response_Ex::actionExit();
                    break;
            }
        }

        // Template switching
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_PC) {
            $this->tpl_mainpage = PLG_SPIKE_CHECKOUT_TEMPLATES_REALDIR . 'default/load_payment_module.tpl';
        } elseif (SC_Display_Ex::detectDevice() == DEVICE_TYPE_SMARTPHONE) {
            $this->tpl_mainpage = PLG_SPIKE_CHECKOUT_TEMPLATES_REALDIR . 'sphone/load_payment_module.tpl';
        } else {
            // DEVICE_TYPE_MOBILE or etc.
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', true, '携帯電話ではこの支払方法は利用できません');
        }

        $objHelper = new SC_Plg_Spike_Checkout_Helper_Plugin_Ex();
        $arrConfig = $objHelper->getConfig();

        switch ($this->getMode()) {
            case 'charge':
                // Form
                $objFormParam = new SC_FormParam_Ex();
                $objFormParam = $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_REQUEST);
                $objFormParam->convParam();

                $this->arrErr = $objFormParam->checkError();
                if (!empty($this->arrErr)) {
                    break;
                }

                $objQuery = SC_Query_Ex::getSingletonInstance();
                $arrData = $objFormParam->getHashArray();

                // Update temporary status and data
                $objQuery->begin();
                $sqlval = array(
                    PLG_SPIKE_CHECKOUT_ORDER_COL_CHECKOUT_TOKEN => $arrData['token'],
                    PLG_SPIKE_CHECKOUT_ORDER_COL_PAYMENT_STATUS => PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_UNSETTLED,
                );
                $objPurchase->sfUpdateOrderStatus($arrOrder['order_id'], ORDER_PENDING, null, null, $sqlval);
                $objQuery->commit();

                // Perform charge logic
                $arrChargeObject = $this->lfChargeBySpike($arrData, $arrOrder, $arrConfig);
                if ($arrChargeObject == false) {
                    SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', false, '例外エラー<br />決済処理が失敗しました。');
                    SC_Response_Ex::actionExit();
                    break;
                }

                $objQuery->begin();
                $sqlval = array(
                    PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT => $arrChargeObject,
                    PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_ID => $arrChargeObject['id'],
                    PLG_SPIKE_CHECKOUT_ORDER_COL_PAYMENT_STATUS => PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_CAPTURED,
                );
                $objPurchase->sfUpdateOrderStatus($arrOrder['order_id'], ORDER_PRE_END, null, null, $sqlval);
                $objQuery->commit();

                // Send mail
                $objPurchase->sendOrderMail($arrOrder['order_id']);

                SC_Response_Ex::sendRedirect(SHOPPING_COMPLETE_URLPATH);
                SC_Response_Ex::actionExit();
                break;

            default:
                break;
        }

        $this->tpl_url = $_SERVER['REQUEST_URI'];
        $this->tpl_title = $arrOrder['payment_method'];
        $this->tpl_checkout_js_url = PLG_SPIKE_CHECKOUT_CHECKOUT_JS_URL;

        $this->tpl_api_public_key = $arrConfig['api_public_key'];
        $this->tpl_order_email = $arrOrder['order_email'];
        $this->tpl_currency = PLG_SPIKE_CHECKOUT_CURRENCY_JPY;
        $this->tpl_payment_total = $arrOrder['payment_total'];
        $this->tpl_use_guest = $arrConfig['guest_checkout'] == '1' ? 'true' : 'false';

        $arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $this->tpl_shop_name = $arrInfo['shop_name'];
    }

    /**
     * デストラクタ
     *
     * @return void
     */
    function destroy()
    {
        parent::destroy();
    }

    /**
     * 外部ページからの遷移の際に受注情報内のTRANSACTION IDとのCSFRチェックを行う。
     *
     * @param integer $order_id 受注ID
     * @param text $transactionid TRANSACTION ID
     * @return boolean
     */
    function lfIsValidToken($order_id, $transactionid)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        return $objQuery->get(MDL_PG_MULPAY_ORDER_COL_TRANSID, 'dtb_order', 'order_id = ?', array($order_id)) == $transactionid;
    }

    /**
     * フォームのパラメータを初期化する
     *
     * @param $objFormParam SC_FormParam_Ex
     * @return object 引数で受け取ったオブジェクト
     */
    function lfInitParam($objFormParam)
    {
        $objFormParam->addParam('チェックアウトトークン', 'token', SMTEXT_LEN, 'a', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        return $objFormParam;
    }

    /**
     * Charge APIを使用してSPIKEでの決済を実行する
     *
     * @param $arrData フォームの値
     * @param $arrOrder 注文情報
     * @param $arrConfig プラグイン設定情報
     * @return bool|mixed 失敗時: false、成功時: レスポンスをJSONエンコードしたオブジェクト
     */
    function lfChargeBySpike($arrData, $arrOrder, $arrConfig)
    {
        $arrParamProducts = array();

        $objPurchase = new SC_Helper_Purchase_Ex();
        $objProduct = new SC_Product_Ex();

        // Retrieve product data
        $arrOrderDetails = $objPurchase->getOrderDetail($arrOrder['order_id']);
        foreach ($arrOrderDetails as $arrOrderDetail) {

            $arrProductDetail = $objProduct->getDetail($arrOrderDetail['product_id']);

            $arrProductClass = $objProduct->getProductsClass($arrOrderDetail['product_class_id']);
            $stock = empty($arrProductClass['stock']) ? 1 : $arrProductClass['stock'];

            $arrParamProducts[] = array(
                'title'         => $arrOrderDetail['product_name'],
                'description'   => $arrProductDetail['main_comment'],
                'language'      => PLG_SPIKE_CHECKOUT_LANGUAGE_JA,
                'price'         => $arrOrderDetail['price'], // Not including tax
                'currency'      => PLG_SPIKE_CHECKOUT_CURRENCY_JPY,
                'count'         => $arrOrderDetail['quantity'],
                'id'            => $arrOrderDetail['product_class_id'],
                'stock'         => $stock,
            );
        }

        // Call API
        try {
            $arrParams = array(
                'amount' => $arrOrder['payment_total'],
                'currency' => PLG_SPIKE_CHECKOUT_CURRENCY_JPY,
                'card' => $arrData['token'],
                'products' => json_encode($arrParamProducts),
            );

            $objSpikePurchase = new SC_Plg_Spike_Checkout_Helper_Charge_Ex();
            $arrResponse = $objSpikePurchase->sendChargeNewRequest($arrParams, $arrConfig['api_secret_key']);
            if (empty($arrResponse) || empty($arrResponse['id'])) {
                return false;
            }

            return $arrResponse;

        } catch (Exception $objException) {
            return false;
        }
    }
}
