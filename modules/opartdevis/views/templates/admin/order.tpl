{if $products}
	<div class="card">
	<div class="card">
	  <div class="card-header" style="background-color:orange;">
	    <div class="row">
	      <div class="col-md-12">
	        <h3 class="card-header-title" style="color:#FFF;">
	          <i class="material-icons" style="color:#FFF;">comment</i> {l s='Product comments on the quote : ' mod='opartdevis'}
	        </h3>
	      </div>
	     </div>
	  </div>
	  <div class="card-body">
	    <h5 class="card-title"></h5>

	    <p class="card-text">
	    	{foreach from=$products item=product}
				<ul>
				    {if $product.commentaire}
				        <li>{$product.product_name|escape:'htmlall':'UTF-8'} : {$product.commentaire nofilter}</li>
				    {/if}
				</ul>
			{/foreach}
	    </p>
	    		
	  </div>
	</div>
	
	</div>
{/if}