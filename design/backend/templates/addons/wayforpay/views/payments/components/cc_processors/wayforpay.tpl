{include file="common/subheader.tpl" title=__("wayforpay.settings.connection") target="#wayforpay_connection"}
<div id="wayforpay_connection">
    <div class="control-group">
        <label class="control-label cm-required" for="wayforpay_merchantAccount">{__("wayforpay.merchantAccount")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][merchantAccount]" id="wayforpay_merchantAccount" value="{$processor_params.merchantAccount}"  size="60">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label cm-required" for="wayforpay_merchantSecretKey">{__("wayforpay.merchantSecretKey")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][merchantSecretKey]" id="wayforpay_merchantSecretKey" value="{$processor_params.merchantSecretKey}"  size="60">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="wayforpay_mode">{__("wayforpay.test_live_mode")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][mode]" id="wayforpay_mode">
                <option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{__("wayforpay.test")}</option>
                <option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{__("wayforpay.live")}</option>
            </select>
        </div>
    </div>
</div>

{include file="common/subheader.tpl" title=__("wayforpay.settings.transaction") target="#wayforpay_transaction"}
<div id="wayforpay_transaction">
    <div class="control-group">
        <label class="control-label cm-required" for="wayforpay_currency">{__("wayforpay.currency")}:</label>
        <div class="controls">
            {assign var="currencies" value=""|fn_wayforpay_get_currencies}
            <select name="payment_data[processor_params][currency]" id="wayforpay_currency">
                {foreach from=$currencies item="c" key="k"}
                    <option value="{$k}"{if $processor_params.currency == $k} selected="selected"{/if}>{$c.description}</option>
                {/foreach}
            </select>
        </div>
    </div>


    <div class="control-group">
        <label class="control-label cm-required" for="wayforpay_orderTimeout">{__("wayforpay.orderTimeout")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][orderTimeout]" id="wayforpay_orderTimeout" value="{$processor_params.orderTimeout}"  size="10">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label cm-required" for="wayforpay_orderLifetime">{__("wayforpay.orderLifetime")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][orderLifetime]" id="wayforpay_orderLifetime" value="{$processor_params.orderLifetime}"  size="10">
        </div>
    </div>
</div>

{include file="common/subheader.tpl" title=__("wayforpay.settings.order_statuses") target="#wayforpay_order_statuses"}
<div id="wayforpay_order_statuses">
    {assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}
    <div class="control-group">
        <label class="control-label" for="wayforpay_order_Created">{__("wayforpay.order_status.Created")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][order_status][Created]" id="wayforpay_order_Created">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}"{if $processor_params.order_status.Created == $k || !$processor_params.order_status.Created && $k == 'B'} selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="wayforpay_order_InProcessing">{__("wayforpay.order_status.InProcessing")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][order_status][InProcessing]" id="wayforpay_order_InProcessing">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}"{if $processor_params.order_status.InProcessing == $k || !$processor_params.order_status.InProcessing && $k == 'O'} selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="wayforpay_order_Approved">{__("wayforpay.order_status.Approved")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][order_status][Approved]" id="wayforpay_order_Approved">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}"{if $processor_params.order_status.Approved == $k || !$processor_params.order_status.Approved && $k == 'P'} selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="wayforpay_order_Pending">{__("wayforpay.order_status.Pending")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][order_status][Pending]" id="wayforpay_order_Pending">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}"{if $processor_params.order_status.Pending == $k || !$processor_params.order_status.Pending && $k == 'O'} selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="wayforpay_order_Expired">{__("wayforpay.order_status.Expired")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][order_status][Expired]" id="wayforpay_order_Expired">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}"{if $processor_params.order_status.Expired == $k || !$processor_params.order_status.Expired && $k == 'F'} selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="wayforpay_order_RefundedVoided">{__("wayforpay.order_status.RefundedVoided")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][order_status][RefundedVoided]" id="wayforpay_order_RefundedVoided">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}"{if $processor_params.order_status.RefundedVoided == $k || !$processor_params.order_status.RefundedVoided && $k == 'E'} selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="wayforpay_order_Declined">{__("wayforpay.order_status.Declined")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][order_status][Declined]" id="wayforpay_order_Declined">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}"{if $processor_params.order_status.Declined == $k || !$processor_params.order_status.Declined && $k == 'D'} selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="wayforpay_order_RefundInProcessing">{__("wayforpay.order_status.RefundInProcessing")}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][order_status][RefundInProcessing]" id="wayforpay_order_RefundInProcessing">
                {foreach from=$statuses item="s" key="k"}
                    <option value="{$k}"{if $processor_params.order_status.RefundInProcessing == $k || !$processor_params.order_status.RefundInProcessing && $k == 'E'} selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>