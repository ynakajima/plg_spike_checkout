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
require_once PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR . 'helper_extends/SC_Plg_Spike_Checkout_Helper_Hook_Ex.php';
require_once PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR . 'helper_extends/SC_Plg_Spike_Checkout_Helper_Payment_Ex.php';

/**
 * SPIKE Checkoutプラグインクラス
 *
 * @package SpikeCheckout
 */
class SC_Plg_Spike_Checkout extends SC_Plugin_Base
{
    /**
     * インストール
     *
     * @param array $arrPlugin プラグイン情報(dtb_plugin)の連想配列
     * @return void
     */
    function install($arrPlugin)
    {
        // plugin files
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/src/logo.png', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/logo.png");

        // module files
        SC_Utils_Ex::recursiveMkdir(PLG_SPIKE_CHECKOUT_MDL_REALDIR, 0755);
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/src/payment.php', PLG_SPIKE_CHECKOUT_MDL_REALDIR . '/payment.php');
    }

    /** 
     * アンインストール
     *
     * @param array $arrPlugin プラグイン情報(dtb_plugin)の連想配列
     * @return void
     */
    function uninstall($arrPlugin)
    {
        // module files
        SC_Helper_FileManager_Ex::deleteFile(PLG_SPIKE_CHECKOUT_MDL_REALDIR);
    }

    /**
     * 有効化
     *
     * @param array $arrPlugin プラグイン情報(dtb_plugin)の連想配列
     * @return void
     */
    function enable($arrPlugin)
    {
        $objHelper = new SC_Plg_Spike_Checkout_Helper_Payment_Ex();
        $objHelper->initPaymentDB();
    }

    /**
     * 無効化
     *
     * @param array $arrPlugin プラグイン情報(dtb_plugin)の連想配列
     * @return void
     */
    function disable($arrPlugin)
    {
        $objHelper = new SC_Plg_Spike_Checkout_Helper_Payment_Ex();
        $objHelper->deletePaymentDB();
    }

    /**
     * 受注管理注文編集画面Beforeローカルフックポイント
     *
     * @param LC_Page_Ex $objPage
     */
    function lfHookAdminOrderEditActionBefore(LC_Page_Ex $objPage)
    {
        switch ($objPage->getMode()) {
            case 'pre_edit':
            case 'edit':
                $objHook = new SC_Plg_Spike_Checkout_Helper_Hook_Ex();
                $objHook->prepareAdminOrderEditDisplay($objPage);
                break;

            case 'plg_spike_checkout_cancel':
                $objHook = new SC_Plg_Spike_Checkout_Helper_Hook_Ex();
                $objHook->performChargeCancel($objPage);
                $objHook->prepareAdminOrderEditDisplay($objPage);
                break;

            default:
                break;
        }
    }

    /**
     * prefilterコールバック関数
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename)
    {
        $objTransform = new SC_Helper_Transform($source);
        switch($objPage->arrPageLayout['device_type_id']) {
            case DEVICE_TYPE_ADMIN:
            default:
                if (strpos($filename, 'order/edit.tpl') !== false) {
                    $objTransform->select('div#order')->appendFirst(file_get_contents(PLG_SPIKE_CHECKOUT_TEMPLATES_REALDIR . 'admin/order_edit.inc.tpl'));
                }
                break;
        }

        $source = $objTransform->getHTML();
    }
}
