{*
* Hook displayCustomerAccount - ajoute le lien "Mes biens" dans My Account
*}
<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="realestate-my-properties-link" href="{$link->getModuleLink('realestatemanager', 'myproperties')|escape:'html':'UTF-8'}">
    <span class="link-item">
        <i class="material-icons">&#xe88a;</i>
        Mes biens immobiliers
    </span>
</a>
