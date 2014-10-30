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
/**
 * SPIKE CheckoutプラグインChargeヘルパーEXクラス
 *
 * @package spike
 * @author Metaps, Inc.
 */
class plugin_update
{
  /**
   * アップデート
   * updateはアップデート時に実行されます.
   * 引数にはdtb_pluginのプラグイン情報が渡されます.
   *
   * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
   * @return void
   */
  function update($arrPlugin)
  {
    require_once(__DIR__ . '/plugin_info.php');

    // バージョンの更新
    $objQuery = SC_Query_Ex::getSingletonInstance();
    $objQuery->begin();
    $plugin_id = $arrPlugin['plugin_id'];
    $plugin_version = plugin_info::$PLUGIN_VERSION;

    $objQuery = SC_Query_Ex::getSingletonInstance();
    $sqlval = array();
    $table = "dtb_plugin";
    $sqlval['plugin_version'] = $plugin_version;
    $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
    $where = "plugin_id = ?";
    $objQuery->update($table, $sqlval, $where, array($plugin_id));
    $objQuery->commit();

    // 変更ファイルの上書き
    $files = array(
      '/templates/default/load_payment_module.tpl',
      '/templates/sphone/load_payment_module.tpl',
    );

    foreach ($files as $file) {
      copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . $file, PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . $file);
    }
  }
}
