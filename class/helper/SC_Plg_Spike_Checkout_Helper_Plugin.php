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
 * SPIKE CheckoutプラグインPluginヘルパークラス
 *
 * @package spike
 * @author Metaps, Inc.
 */
class SC_Plg_Spike_Checkout_Helper_Plugin
{
    /**
     * dtb_pluginからSpike Checkoutプラグインのレコードを取得する
     *
     * @return array dtb_pluginのレコード
     */
    public function getPlugin()
    {
        return SC_Plugin_Util_Ex::getPluginByPluginCode(PLG_SPIKE_CHECKOUT_PLUGIN_CODE);
    }

    /**
     * dtb_pluginからSpike Checkoutプラグインの設定を取得する
     *
     * @return array プラグイン設定
     */
    public function getConfig()
    {
        $arrPlugin = SC_Plugin_Util_Ex::getPluginByPluginCode(PLG_SPIKE_CHECKOUT_PLUGIN_CODE);
        if (empty($arrPlugin)) {
            return null;
        }
        return unserialize($arrPlugin['free_field1']);
    }

    /**
     * dtb_pluginのSpike Checkoutプラグインの設定をセットする
     *
     * @param $arrInputData 更新データ
     * @return array プラグイン設定
     */
    public function setConfig($arrInputData)
    {
        $arrConfig = $this->getConfig();
        if (empty($arrConfig)) {
            $arrConfig = $arrInputData;
        } else {
            $arrConfig = array_merge($arrConfig, $arrInputData);
        }

        $arrUpdate = array('free_field1' => serialize($arrConfig));
        $objQuery = SC_Query::getSingletonInstance();
        $objQuery->update('dtb_plugin', $arrUpdate, 'plugin_code = ?', array(PLG_SPIKE_CHECKOUT_PLUGIN_CODE));

        return $arrConfig;
    }
}
