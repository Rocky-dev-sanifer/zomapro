/**
 * @category Prestashop
 * @category Module
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

$(document).ready(function() {
    opartDevisLoadCarrierList();

    $('#opart_devis_carrier_input').change(function() {
        opartDevisUpdateCarrier()
    });
});
