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

/**
 * SPIKE CheckoutプラグインPaymentヘルパークラス
 *
 * @package spike
 * @author Metaps, Inc.
 */
class SC_Plg_Spike_Checkout_Helper_Payment
{
    /**
     * dtb_paymentからSPIKE Checkoutのデータを取得する
     *
     * @return array dtb_paymentのレコード
     */
    public function getPaymentData()
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrRet = $objQuery->select('*', 'dtb_payment', 'memo03 = ?', array(PLG_SPIKE_CHECKOUT_MODULE_CODE));
        return $arrRet[0];
    }

    /**
     * dtb_paymentのSPIKE Checkoutのデータを保存(insert or update)する
     *
     * @param array $arrInputData
     * @return array
     */
    public function savePaymentData($arrInputData = array())
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        $arrData = array();
        $arrPaymentData = $this->getPaymentData();

        if (empty($arrPaymentData['payment_id'])) {
            $objSession = new SC_Session_Ex();

            $arrData = array_merge($arrData, $arrInputData);

            $arrData['creator_id'] = $objSession->member_id;
            $arrData['create_date'] = 'now()';
            $arrData['update_date'] = 'now()';
            $arrData['payment_id'] = $objQuery->nextVal('dtb_payment_payment_id');

            $objQuery->insert('dtb_payment', $arrData);
        }
        else {
            unset($arrInputData['payment_id']); // Safety blocking

            $arrData = array_merge($arrPaymentData, $arrInputData);
            $arrData['update_date'] = 'now()';

            $objQuery->update('dtb_payment', $arrData, 'payment_id = ?', array($arrPaymentData['payment_id']));
        }

        return $arrData;
    }

    /**
     * dtb_paymenのrankの最大値を取得する
     *
     * @return int
     */
    public function getPaymentMaxRank()
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        return $objQuery->getOne('SELECT max(rank) FROM dtb_payment');
    }

    /**
     * dtb_paymentのSPIKE Checkoutのデータを初期化する
     *
     * @return mixed dtb_paymentのレコード
     */
    public function initPaymentDB()
    {
        $arrData = array();

        $arrData['payment_method'] = PLG_SPIKE_CHECKOUT_PAYMENT_NAME;
        $arrData['memo03'] = PLG_SPIKE_CHECKOUT_MODULE_CODE; // Identifier to detect a spike payment recoard
        $arrData['module_path'] = realpath(PLG_SPIKE_CHECKOUT_MDL_REALDIR . 'payment.php');

        $arrData['del_flg'] = 0;

        $arrData['charge_flg'] = '2'; // Unsupported charge price
        $arrData['charge'] = 0;

        $arrData['fix'] = 2; // Changeable payment record

        $arrData['rule_max']   = PLG_SPIKE_CHECKOUT_PRICE_JPY_MIN;
        $arrData['upper_rule'] = PLG_SPIKE_CHECKOUT_PRICE_JPY_FREE_MAX;
        $arrData['rule_min'] = PLG_SPIKE_CHECKOUT_PRICE_JPY_MIN;
        //$arrData['upper_rule_max'] = 9999999;

        $arrPaymentData = $this->getPaymentData();
        if (empty($arrPaymentData['payment_id']) || $arrPaymentData['del_flg'] == 1) {
            $arrData['rank'] = $this->getPaymentMaxRank() + 1;
        }

        return $this->savePaymentData($arrData);
    }

    /**
     * dtb_paymentのSPIKE Checkoutのデータを削除状態に変更する
     *
     * @return void
     */
    public function deletePaymentDB()
    {
        $arrPaymentData = $this->getPaymentData();
        if (empty($arrPaymentData)) {
            return;
        }

        $objPayment = new SC_Helper_Payment_Ex();
        $objPayment->delete($arrPaymentData['payment_id']);
    }
}
