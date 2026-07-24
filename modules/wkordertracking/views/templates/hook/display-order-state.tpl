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

{if isset($activeTrackingStates)}
    <section class="box wk-state-box">
        <h3>{l s='Follow your order' mod='wkordertracking'}</h3>
        {if Configuration::get('WK_ORDER_TRACKING_TEMPLATE') == 1}
            <div class="wk-state-template-one">
                {*Display bar in Template One (Desktop and Mobile*}
                {foreach $activeTrackingStates as $stateKey => $states}
                    <div class="wk-state-container">
                        <div class="wk-state-circle {if isset($states['achieved'])}wk-state-circle-active{/if}">
                            <span class="wk-state-icon" style="{if isset($states['achieved'])}visibility:visibile;{else}visibility:hidden;{/if}">
                                <i class="material-icons">check</i>
                            </span>
                            <div class="wk-state-label">
                                {if isset($states['achieved'])}
                                    {assign var="image_exist" value="{$smarty.const._PS_MODULE_DIR_}/wkordertracking/views/img/achieved_icon/{$states.id_state}.jpg"}
                                    {if file_exists($image_exist)}
                                        {assign var="image_path" value="{$smarty.const._MODULE_DIR_}/wkordertracking/views/img/achieved_icon/{$states.id_state}.jpg"}
                                    {else}
                                        {assign var="image_path" value="{$smarty.const._MODULE_DIR_}/wkordertracking/views/img/default_achieved.png"}
                                    {/if}
                                {else}
                                    {assign var="image_exist" value="{$smarty.const._PS_MODULE_DIR_}/wkordertracking/views/img/pending_icon/{$states.id_state}.jpg"}
                                    {if file_exists($image_exist)}
                                        {assign var="image_path" value="{$smarty.const._MODULE_DIR_}/wkordertracking/views/img/pending_icon/{$states.id_state}.jpg"}
                                    {else}
                                        {assign var="image_path" value="{$smarty.const._MODULE_DIR_}/wkordertracking/views/img/default_pending.png"}
                                    {/if}
                                {/if}
                                <img width="60" src="{$image_path}" class="img-responsive">
                                <div class="wk-state-title"><span title="{$states.state_name}">{$states.state_name}</span></div>
                                {if isset($states['achieved']) && $states.description != ''}
                                    <span class="wk_tooltiptext">{$states.description}</span>
                                {/if}
                            </div>
                        </div>
                    </div>
                    {if ($stateKey + 1) < count($activeTrackingStates)}
                        <div class="wk-state-bar {if isset($states['achieved'])}wk-state-bar-active{/if}"></div>
                    {/if}
                {/foreach}
                <div class="clearfix"></div>
            </div>
        {else if Configuration::get('WK_ORDER_TRACKING_TEMPLATE') == 2}
            <div class="wk-state-template-two">
                {*Display bar in Template two (Desktop and Mobile*}
                {foreach $activeTrackingStates as $stateKey => $states}
                    <div class="wk-state-container">
                        <div class="wk-state-circle {if isset($states['achieved'])}wk-state-circle-active{/if}">
                            <div class="wk-state-label">
                                {if isset($states['achieved'])}
                                    <span class="wk-state-icon"><i class="material-icons">check</i></span>
                                {else}
                                    {assign var="image_exist" value="{$smarty.const._PS_MODULE_DIR_}/wkordertracking/views/img/pending_icon/{$states.id_state}.jpg"}
                                    {if file_exists($image_exist)}
                                        {assign var="image_path" value="{$smarty.const._MODULE_DIR_}/wkordertracking/views/img/pending_icon/{$states.id_state}.jpg"}
                                    {else}
                                        {assign var="image_path" value="{$smarty.const._MODULE_DIR_}/wkordertracking/views/img/default_pending.png"}
                                    {/if}
                                    <img width="50" src="{$image_path}" class="img-responsive">
                                {/if}
                                <div class="wk-state-title"><span title="{$states.state_name}">{$states.state_name}</span></div>
                                {if isset($states['achieved']) && $states.description != ''}
                                    <span class="wk_tooltiptext">{$states.description}</span>
                                {/if}
                            </div>
                        </div>
                    </div>
                    {if ($stateKey + 1) < count($activeTrackingStates)}
                        <div class="wk-state-bar {if isset($states['achieved'])}wk-state-bar-active{/if}"></div>
                    {/if}
                {/foreach}
                <div class="clearfix"></div>
            </div>
        {/if}
    </section>
{/if}