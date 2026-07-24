{**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{if $PS_VERSION > '1.7.7'}
    <div class="card d-print-none">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-header-title">
                        <i class="material-icons">local_shipping</i>
                        {l s='Tracking state' mod='wkordertracking'}
                    </h3>
                </div>
            </div>
        </div>
        <div class="card-body">
{else}
<div class="panel">
	<div class="panel-heading">
        <i class="material-icons">local_shipping</i>
        {l s='Tracking state' mod='wkordertracking'}
	</div>
{/if}
    <form class="form-horizontal" name="wk-order-state-form" action="{$link->getAdminLink('AdminWkTrackingState')}" method="post">
        <input type="hidden" name="id_order" value="{$idOrder}">
        <div class="row">
            <label class="control-label col-lg-2">
                {l s='Choose your order state' mod='wkordertracking'}
            </label>
            <div class="col-lg-4">
                {if $activeTrackingStates}
                    <select name="id_state" class='form-control'>
                        {foreach $activeTrackingStates as $trackingState}
                            <option value="{$trackingState['id_state']}" {if isset($currentState) && $currentState == $trackingState['id_state']}selected="selected"{/if}>
                                {$trackingState['state_name']}
                            </option>
                        {/foreach}
                    </select>
                {/if}
            </div>
            <div class="col-lg-2">
                <button type="submit" name="changeOrderState" class="btn btn-primary">
                    {l s='Update state' mod='wkordertracking'}
                </button>
            </div>
        </div>
    </form>
    {if $PS_VERSION > '1.7.7'}
    </div>
</div>
{else}
</div>
{/if}