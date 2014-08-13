<!--{*
 * SPIKE Checkoutプラグイン設定ページテンプレート
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

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">//<![CDATA[
self.moveTo(20,20);
self.resizeTo(620, 650);
self.focus();
//]]>
</script>
<h2><!--{$tpl_subtitle}--></h2>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit">
<p class="remark">
SPIKE Checkout をご利用頂くためには、<a href="https://spike.cc" target="_blank">SPIKE</a>での登録が必要です。
</p>

<!--{if $arrErr.err != ""}-->
<div class="attention"><!--{$arrErr.err}--></div>
<!--{/if}-->

<table class="form">
  <colgroup width="20%"></colgroup>
  <colgroup width="30%"></colgroup>

  <tr>
    <th colspan="2">▼設定</th>
  </tr>
  <tr>
    <th>秘密鍵<span class="attention">※</span></th>
    <td>
      <!--{assign var=key value="api_secret_key"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box60" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->">
    </td>
  </tr>
  <tr>
    <th>公開鍵<span class="attention">※</span></th>
    <td>
      <!--{assign var=key value="api_public_key"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text" name="<!--{$key}-->" style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value}-->" class="box60" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->">
    </td>
  </tr>
  <tr>
    <th>ゲスト決済</th>
      <td>
        <!--{assign var=key value="guest_checkout"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <input type="checkbox" name="<!--{$key}-->" value="1" id="<!--{$key}-->" <!--{if $arrForm[$key].value == 1}-->checked="checked"<!--{/if}--> />
        <p>※ ゲスト決済を利用する場合はチェックを入れて下さい</p>
      </td>
    </tr>
  <tr>
</table>
<div class="btn-area">
  <ul>
    <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
  </ul>
</div>
</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
