{**
 * ZomaPro - Override affichage du suivi de commande (wkordertracking) : étapes verticales.
 *}
{if isset($activeTrackingStates)}
  <div class="zmot">
    <h3 class="zmot-title">{l s='Suivi de la commande' mod='wkordertracking'}</h3>
    <ul class="zmot-steps">
      {foreach $activeTrackingStates as $k => $s}
        <li class="zmot-step{if isset($s.achieved)} done{/if}{if $s.id_state == $orderCurrentStateId} current{/if}">
          <span class="zmot-dot">
            {if isset($s.achieved) && $s.id_state != $orderCurrentStateId}
              <i class="material-icons">check</i>
            {else}
              {$k + 1}
            {/if}
          </span>
          <span class="zmot-label">{$s.state_name}</span>
        </li>
      {/foreach}
    </ul>
  </div>
{/if}
