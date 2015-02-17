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
 * SPIKE CheckoutプラグインChargeヘルパークラス
 *
 * @package spike
 * @author Metaps, Inc.
 */
class SC_Plg_Spike_Checkout_Helper_Charge
{
    /**
     * SPIKE Checkoutプラグインで使用するユーザエージェントを返す
     *
     * @return string ユーザエージェント
     */
    public function getPluginUserAgent()
    {
        return sprintf("%s (%s)", PLG_SPIKE_CHECKOUT_USER_AGENT, HTTP_URL);
    }

    /**
     * SPIKE Checkout課金作成APIのエンドポイントを返す
     *
     * @return string エンドポイントURL
     */
    public function getChargeNewApiEndpoint()
    {
        return PLG_SPIKE_CHECKOUT_ENDPOINT_CHARGE_NEW;
    }

    /**
     * SPIKE Checkout課金取り消しAPIのエンドポイントを返す
     *
     * @param $charge_object_id 課金オブジェクトID
     * @return mixed エンドポイントURL
     */
    public function getChargeRefundApiEndpoint($charge_object_id)
    {
        return str_replace('{CHARGE_ID}', $charge_object_id, PLG_SPIKE_CHECKOUT_ENDPOINT_CHARGE_REFUND);
    }

    /**
     * SPIKE Checkout課金情報取得APIのエンドポイントを返す
     *
     * @param $charge_object_id 課金オブジェクトID
     * @return mixed エンドポイントURL
     */
    public function getChargeDataApiEndpoint($charge_object_id)
    {
        return str_replace('{CHARGE_ID}', $charge_object_id, PLG_SPIKE_CHECKOUT_ENDPOINT_CHARGE_DATA);
    }

    /**
     * SPIKE Checkout課金作成APIへリクエストを送信する
     *
     * @param $arrData 送信するPOSTデータ
     * @param $secret_key API呼び出し用の秘密鍵
     * @return bool|mixed 失敗時: false、成功時: レスポンスをJSONエンコードしたオブジェクト
     */
    public function sendChargeNewRequest($arrData, $secret_key)
    {
        $url = $this->getChargeNewApiEndpoint();
        $arrHeaders = array(
            'User-Agent: ' . $this->getPluginUserAgent(),
            'Authorization: Basic ' . base64_encode("${secret_key}:"),
        );
        $arrOptions = array('http' => array(
            'method' => 'POST',
            'content' => http_build_query($arrData),
            'header' => implode("\r\n", $arrHeaders),
            'ignore_errors' => true,
        ));

        $contents = file_get_contents($url, false, stream_context_create($arrOptions));
        if (empty($contents)) {
            return false;
        }

        return json_decode($contents, true);
    }

    /**
     * SPIKE Checkout課金取り消しAPIへリクエストを送信する
     *
     * @param $charge_object_id 課金オブジェクトID
     * @param $secret_key API呼び出し用の秘密鍵
     * @return bool|mixed 失敗時: false、成功時: レスポンスをJSONエンコードしたオブジェクト
     */
    public function sendChargeRefundRequest($charge_object_id, $secret_key)
    {
        $url = $this->getChargeRefundApiEndpoint($charge_object_id);
        $arrHeaders = array(
            'User-Agent: ' . $this->getPluginUserAgent(),
            'Authorization: Basic ' . base64_encode("${secret_key}:"),
        );
        $arrOptions = array('http' => array(
            'method' => 'POST',
            'content' => "\r\n", // Set empty contents.
            'header' => implode("\r\n", $arrHeaders),
            'ignore_errors' => true,
        ));

        $contents = file_get_contents($url, false, stream_context_create($arrOptions));
        if (empty($contents)) {
            return false;
        }

        return json_decode($contents, true);
    }

    /**
     * SPIKE Checkout課金情報取得APIへリクエストを送信する
     *
     * @param $charge_object_id 課金オブジェクトID
     * @param $secret_key API呼び出し用の秘密鍵
     * @return bool|mixed 失敗時: false、成功時: レスポンスをJSONエンコードしたオブジェクト
     */
    public function sendChargeDataRequest($charge_object_id, $secret_key)
    {
        $url = $this->getChargeDataApiEndpoint($charge_object_id);
        $arrHeaders = array(
            'User-Agent: ' . $this->getPluginUserAgent(),
            'Authorization: Basic ' . base64_encode("${secret_key}:"),
        );
        $arrOptions = array('http' => array(
            'method' => 'GET',
            'header' => implode("\r\n", $arrHeaders),
            'ignore_errors' => true,
        ));

        $contents = file_get_contents($url, false, stream_context_create($arrOptions));
        if (empty($contents)) {
            return false;
        }

        return json_decode($contents, true);
    }
}
