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

require_once PLUGIN_UPLOAD_REALDIR . 'plg_spike_checkout/define.php';
require_once PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR . 'helper_extends/SC_Plg_Spike_Checkout_Helper_Charge_Ex.php';
require_once PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR . 'helper_extends/SC_Plg_Spike_Checkout_Helper_Plugin_Ex.php';
require_once PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR . 'page_extends/LC_Page_Mdl_Spike_Checkout_Payment_Ex.php';

/**
 * SPIKE CheckoutプラグインHookヘルパークラス
 *
 * @package spike
 * @author Metaps, Inc.
 */
class SC_Plg_Spike_Checkout_Helper_Hook
{
    /**
     * 受注管理注文編集画面を表示するための準備処理
     *
     * @param LC_Page_Ex $objPage
     */
    public function prepareAdminOrderEditDisplay(LC_Page_Ex $objPage)
    {
        $objFormParam = new SC_FormParam_Ex();
        $objFormParam->addParam('注文番号', 'order_id');
        $objFormParam->setParam($_REQUEST);
        $order_id = $objFormParam->getValue('order_id');

        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($order_id);

        $arrPayment = SC_Helper_Payment_Ex::get($arrOrder['payment_id']);
        $objPage->is_spike_checkout = $arrPayment['memo03'] == PLG_SPIKE_CHECKOUT_MODULE_CODE;

        if (SC_Utils_Ex::isBlank($order_id) || ! $objPage->is_spike_checkout) {
            return;
        }

        $objPage->arrSpikeOrder = $arrOrder;

        $objPage->spike_charge_object_id = $arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_ID];
        $objPage->spike_checkout_token = $arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_CHECKOUT_TOKEN];
        $objPage->is_spike_payment_status_captured =
            $arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_PAYMENT_STATUS] == PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_CAPTURED;
        $objPage->is_spike_payment_status_canceled =
            $arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_PAYMENT_STATUS] == PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_CANCELED;

        $objPage->arrSpikeCharge = unserialize($arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT]);

        $arrSpikeChargeLogs = unserialize($arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_LOGS]);
        $objPage->arrSpikeChargeLogs = is_array($arrSpikeChargeLogs) ? $arrSpikeChargeLogs : array($objPage->arrSpikeCharge);
    }

    /**
     * 決済のキャンセル処理
     *
     * @param LC_Page_Ex $objPage
     */
    public function performChargeCancel(LC_Page_Ex $objPage)
    {
        $objFormParam = new SC_FormParam_Ex();
        $objFormParam->addParam('注文番号', 'order_id');
        $objFormParam->addParam('支払方法', 'payment_id');
        $objFormParam->setParam($_REQUEST);

        $order_id = $objFormParam->getValue('order_id');
        $payment_id = $objFormParam->getValue('payment_id');

        $arrPayment = SC_Helper_Payment_Ex::get($payment_id);
        $objPage->is_spike_checkout = $arrPayment['memo03'] == PLG_SPIKE_CHECKOUT_MODULE_CODE;

        if (SC_Utils_Ex::isBlank($order_id) || ! $objPage->is_spike_checkout) {
            return;
        }

        $objHelper = new SC_Plg_Spike_Checkout_Helper_Plugin_Ex();
        $arrConfig = $objHelper->getConfig();
        $secret_key = $arrConfig['api_secret_key'];

        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($order_id);
        $charge_object_id = $arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_ID];

        $arrResponse = array();

        try {
            $objCharge = new SC_Plg_Spike_Checkout_Helper_Charge_Ex();
            $arrResponse = $objCharge->sendChargeRefundRequest($charge_object_id, $secret_key);
            if (empty($arrResponse) || empty($arrResponse['id'])) {
                $error_message = '決済のキャンセルに失敗しました。';
                if (! empty($arrResponse['error'])) {
                    $error_message .= sprintf("(TYPE: %s, MESSAGE: %s)", $arrResponse['error']['type'], $arrResponse['error']['message']);
                }
                $objPage->spike_checkout_error = $error_message;
                return;
            }
        } catch (Exception $objException) {
            $objPage->spike_checkout_error = '決済のキャンセルに失敗しました。';
            return;
        }

        // ログに追加
        $arrSpikeChargeLogs = unserialize($arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_LOGS]);
        if (!is_array($arrSpikeChargeLogs)) {
            $arrSpikeChargeLogs = array($arrResponse);
        } else {
            $logIndex = -1;
            for($i = 0, $l = count($arrSpikeChargeLogs); $i < $l; $i++) {
                if ($arrSpikeChargeLogs[$i]['id'] === $arrResponse['id']) {
                    $arrSpikeChargeLogs[$i] = $arrResponse;
                    $logIndex = $i;
                    break;
                }
            }
            if ($logIndx === -1) {
                array_push($arrSpikeChargeLogs, $arrResponse);
            }
        }

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        $sqlval = array(
            PLG_SPIKE_CHECKOUT_ORDER_COL_PAYMENT_STATUS => PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_CANCELED,
            PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT => serialize($arrResponse),
            PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_LOGS => serialize($arrSpikeChargeLogs),
        );
        $objPurchase->sfUpdateOrderStatus($order_id, null, null, null, $sqlval);
        $objQuery->commit();
    }

    /**
     * 再決済処理
     *
     * @param LC_Page_Ex $objPage
     */
    public function performReCharge(LC_Page_Ex $objPage)
    {
        $objFormParam = new SC_FormParam_Ex();
        $objFormParam->addParam('注文番号', 'order_id');
        $objFormParam->addParam('支払方法', 'payment_id');
        $objFormParam->setParam($_REQUEST);

        $order_id = $objFormParam->getValue('order_id');
        $payment_id = $objFormParam->getValue('payment_id');

        $arrPayment = SC_Helper_Payment_Ex::get($payment_id);
        $objPage->is_spike_checkout = $arrPayment['memo03'] == PLG_SPIKE_CHECKOUT_MODULE_CODE;

        if (SC_Utils_Ex::isBlank($order_id) || ! $objPage->is_spike_checkout) {
            return;
        }

        $objHelper = new SC_Plg_Spike_Checkout_Helper_Plugin_Ex();
        $arrConfig = $objHelper->getConfig();
        $secret_key = $arrConfig['api_secret_key'];

        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($order_id);

        $arrData = array(
          'token' => $arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_CHECKOUT_TOKEN]
        );

        $arrResponse = array();

        try {
            $objCharge = new LC_Page_Mdl_Spike_Checkout_Payment();
            $arrResponse = $objCharge->lfChargeBySpike($arrData, $arrOrder, $arrConfig);
            if (empty($arrResponse) || empty($arrResponse['id'])) {
                $error_message = '再決済に失敗しました。';
                if (! empty($arrResponse['error'])) {
                    $error_message .= sprintf("(TYPE: %s, MESSAGE: %s)", $arrResponse['error']['type'], $arrResponse['error']['message']);
                }
                $objPage->spike_checkout_error = $error_message;
                return;
            }
        } catch (Exception $objException) {
            $objPage->spike_checkout_error = '再決済に失敗しました。';
            return;
        }

        // ログに追加
        $arrSpikeChargeLogs = unserialize($arrOrder[PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_LOGS]);
        if (!is_array($arrSpikeChargeLogs)) {
            $arrSpikeChargeLogs = array($arrResponse);
        } else {
            array_push($arrSpikeChargeLogs, $arrResponse);
        }

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        $sqlval = array(
            PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_ID => $arrResponse['id'],
            PLG_SPIKE_CHECKOUT_ORDER_COL_PAYMENT_STATUS => PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_CAPTURED,
            PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT => serialize($arrResponse),
            PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_LOGS => serialize($arrSpikeChargeLogs),
        );
        $objPurchase->sfUpdateOrderStatus($order_id, null, null, null, $sqlval);
        $objQuery->commit();
    }
}
