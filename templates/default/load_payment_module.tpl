<!--{*
 * SPIKE Checkoutプラグイン決済ページテンプレート
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
<style type="text/css">

    .spike-button {
        width: 190px;
        height: 45px;
        line-height: 45px;
        font-size: 16px;
        background: #55a2e5;
        text-transform: none !important;
        font-family: "Raleway", Helvetica, Arial, "Hiragino Kaku Gothic Pro", "ヒラギノ角ゴ Pro W3", メイリオ, Meiryo, "ＭＳ Ｐゴシック", sans-serif;
        font-weight: 400 !important;
        color: white;
        -webkit-border-radius: 1000px;
        border-radius: 1000px;
        padding: 0;
        text-align: left;
        margin-top: 0;
        margin-bottom: 0;
        display: inline-block;
        cursor: pointer;
        -webkit-transition: all 0.5s;
        -moz-transition: all 0.5s;
        -o-transition: all 0.5s;
        transition: all 0.5s;
    }

    .spike-icon {
        width: 46px;
        height: 45px;
        float: left;
        border-right: 1px solid rgba(255, 255, 255, 0.2);
        display: inline-block;
        position: relative;
        font-style: italic;
        line-height: inherit;
    }

    .spike-icon:before {
        width: 20px;
        height: 20px;
        margin-top: -11px;
        left: 15px;
        content: '';
        position: absolute;
        top: 50%;
        vertical-align: middle;
        background: url("https://dan2wmgtyui9q.cloudfront.net/assets/logo/spike-button-ec741167dcf76d0484cab7ed7301a799.svg") no-repeat;
    }

    #button-large-display-text {
        margin-left: 0px;
        margin-right: 14px;
        width: 128px;
        text-align: center;
        font-weight: normal;
        display: inline-block;
        text-align: center;
        line-height: inherit;
    }

</style>

<div id="undercolumn">
  <div id="undercolumn_shopping">

    <p class="flow_area"><img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_03.jpg" alt="購入手続きの流れ" /></p>
    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <form name="form1" id="cc_spike_checkout_924b54ffcd5affe53bb2970853cf2105616426f1" method="POST" action="<!--{$tpl_url|h}-->" autocomplete="off">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="charge" />
      <input type="hidden" name="token" id="token" value="" />
      <div style="text-align: center; margin: 100px 0;">
        <span class="spike-button" id="purchase"><i class="spike-icon"></i><b id="button-large-display-text">カードで支払う</b></span>
      </div>
    </form>

    <div class="btn_area">
      <ul style="text-align: center; display: inline-block;">
        <li><a href="./confirm.php"><img class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" name="back<!--{$key}-->" /></a></li>
      </ul>
    </div>

  </div>
</div>

<script src="<!--{$tpl_checkout_js_url|h}-->"></script>
<script type="text/javascript"><!--

    var handler = SpikeCheckout.configure({
        key: '<!--{$tpl_api_public_key}-->',
        token: function(token, args) {
            $('#purchase').attr('disabled', 'disabled');
            $(':input[type="hidden"][name="token"]').val(token.id);
            $('form#cc_spike_checkout_924b54ffcd5affe53bb2970853cf2105616426f1').submit();
        },
        closed: function() {
            if (! $(':input[type="hidden"][name="token"]').val()) {
                $('img[name="back"]').click();
            }
        }
    });

    $('#purchase').click(function(e) {
        handler.open({
            name: '<!--{$tpl_shop_name}-->',
            amount: <!--{$tpl_payment_total}-->,
            currency: '<!--{$tpl_currency}-->',
            email: '<!--{$tpl_order_email}-->',
            guest: <!--{$tpl_use_guest}-->
        });
        e.preventDefault();
    });

//--></script>
