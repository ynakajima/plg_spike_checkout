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

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once PLUGIN_UPLOAD_REALDIR . 'plg_spike_checkout/define.php';
require_once PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR . 'helper_extends/SC_Plg_Spike_Checkout_Helper_Plugin_Ex.php';

/**
 * SPIKE Checkoutプラグイン設定ページクラス
 *
 * @package spike
 * @author Metaps, Inc.
 */
class LC_Page_Plg_Spike_Checkout_Config extends LC_Page_Admin_Ex
{
    /**
     * 初期化
     *
     * @return void
     */
    function init()
    {
        parent::init();

        $this->tpl_mainpage = PLG_SPIKE_CHECKOUT_TEMPLATES_REALDIR . 'admin/config.tpl';
        $this->tpl_subtitle = PLG_SPIKE_CHECKOUT_PLUGIN_NAME;
    }

    /**
     * 実行
     *
     * @return void
     */
    function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * ページのアクション
     *
     * @return void
     */
    function action()
    {
        $this->setTemplate($this->tpl_mainpage);

        $objFormParam = new SC_FormParam_Ex();
        $objFormParam = $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);

        $objHelper = new SC_Plg_Spike_Checkout_Helper_Plugin_Ex();

        switch ($_POST['mode']) {
        case 'edit':
            $this->arrErr = $objFormParam->checkError();
            if (count($this->arrErr) == 0) {
                $objHelper->setConfig($objFormParam->getHashArray());
                $this->tpl_onload = 'alert("登録完了しました。\nプラグインを有効化した後、基本情報＞支払方法設定より詳細設定をおこなってください。"); window.close();';
            }
            break;

        default:
            $arrConfig = $objHelper->getConfig();
            $objFormParam->setParam($arrConfig);
            break;
        }
        
        $this->arrForm = $objFormParam->getFormParamList();
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
     * パラメータの初期化
     *
     * @param $objFormParam SC_FormParam
     * @return SC_FormParam 引数で受け取った$objFormParam
     */
    function lfInitParam($objFormParam)
    {
        $objFormParam->addParam('公開鍵', 'api_public_key', SMTEXT_LEN, 'a', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('秘密鍵', 'api_secret_key', SMTEXT_LEN, 'a', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ゲスト決済の利用', 'guest_checkout');

        return $objFormParam;
    }
}
