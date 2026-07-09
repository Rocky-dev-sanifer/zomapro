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
	        {l s='FAQ' mod='opartdevis'}
	</div>
	<div>
		<div id="accordion">
			  <div class="card">
			    <div class="card-header" id="headingOne">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
			          {l s='How to limit the possibility of transforming a cart into a quote to certain categories of customers?' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>

			    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
			      <div class="card-body">
			         <p>{l s='To limit the use of the quote module to a certain group of customers, you must go to the “Prestashop settings -> customers -> groups” section.' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}faq1.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}faq2.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			         <p>{l s='Then click on the group you want to edit.' mod='opartdevis'}</p>
			         <p>{l s='For each customer group, you can activate or not the modules available for your store.' mod='opartdevis'}<br/>
			         {l s='Basically, enabled modules are available for all customer groups. Thus, if you want to limit the use of the quote module to certain groups of customers, you must modify the groups that will no longer have access and deactivate the module for this group.' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}faq3.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			      </div>
			    </div>
			  </div>

			  <div class="card">
			    <div class="card-header" id="headingTwo">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
    					{l s='The module does not appear on the customer account page' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>
			    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
			      <div class="card-body">
			        <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}faq4.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			        <p>{l s='For classic themes, there is no operation to do, the module is present as standard.' mod='opartdevis'}</p>
			        <p><strong>{l s='If you are using the WareHouse theme' mod='opartdevis'}</strong></p>
			        <p>{l s='Depending on the version of your theme, it is possible that the module is not present in the menu available in the customer account' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}faq5.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			        <p>{l s='If so, add the hook point to the theme. To do this, you must modify the “page.tpl” file following this path: themes/warehouse/templates/customer/page.tpl' mod='opartdevis'}</p>
			        <p>{l s='To make this addition, you must therefore have access to your FTP.' mod='opartdevis'}</p>
			        <p>{l s='In the “page.tpl” file; you need to add this line:' mod='opartdevis'}</p>
			         <p><img src="{$dirimg|escape:'htmlall':'UTF-8'}faq6.png" style="border: 1px solid #CCC; margin:10px 0px;" /></p>
			      </div>
			    </div>
			  </div>
			</div>

			<div class="card">
			    <div class="card-header" id="headingThree">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseOne">
			          {l s='How can the customer no longer be able to modify the quote?' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>

			    <div id="collapseThree" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
			      <div class="card-body">
			         <p>{l s='Your customers can modify the quotes only if they are not validated by you in the back office.' mod='opartdevis'}</p>
			         <p>{l s='As long as the quote is not validated, the module considers that customers can still modify their request and therefore make changes to the quote' mod='opartdevis'}</p>
			         <p>{l s='Once validated by you, the module considers that it is your offer, thus the validity date no longer changes and takes effect from the moment you validate it. The date, the prices as well as the content of the quote are then no longer modifiable' mod='opartdevis'}</p>
			      </div>
			    </div>
			  </div>

			<div class="card">
			    <div class="card-header" id="headingFour">
			      <h2 class="mb-0">
			        <button class="btn btn-link opartdevis-faq-title" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseOne">
			          {l s='How to modify the shipping costs manually without taking into account the pre-registered transport rules?' mod='opartdevis'}
			        </button>
			      </h2>
			    </div>

			    <div id="collapseFour" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
			      <div class="card-body">
			         <p>{l s='By default, the module uses pre-registered carriers and does not allow you to add shipping costs manually to the quote.' mod='opartdevis'}</p>
			         <p>{l s='However, if you wish, we can make a specific development on your module so that it authorizes the addition of shipping costs manually.' mod='opartdevis'}</p>
			         <p>{l s='Contact us for more informations. ' mod='opartdevis'}</p>
			      </div>
			    </div>
			  </div>  

	</div>
</div>