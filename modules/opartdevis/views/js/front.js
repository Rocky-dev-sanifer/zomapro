/**
 * Prestashop module : OpartDevis
 *
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

function opartDevisLoadCarrierList() {
    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'LoadCarrierList'}
    );

    $.ajax({
        type: 'POST',
        url: opart_ajaxUrl,
        data: $.param(data),
        cache: false,
        dataType: 'JSON',
        success: function(data){
            opartDevisPopulateSelectCarrier(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisPopulateSelectCarrier(data) {
    if (data['prefered_order']) {
        // get prefered carrier order
        var order = data['prefered_order'].split(',');

        var carrierSelect = $('#opart_devis_carrier_input');
        carrierSelect.html('');

        for (var k = 0; k < order.length; k++) {
            if ($('#selected_carrier').val() == order[k]) {
                var selected = 'selected';
            } else {
                 var selected = '';
            }

            carrierSelect.append('<option value="' + order[k] + '" ' + selected + '>' + data[order[k]]['name'] + ' - ' + data[order[k]]['price'] + ' (' + data[order[k]]['taxOrnot'] + ')</option>');
        }
    }

    opartDevisUpdateCarrier();
}

function opartDevisUpdateCarrier() {
    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'UpdateCarrier'}
    );

    $.ajax({
        type: 'POST',
        url: opart_ajaxUrl,
        data: $.param(data),
        dataType: 'JSON',
        success: function (data) {
            if (data) {
                $('#opartQuotationTotalQuotationWithTax').html(data.total_price);
                $('#opartQuotationTotalQuotation').html(data.total_price_without_tax);
                $('#opartQuotationTotalTax').html(data.total_tax);
                $('#opartQuotationTotalDiscounts').html(data.total_discounts);
                $('#opartQuotationTotalShippingWithoutTax').html(data.total_shipping_tax_exc);
                if (priceDisplay == 1) {
                    $('#opartQuotationTotalShipping').html(data.total_shipping_tax_exc);
                } else {
                    $('#opartQuotationTotalShipping').html(data.total_shipping);
                } 
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}
