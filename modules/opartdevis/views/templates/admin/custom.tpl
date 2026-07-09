{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}


<div class="alert alert-info">
	{l s='Do you need to adapt the module to your specific needs? contact us' mod='opartdevis'}
</div>
<div class="panel">
	<div class="panel-heading">
	        {l s='Custom development' mod='opartdevis'}
	</div>
	<div>
		<p> {l s='At the customer\'s request, we regularly customize the quote module.' mod='opartdevis'}<br/>
			{l s='If you wish to adapt the module to your specific needs, do not hesitate to contact us.' mod='opartdevis'}
		</p>
		<p> {l s='Below you will find some examples of customization or additional features that we have already been able to create on request.' mod='opartdevis'}</p>
		<div id="accordion">
			  <div class="card">
			    <div class="card-header" id="headingOne">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
			          {l s='View products contained in a product pack' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>

			    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
			      <div class="card-body">
			         <p>{l s='When one adds a product pack in a quote, in the pdf we find the details of the products contained in the pack.' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}pack-produit.jpg" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			      </div>
			    </div>
			  </div>

			  <div class="card">
			    <div class="card-header" id="headingTwo">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
    					{l s='Product only on quote' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>
			    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
			      <div class="card-body">
			        <p>{l s='In the product sheet, addition of a checkbox to indicate that a product is only available on quote.' mod='opartdevis'}</p>
			        <p>{l s='On the front office, replace the add to cart button with request a quote + hide the price' mod='opartdevis'}</p>
			        <p>{l s='In the cart, we hide the order button if the cart contains a product only available on quote' mod='opartdevis'}</p>
			      </div>
			    </div>
			  </div>
			</div>

			<div class="card">
			    <div class="card-header" id="headingThree">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseOne">
			          {l s='Add new fields' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>

			    <div id="collapseThree" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
			      <div class="card-body">
			         <p>{l s='We can add fields according to your needs.' mod='opartdevis'}</p>
			         <p>{l s='In this example, we have added a payment method and delivery time field in the administration' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}new-field.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			         <p>{l s='These fields can then be visible or not on the quote in pdf' mod='opartdevis'}</p>
			      </div>
			    </div>
			  </div>

			<div class="card">
			    <div class="card-header" id="headingFour">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseOne">
			          {l s='Assign quotes to sales reps' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>

			    <div id="collapseFour" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
			      <div class="card-body">
			         <p>{l s='We can allow you to assign quotes to your sales representatives' mod='opartdevis'}</p>
			         <p>{l s='In the module configuration, you must specify the profile of your sales representatives' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}commerciaux.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			         <p>{l s='In the listing of quotes, you have a menu with your different sales representatives as well as unassigned quotes' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}menu-commercial.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			         <p>{l s='Sales reps only have access to their tab' mod='opartdevis'}</p>
			         <p>{l s='When there is a new quote, you can assign it to a sales representative' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}commercial-devis.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			         <p>{l s='You can also assign a quote to a salesperson from the listing' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}commercial-devis-listing.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}commercial-devis-listing-2.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			      </div>
			    </div>
			</div> 

			<div class="card">
			    <div class="card-header" id="headingcinq">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title" data-toggle="collapse" data-target="#collapsecinq" aria-expanded="false" aria-controls="collapseOne">
			          {l s='Download quote in excel' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>

			    <div id="collapsecinq" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
			      <div class="card-body">
			         <p>{l s='You can download the quote in excel format' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}download-excel.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}excel.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			      </div>
			    </div>
			</div>  


			<div class="card">
			    <div class="card-header" id="headingsix">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title" data-toggle="collapse" data-target="#collapsesix" aria-expanded="false" aria-controls="collapseOne">
			          {l s='Merge Quotes' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>

			    <div id="collapsesix" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
			      <div class="card-body">
			         <p>{l s='You have several quotes for the same customer and you want to merge them?' mod='opartdevis'}</p>
			         <p>{l s='In the listing you can select the quotes to merge' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}merge-quote.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			      </div>
			    </div>
			</div> 


			<div class="card">
			    <div class="card-header" id="headingseven">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseseven">
    					{l s='Save quote requests in the back office from the simple form' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>
			    <div id="collapseseven" class="collapse" aria-labelledby="headingseven" data-parent="#accordion">
			      <div class="card-body">
			        <p>{l s='When requesting a quote from the simple form, automatic creation of the customer, address and retrieval of the basket to create the quote in the back office' mod='opartdevis'}</p>
			      </div>
			    </div>
			  </div>
			</div>

	</div>
</div>