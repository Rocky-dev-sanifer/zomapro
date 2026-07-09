{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

<div class="panel">
    <div class="panel-heading">
        {l s='Help' mod='opartdevis'}
    </div>
    <fieldset>
        <strong>{l s='If you require assistance, you can' mod='opartdevis'}:</strong><br />
        <ul>
            <li>
                {l s='Read the documentation' mod='opartdevis'}
                <a href="{$module_dir|escape:'htmlall':'UTF-8'}/views/documentation/readme_fr.pdf" target="blank">
                    {l s='In french' mod='opartdevis'}
                </a>
                {l s='or' mod='opartdevis'}
                <a href="{$module_dir|escape:'htmlall':'UTF-8'}/views/documentation/readme_en.pdf" target="blank">
                    {l s='in english' mod='opartdevis'}
                </a>
            </li>
        </ul>
    </fieldset>

    <div class="panel-footer">
        {l s='This module was created by Olivier Clémence' mod='opartdevis'}
    </div>
</div>
