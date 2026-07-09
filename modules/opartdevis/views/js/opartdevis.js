/**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

$(document).ready(function () {
    // Update "Order" button on payment page
    var payment_button = $("#payment-confirmation button").html();

    $("#checkout-payment-step input[type='radio'] ").click(function (e) {
        var module_name = this.dataset.moduleName;
        
        setTimeout(function() {
            if (module_name == "opartdevis"){
                $("#payment-confirmation button").html(order_button_content);
            } else {
                $("#payment-confirmation button").html(payment_button);
            }
        }, 100);
    });

});
