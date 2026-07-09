<div class="card customer-quotations-card mt-1" style="width:-webkit-fill-available;margin:0px 15px">
  <h3 class="card-header">
    <i class="material-icons">request_quote</i>
    {l s='Quotations' mod='opartdevis'}
  </h3>
  <div class="card-body">
          <table class="table">
          	<thead>
          		<tr>
          			<td> {l s='ID' mod='opartdevis'}</td>
          			<td> {l s='Name' mod='opartdevis'}</td>
          			<td> {l s='date' mod='opartdevis'}</td>
          			<td> {l s='total' mod='opartdevis'}</td>
          			<td> {l s='status' mod='opartdevis'}</td>
          			<td> {l s='View' mod='opartdevis'}</td>
          			<td> {l s='Modify' mod='opartdevis'}</td>
          		</tr>
          	</thead>
          	<tbody>
          			{foreach from=$quotations item=quotation}
          			<tr>
	          			<td>{$quotation->id_opartdevis|intval}</td>
	          			<td>{$quotation->name|escape:'htmlall':'UTF-8'}</td>
	          			<td>{$quotation->date_add|escape:'htmlall':'UTF-8'}</td>
	          			<td>{Cart::getTotalCart($quotation->id_cart|intval, false, Cart::BOTH_WITHOUT_SHIPPING)}</td>
	          			<td>{$quotation->getStatusName($quotation->status)|escape:'htmlall':'UTF-8'}</td>
	          			<td><a class="btn btn-primary" href="{$link->getModuleLink('opartdevis', 'showquotation', ['id_cart' => $quotation->id_cart,'document'=>'quotation'])|escape:'htmlall':'UTF-8'}">{l s='download' mod='opartdevis'}</a></td>
	          			<td>
						  <a class="btn btn-default"
						     href="{$adminOpartdevisLink|escape:'htmlall':'UTF-8'}&updateopartdevis&id_opartdevis={$quotation->id_opartdevis|intval}">
						    {l s='Edit' mod='opartdevis'}
						  </a>
						</td>
						</tr>

          			{/foreach}
          	</tbody>
      </div>
</div>
