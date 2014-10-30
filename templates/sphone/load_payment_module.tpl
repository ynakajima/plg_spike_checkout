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
<section id="undercolumn">

  <h2 class="title"><!--{$tpl_title|h}--></h2>

  <form name="form1" id="cc_spike_checkout_924b54ffcd5affe53bb2970853cf2105616426f1" method="POST" action="<!--{$tpl_url|h}-->" autocomplete="off">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="charge" />
    <input type="hidden" name="token" id="token" value="" />

    <div class="btn_area">
      <ul class="btn_btm">
         <li><a rel="external" href="#" class="btn" id="purchase">カードで支払う</a></li>
         <li><a rel="external" href="./confirm.php" class="btn_back" id="btn_back">戻る</a></li>
      </ul>
    </div>
  </form>

</section>

<script src="<!--{$tpl_checkout_js_url|h}-->"></script>
<script type="text/javascript"><!--

    var handler = SpikeCheckout.configure({
        key: '<!--{$tpl_api_public_key}-->',
        token: function(token, args) {
            $(':input[type="hidden"][name="token"]').val(token.id);
            $('form#cc_spike_checkout_924b54ffcd5affe53bb2970853cf2105616426f1').submit();
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
