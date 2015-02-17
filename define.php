<?php
/**
 * 定義用ファイル
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

define('PLG_SPIKE_CHECKOUT_PLUGIN_CODE', 'plg_spike_checkout');
define('PLG_SPIKE_CHECKOUT_MODULE_CODE', 'mdl_spike_checkout');

define('PLG_SPIKE_CHECKOUT_PLUGIN_NAME',  'SPIKE Checkoutクレジットカード決済');
define('PLG_SPIKE_CHECKOUT_PAYMENT_NAME', 'SPIKEクレジットカード決済');

define('PLG_SPIKE_CHECKOUT_REALDIR',                PLUGIN_UPLOAD_REALDIR . PLG_SPIKE_CHECKOUT_PLUGIN_CODE . '/');
define('PLG_SPIKE_CHECKOUT_CLASS_REALDIR',          PLG_SPIKE_CHECKOUT_REALDIR . 'class/');
define('PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR',  PLG_SPIKE_CHECKOUT_REALDIR . 'class_extends/');
define('PLG_SPIKE_CHECKOUT_TEMPLATES_REALDIR',      PLG_SPIKE_CHECKOUT_REALDIR . 'templates/');
define('PLG_SPIKE_CHECKOUT_MDL_REALDIR',            MODULE_REALDIR . PLG_SPIKE_CHECKOUT_MODULE_CODE . '/');

define('PLG_SPIKE_CHECKOUT_CURRENCY_JPY', 'JPY');
define('PLG_SPIKE_CHECKOUT_LANGUAGE_JA',  'JA');

define('PLG_SPIKE_CHECKOUT_PRICE_JPY_MIN', 300);
define('PLG_SPIKE_CHECKOUT_PRICE_JPY_FREE_MAX', 60000);

define('PLG_SPIKE_CHECKOUT_CHECKOUT_JS_URL', 'https://checkout.spike.cc/v1/checkout.js');
define('PLG_SPIKE_CHECKOUT_ENDPOINT_HOST',   'https://api.spike.cc');
define('PLG_SPIKE_CHECKOUT_USER_AGENT',      'EC-CUBE SPIKE Checkout Plugin/1.0');
define('PLG_SPIKE_CHECKOUT_ENDPOINT_CHARGE_NEW',    PLG_SPIKE_CHECKOUT_ENDPOINT_HOST . '/v1/charges');
define('PLG_SPIKE_CHECKOUT_ENDPOINT_CHARGE_REFUND', PLG_SPIKE_CHECKOUT_ENDPOINT_HOST . '/v1/charges/{CHARGE_ID}/refund');

define('PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT',      'memo02');
define('PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_ID',   'memo03');
define('PLG_SPIKE_CHECKOUT_ORDER_COL_CHECKOUT_TOKEN',     'memo04');
define('PLG_SPIKE_CHECKOUT_ORDER_COL_PAYMENT_STATUS',     'memo05');
define('PLG_SPIKE_CHECKOUT_ORDER_COL_API_LOGS',           'memo06');
define('PLG_SPIKE_CHECKOUT_ORDER_COL_CHARGE_OBJECT_LOGS', 'memo07');

define('PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_UNSETTLED',  0);  // 未決済
//define('PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_AUTHORIZED', 11); // 仮売上済
define('PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_CAPTURED',   12); // 実売上済
define('PLG_SPIKE_CHECKOUT_PAYMENT_STATUS_CANCELED',   13); // 取消済
