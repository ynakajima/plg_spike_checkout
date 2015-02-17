<!--{*
 * SPIKE Checkoutプラグイン注文編集画面追加ファイル
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
 *}-->
<!--{if $is_spike_checkout}-->
<script type="text/javascript"><!--
  function fnPlgSpikeCheckoutConfirm(mode, anchor, anchor_name) {
    if(window.confirm('決済操作を行います。\n受注データを編集していない場合は先に保存して下さい。\nよろしいですか？')) {
      fnModeSubmit(mode, anchor, anchor_name);
    }
  }
//--></script>

<h2>SPIKE Checkoutクレジットカード決済 決済情報</h2>
<table class="form" id="plg_spike_checkout_form">
  <tr>
    <th>決済種別</th>
    <td><!--{$arrSpikeOrder.payment_method}-->(<!--{$arrSpikeOrder.payment_id}-->)</td>
  </tr>
  <tr>
    <th>取引状態</th>
    <td>
      <!--{if $is_spike_payment_status_captured}-->
        実売上済み
      <!--{elseif is_spike_payment_status_canceled}-->
        取消済み
      <!--{else}-->
        不明な状態
      <!--{/if}-->
    </td>
  </tr>
  <!--{if $spike_checkout_error}-->
    <tr>
      <th>決済操作エラー</th>
      <td class="attention"><!--{$spike_checkout_error|h}--></td>
    </tr>
  <!--{/if}-->
  <tr>
    <th>チェックアウトトークン</th>
    <td><!--{$spike_checkout_token}--></td>
  </tr>
  <tr>
    <th>課金オブジェクトID</th>
    <td><!--{$spike_charge_object_id}--></td>
  </tr>
  <tr>
    <th>決済接続環境</th>
    <td>
      <!--{if $arrSpikeCharge.livemode}-->
        本番環境
      <!--{else}-->
        サンドボックス環境
      <!--{/if}-->
    </td>
  </tr>
  <tr>
    <th>決済日時</th>
    <td><!--{$arrSpikeCharge.created|date_format:"%Y/%m/%d %H:%M:%S"}--></td>
  </tr>
  <tr>
    <th>決済金額</th>
    <td><!--{$arrSpikeCharge.amount|number_format|h}-->円</td>
  </tr>
  <tr>
    <th>決済取消</th>
    <td>
      <!--{if $arrSpikeCharge.refunded}-->
        <!--{$arrSpikeCharge.refunds[0].created|date_format:"%Y/%m/%d %H:%M:%S"}-->
      <!--{else}-->
        --
      <!--{/if}-->
    </td>
  </tr>
  <tr>
    <th>決済操作</th>
    <td>
      <!--{if $is_spike_payment_status_captured}-->
        <a class="btn-normal" href="javascript:void(0);" onclick="fnPlgSpikeCheckoutConfirm('plg_spike_checkout_cancel','','');">決済取消</a>&nbsp;
      <!--{else}-->
        <a class="btn-normal" href="javascript:void(0);" onclick="fnPlgSpikeCheckoutConfirm('plg_spike_checkout_charge','','');">現在のお支払い合計金額（<!--{$arrSpikeOrder.payment_total|default:0|number_format|h}-->円）で再決済</a>&nbsp;
      <!--{/if}-->
    </td>
  </tr>
</table>

<h3>SPIKE Checkoutクレジットカード決済 決済ログ</h3>
<table class="form" id="plg_spike_checkout_form">
  <tr>
    <th>決済日時</th>
    <th style="width: 80px;">取引状態</th>
    <th>決済金額</th>
    <th>課金オブジェクトID</th>
    <th>取消日時</th>
  </tr>
  <!--{foreach from=$arrSpikeChargeLogs item=arrSpikeChargeLog name=spikeChargeLog}-->
  <tr>
    <td><!--{$arrSpikeChargeLog.created|date_format:"%Y/%m/%d %H:%M:%S"}--></td>
    <td style="width: 80px;">
      <!--{if $arrSpikeChargeLog.paid && !$arrSpikeChargeLog.refunded}-->
        実売上済み
      <!--{else}-->
        取消済み
      <!--{/if}-->
    </td>
    <td><!--{$arrSpikeChargeLog.amount|number_format|h}-->円</td>
    <td><!--{$arrSpikeChargeLog.id|h}--></td>
    <td>
      <!--{if $arrSpikeChargeLog.refunded}-->
        <!--{$arrSpikeChargeLog.refunds[0].created|date_format:"%Y/%m/%d %H:%M:%S"}-->
      <!--{else}-->
        --
      <!--{/if}-->
    </td>
  </tr>
  <!--{/foreach}-->
</table>


<h2>受注詳細</h2>
<!--{/if}-->
