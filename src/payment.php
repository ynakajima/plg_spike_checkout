<?php
/**
 * SPIKE Checkoutプラグイン決済ページアクション
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

require_once PLUGIN_UPLOAD_REALDIR . 'plg_spike_checkout/define.php';
require_once PLG_SPIKE_CHECKOUT_CLASS_EXTENDS_REALDIR . '/page_extends/LC_Page_Mdl_Spike_Checkout_Payment_Ex.php';

$objPage = new LC_Page_Mdl_Spike_Checkout_Payment_Ex();
$objPage->init();
$objPage->process();
