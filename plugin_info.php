<?php
/**
 * SPIKE Checkout Plugin for EC-CUBE
 *
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

/**
 * プラグインの情報クラス
 *
 * @package spike
 * @author Metaps, Inc.
 */
class plugin_info
{
    static $PLUGIN_CODE       = 'plg_spike_checkout';
    static $PLUGIN_NAME       = 'SPIKE Checkoutクレジットカード決済';
    static $CLASS_NAME        = 'SC_Plg_Spike_Checkout';
    static $PLUGIN_VERSION    = '1.1';
    static $COMPLIANT_VERSION = '2.13.0,2.12.0';
    static $AUTHOR            = 'SPIKE';
    static $DESCRIPTION       = 'SPIKE Checkoutを用いたクレジットカード決済が利用可能です。';
    static $PLUGIN_SITE_URL   = 'https://spike.cc/';
    static $AUTHOR_SITE_URL   = 'https://spike.cc/';
    static $LICENSE           = 'GPL3';
    static $HOOK_POINTS = array(
        array('LC_Page_Admin_Order_Edit_action_before', 'lfHookAdminOrderEditActionBefore'),
        array('prefilterTransform', 'prefilterTransform'));
}
