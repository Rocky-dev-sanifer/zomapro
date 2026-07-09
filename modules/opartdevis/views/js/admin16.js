/**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

$(document).ready(function(){

     $('#loader').fadeOut("slow"); 

        //afficher ou non les frais de port manuel
    if ($('#port_manuel').is(':checked')) {
          $('#box-port-manuel').show();
        } else {
          $('#box-port-manuel').hide();
        }


  



    // Customer Auto-complete
    $('#opart_devis_customer_autocomplete_input').autocomplete(
        ajaxUrl,
        {
            minChars: 1,
            autoFill: true,
            max: 200,
            matchContains: true,
            scroll: false,
            dataType: 'JSON',
            cacheLength: 0,
            extraParams: {
                ajax: true,
                action: 'SearchCustomer',
                token: token
            },
            parse: function(customers) {
                var formated_customers = new Array();

                for (var i = 0; i < customers.length; i++) {
                    formated_customers[i] = {
                        data: customers[i],
                        value: (
                            customers[i].id_customer
                            + ' - ' + customers[i].lastname
                            + ' - ' + customers[i].firstname
                            + ' - ' + customers[i].email
                        ).trim()
                    };
                }

                return formated_customers;
            },
            formatItem: function(data, i, max, value, term) {
                return value;
            }
        }
    ).result(function(e, customer){
        if (customer != undefined) {
            opartDevisAddCustomerToQuotation(
                customer['id_customer'],
                customer['lastname'],
                customer['firstname'],
                customer['email']
            );
        }

        $(this).val('');
    });

    // Product Auto-complete
    $('#opart_devis_product_autocomplete_input').autocomplete(
        ajaxUrl,
        {
            minChars: 3,
            delay: 400,
            autoFill: true,
            max: 200,
            matchContains: true,
            scroll: false,
            dataType: 'JSON',
            cacheLength: 0,
            extraParams: {
                ajax: true,
                action: 'SearchProduct',
                token: token,
                id_customer: function () {
                    return $('#opart_devis_customer_id').val()
                }
            },
            parse: function(products) {
                var formated_products = new Array();

                for (var i = 0; i < products.length; i++) {
                    formated_products[i] = {
                        data: products[i],
                        value: (
                            products[i].id_product
                            + ' - ' + products[i].name
                            + ' - ' + products[i].price
                            + ' - ' + products[i].reduced_price
                        ).trim()
                    };
                }

                return formated_products;
            },
            formatItem: function(data, i, max, value, term) {
                return value;
            }
        }
    ).result(function(e, product){
        if (product != undefined) {
            opartDevisAddProductToQuotation(
                product.id_product,
                product.name,
                product.stock_available,
                product.price,
                1,
                0,
                product.reduced_price,
                null,
                null,
                null,
                product.reduced_price,
            );
        }

        $(this).val('');
    });

           // discount Auto-complete
    $('#opart_devis_discount_autocomplete_input').autocomplete(
        ajaxUrl,
        {
            minChars: 1,
            autoFill: true,
            max: 200,
            matchContains: true,
            scroll: false,
            dataType: 'JSON',
            cacheLength: 0,
            extraParams: {
                ajax: true,
                action: 'SearchDiscount',
                token: token,
            },
            parse: function(rules) {
                var formated_discounts = new Array();

                for (var i = 0; i < rules.length; i++) {
                    formated_discounts[i] = {
                        data: rules[i],
                        value: (
                            rules[i].name
                            + ' - ' + rules[i].code
                        ).trim()
                    };
                }

                return formated_discounts;
            },
            formatItem: function(data, i, max, value, term) {
                return value;
            }
        }
    ).result(function(e, rule){
        if (rule != undefined) {
            opartDevisAddDiscountToQuotation(
                rule.id_cart_rule,
                rule.code,
                rule.description,
                rule.name,
            );
        }

        $(this).val('');
    });


    $('#opart_devis_refresh_carrier_list').click(function(e) {
        e.preventDefault();

        opartDevisLoadCarrierList();
    });

          $('#port_manuel').click(function(e) {
        if ($('#port_manuel').is(':checked')) {
          $('#box-port-manuel').show();
            } else {
              $('#box-port-manuel').hide();
                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: {
                        ajax: true,
                        action: 'CarrierPrice',
                        token: token,
                        price: 0,
                        id_opartdevis: $('input[name=id_opartdevis]').val(),
                        port_manuel: 0
                    },
                    cache: false,
                    dataType: 'JSON',
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            }

            //disable button
           // $('#opartBtnSubmit').prop('disabled', true);
    });


    $('#opart_devis_refresh_total_quotation').click(function(e) {
        e.preventDefault();

        if ($(this).hasClass('disabled')) {
            return false;
        }

        $(this).addClass('disabled');

        opartDevisGetTotalCart();
    });

    $('#opart_devis_select_cart_rules').change(function(e) {
        e.preventDefault();

        $('#opartDevisCartRulesMsgError').hide('fast');

        if ($(this).val() == "-1") {
            return false;
        }

        if ($('#trCartRule_'+$(this).val()).length > 0) {
            $('#opartDevisCartRulesMsgError').html('This rule is already in cart');
            $('#opartDevisCartRulesMsgError').show('fast');

            return false;
        }

        var data = $('#opartDevisForm').serializeArray();

        data.push(
            {name: 'ajax', value: true},
            {name: 'action', value: 'AddCartRule'},
            {name: 'token', value: token},
            {name: 'id_cart_rule', value: $(this).val()}
        );

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            dataType: 'JSON',
            data: $.param(data),
            success: function(data) {
                if (!data.id) {
                    $('#opartDevisCartRulesMsgError').html(data);
                    $('#opartDevisCartRulesMsgError').show('fast');
                } else {
                    opartDevisAddRuleToQuotation(
                        data.id,
                        data.name[id_lang_default],
                        data.description,
                        data.code,
                        data.free_shipping,
                        data.reduction_percent,
                        data.reduction_amount,
                        '0',
                        data.gift_product
                    );
                }
            }, error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });

        //opartDevisLoadCarrierList();
    });

    $('.delete_attachement').on('click', function(e) {
        e.preventDefault();

        opartDevisDeleteUploadedFile(this);
    });

        $('#opart_devis_carrier_price').on('input', function(){
        var price = $(this).val().replace(',', '.');
        if(parseFloat(price)>=0){
            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: {
                    ajax: true,
                    action: 'CarrierPrice',
                    token: token,
                    price: price,
                    id_opartdevis: $('input[name=id_opartdevis]').val(),
                    port_manuel: 1
                },
                cache: false,
                dataType: 'JSON',
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    });

});

function opartDevisAddDiscountToQuotation(id_cart_rule,code,description,name){


        $('#opartDevisCartRulesMsgError').hide('fast');

        if (id_cart_rule == "-1") {
            return false;
        }

        if ($('#trCartRule_'+id_cart_rule).length > 0) {
            $('#opartDevisCartRulesMsgError').html('This rule is already in cart');
            $('#opartDevisCartRulesMsgError').show('fast');

            return false;
        }

        var data = $('#opartDevisForm').serializeArray();

        data.push(
            {name: 'ajax', value: true},
            {name: 'action', value: 'AddCartRule'},
            {name: 'token', value: token},
            {name: 'id_cart_rule', value: id_cart_rule}
        );

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            dataType: 'JSON',
            data: $.param(data),
            success: function(data) {
                if (!data.id) {
                    $('#opartDevisCartRulesMsgError').html(data);
                    $('#opartDevisCartRulesMsgError').show('fast');
                } else {
                    opartDevisAddRuleToQuotation(
                        data.id,
                        data.name[id_lang_default],
                        data.description,
                        data.code,
                        data.free_shipping,
                        data.reduction_percent,
                        data.reduction_amount,
                        '0',
                        data.gift_product
                    );
                }
            }, error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
}



function opartDevisAddProductToQuotation(prodId, prodName, stockAvailable, prodPrice, qty, idAttribute, specificPrice, yourPrice, DiscountPrice, customization_datas_json, total, commentaire, copy = false) {
    opartDevisToggleSubmitBtn(0);

    var id_attribute = (idAttribute == null) ? idAttribute : null;
    var specificPrice = (specificPrice != undefined) ? specificPrice : '';
    var specificQty = (specificQty != undefined) ? specificQty : '';
    var yourPrice = (yourPrice != undefined) ? yourPrice : '';
    var DiscountPrice = (DiscountPrice != undefined) ? DiscountPrice : '';
    var commentaire = (commentaire != undefined) ? commentaire : '';

    randomId = new Date().getTime();

    var customization_datas = $.parseJSON(customization_datas_json);
    var displayedCustomizationDatas = '';
    var qtyInputType = 'text';
    var onChangeCustomizationPrice = '';
    var customPriceClass = ''

    if (customization_datas && customization_datas.length) {
        for (var i = 0; i < customization_datas.length; i++){
            displayedCustomizationDatas += '<tr class="trAdminCustomizationData"><td colspan="7" class="tdAdminCustomizationDataValue">';

            var customization_datas_array = customization_datas[i]['datas'][1];

            for (var j = 0; j < customization_datas_array.length; j++){
                var addBr = (j > 0) ? '<br />' : '';
                displayedCustomizationDatas += addBr + customization_datas_array[j]['name'] + ' : ' + customization_datas_array[j]['value'];
            }

            displayedCustomizationDatas += '<td class="tdAdminCustomizationDataQty"><input type="text" value="' + customization_datas[i]['quantity'] + '" name="add_customization[' + randomId + '][' + customization_datas[i]['datas']['1']['0']['id_customization'] + '][newQty]" /></td></td><td></td></tr>';
        }

        qtyInputType = 'hidden';
        customPriceClass = 'customprice_' + prodId + '_' + idAttribute;
        onChangeCustomizationPrice = 'onchange="opartDevisAutoChangePrice(this,\'' + customPriceClass + '\')"';
    }

    var newTr = '<div id="trProd_' + randomId + '" style="display:none;" class="lign-produit row draggable">';
    newTr += '<div class="idproduit col-md-1" id="tdIdprod_' + randomId + '">' + prodId + '<input type="hidden" name="whoIs[' + randomId + ']" value="' + prodId + '" id="whoIs_' + randomId + '"/></div>';
    newTr += '<div class="col-md-1">' + prodName + '</div>';
    newTr += '<div class="col-md-2" id="declinaisonsProd_' + randomId + '"></div>';
    newTr += '<div class="col-md-1 text-center" id="stockAvailable_' + randomId + '">' + stockAvailable + '</div>';
    newTr += '<div class="prodPrice col-md-1 text-center" id="prodPrice_' + randomId + '">' + prodPrice + '</div>';
    newTr += '<div class="col-md-1"><input type="text" name="specific_discount" id="specificDiscount_' + randomId + '" class="calcTotalOnChangeDiscount" value="'+ DiscountPrice + '" data-price="'+ prodPrice +'" /></div>';
    newTr += '<div class="col-md-1"><input ' + onChangeCustomizationPrice + ' name="specific_price[' + randomId + ']" id="specificPriceInput_' + randomId + '" type="text" value="' + yourPrice + '" class="calcTotalOnChange ' + customPriceClass + '"/></div>';
    newTr += '<div class="prodPrice text-center col-md-1" id="prodReducedPrice_' + randomId + '">' + specificPrice + '</div>';

    newTr += '<div class="productPrice col-md-1 toto">';
    newTr += '<input id="inputQty_' + randomId + '" type="' + qtyInputType + '" value="' + qty + '" name="add_prod[' + randomId + ']" class="opartDevisAddProdInput calcTotalOnChange"/>';

    if (customization_datas && customization_datas.length) {
        newTr += '<span></span>';
    }

    newTr += '</div>';

    newTr += '<div class="prodPrice col-md-1 text-center" id="prodTotal_' + randomId + '">' + total + '</div>';

    newTr += '<div>';
    if (customization_datas == null || customization_datas == '') {
        newTr += '<a href="#" onclick="opartDevisDeleteProd(\'' + randomId + '\'); return false;"><i class="icon-trash"></i></a> <a href="#" onclick="event.preventDefault();opartDevisAddProductToQuotation(' + prodId +',\''+ prodName+'\','+ stockAvailable+', '+prodPrice+', '+qty+', '+idAttribute+', '+specificPrice+', null, null, '+total + ',null,true);"><i class="icon-copy"></i></a><i class="icon-move"></i>';
    }
    newTr += '</div>';
    newTr += '<div id="commentaireProd_' + randomId + '" class="commentaire">';
    newTr += '<div>["Facultatif"] Commentaire pour le produit : '+ prodName +' : <textarea name="commentaire[' + randomId + ']">'+commentaire+'</textarea>';
    newTr += '</div>';
    newTr += '</div>';
    newTr += '</div>';

    newTr += displayedCustomizationDatas;

    $('#opartDevisProdList').append(newTr);
    $('#trProd_'+randomId).show('fast');

    if(copy == true){
         var position = $('#trProd_'+randomId).offset().top - 150;

      $('html, body').animate({
        scrollTop: position
      }, 1000);
    }

    // Ajouter les gestionnaires d'événements de drag and drop ici
    $('#trProd_'+randomId).find('.icon-move').on('mousedown', function(event) {
        const draggableElement = $(this).closest('.draggable')[0];
        if (draggableElement) {
            draggableElement.setAttribute('draggable', true);
        }
    });

    $('#trProd_'+randomId).find('.icon-move').on('mouseup', function(event) {
        const draggableElement = $(this).closest('.draggable')[0];
        if (draggableElement) {
            draggableElement.removeAttribute('draggable');
        }
    });

    $('#trProd_'+randomId).on('dragstart', function(event) {
        const draggedElement = event.target;
        if (draggedElement) {
            event.originalEvent.dataTransfer.setData('text/plain', draggedElement.id);
            draggedElement.classList.add('dragging');
        }
    });

    $('#trProd_'+randomId).on('dragover', function(event) {
        event.preventDefault();
    });

    $('#trProd_'+randomId).on('dragenter', function(event) {
        const targetElement = $(event.target).closest('.draggable')[0];
        if (targetElement) {
            targetElement.classList.add('hovered');
        }
    });

    $('#trProd_'+randomId).on('dragleave', function(event) {
        const targetElement = $(event.target).closest('.draggable')[0];
        if (targetElement) {
            targetElement.classList.remove('hovered');
        }
    });

    $('#trProd_'+randomId).on('drop', function(event) {
        event.preventDefault();
        const draggedElement = document.querySelector('.dragging');
        const targetElement = $(event.target).closest('.draggable')[0];
        if (draggedElement && targetElement) {
            draggedElement.classList.remove('dragging');
            targetElement.classList.remove('hovered');
            const draggedPosition = Array.from(targetElement.parentElement.children).indexOf(draggedElement);
            const targetPosition = Array.from(targetElement.parentElement.children).indexOf(targetElement);
            if (draggedPosition < targetPosition) {
                targetElement.parentElement.insertBefore(draggedElement, targetElement.nextSibling);
            } else {
                targetElement.parentElement.insertBefore(draggedElement, targetElement);
            }
        }
    });

    opartDevisLoadProductCombinations(randomId, idAttribute);
    OpartPriceDiscountChange();
    opartBindOnChange();
}



function OpartPriceDiscountChange(prodPrice) {
    $('.calcTotalOnChangeDiscount').unbind( "change" );
     $('.calcTotalOnChangeDiscount').change(function() {
        var randomId = $(this).attr('id').substring($(this).attr('id').lastIndexOf('_')+1);
        var article = document.getElementById('specificDiscount_' + randomId);
        var prodPrice = article.dataset.price // "3"
        console.log(prodPrice);
        var pourcentage = $('#specificDiscount_' + randomId).val();
        var coeff = (100 - pourcentage) / 100;
        var remise = prodPrice * coeff;
        document.getElementById('specificPriceInput_' + randomId).value = remise.toFixed(2);
          if ($('#specificDiscount_' + randomId).val()=='') {
            if ($('#last_selected_attribute_'+randomId).length) {
                var id_attribute = $('#last_selected_attribute_'+randomId).val();
            } else {
                var id_attribute = 0;
            }

            opartDevisDeleteSpecificPrice();

            var current_id_attribute = $('#select_attribute_' + randomId).val()

            $('#last_selected_attribute_' + randomId).val(current_id_attribute);
        }

        opartDevisGetReducedPrices();

    });


    $('.calcTotalOnChangeDec').unbind( "change" );
    $('.calcTotalOnChangeDec').change(function() {
        var randomId = $(this).attr('id').substring($(this).attr('id').lastIndexOf('_')+1);

        if ($('#specificPriceInput_' + randomId).val()=='') {
            if ($('#last_selected_attribute_'+randomId).length) {
                var id_attribute = $('#last_selected_attribute_'+randomId).val();
            } else {
                var id_attribute = 0;
            }

            opartDevisDeleteSpecificPrice();

            var current_id_attribute = $('#select_attribute_' + randomId).val()

            $('#last_selected_attribute_' + randomId).val(current_id_attribute);
        }

        opartDevisGetReducedPricesCombinations();
    });
}

function opartDevisAutoChangePrice(currentInput, inputClass) {
    $('.' + inputClass).each(function() {
        $(this).val(currentInput.value);
    });
}

function opartBindOnChange() {
    $('.calcTotalOnChange').unbind( "change" );
    $('.calcTotalOnChange').change(function() {
        var randomId = $(this).attr('id').substring($(this).attr('id').lastIndexOf('_')+1);

        if ($('#specificPriceInput_' + randomId).val()=='') {
            if ($('#last_selected_attribute_'+randomId).length) {
                var id_attribute = $('#last_selected_attribute_'+randomId).val();
            } else {
                var id_attribute = 0;
            }

            opartDevisDeleteSpecificPrice();

            var current_id_attribute = $('#select_attribute_' + randomId).val()

            $('#last_selected_attribute_' + randomId).val(current_id_attribute);
        }

        opartDevisGetReducedPrices();
    });

    $('.calcTotalOnChangeDec').unbind( "change" );
    $('.calcTotalOnChangeDec').change(function() {
        var randomId = $(this).attr('id').substring($(this).attr('id').lastIndexOf('_')+1);

        if ($('#specificPriceInput_' + randomId).val()=='') {
            if ($('#last_selected_attribute_'+randomId).length) {
                var id_attribute = $('#last_selected_attribute_'+randomId).val();
            } else {
                var id_attribute = 0;
            }

            opartDevisDeleteSpecificPrice();

            var current_id_attribute = $('#select_attribute_' + randomId).val()

            $('#last_selected_attribute_' + randomId).val(current_id_attribute);
        }

        opartDevisGetReducedPricesCombinations();
    });
}

function opartDevisAddRuleToQuotation(ruleId, name, description, code, free_shipping, reduction_percent, reduction_amount, reduction_type, gift_product) {
    var gift_product_link=(gift_product==0)?'':gift_product;
    var newTr = '<tr id="trCartRule_' + ruleId + '" style="display:none;">';
    newTr += '<td>' + ruleId + '<input type="hidden" name="add_rule[]" value="' + ruleId + '" /></td>';
    newTr += '<td>' + name + '</td>';
    newTr += '<td>' + description + '</td>';
    newTr += '<td>' + code + '</td>';
    newTr += '<td>' + ((free_shipping==1) ? '<i class="icon-check"></i>' : '') + '</td>';
    newTr += '<td>' + reduction_percent + '</td>';
    newTr += '<td>' + reduction_amount + '</td>';
    newTr += '<td>' + reduction_type + '</td>';
    newTr += '<td>' + gift_product_link + '</td>';
    newTr += '<td><a href="#" onclick="opartDevisDeleteRule(\'' + ruleId + '\'); return false;"><i class="icon-trash"></i></a></td>';
    newTr += '</tr>';

    $('#opartDevisCartRuleList').append(newTr);
    $('#trCartRule_'+ruleId).show('fast');
}

function opartDevisLoadProductCombinations(randomId, idAttribute) {
    opartDevisToggleSubmitBtn(0);

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'LoadProductCombinations',
            token: token,
            id_product: $('#whoIs_' + randomId).val()
        },
        success: function(combinations){
            opartDevisPopulateDeclinaisons(
                combinations,
                randomId,
                idAttribute
            );
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisPopulateDeclinaisons(combinations, randomId, idAttribute) {
    if (!Object.keys(combinations).length) {
        return false;
    }

    //select soit defaut soit selected
    var s = $('<select id="select_attribute_' + randomId + '" name="add_attribute[' + randomId + ']" class="calcTotalOnChangeDec" />');
    for (var key in combinations) {
        var selected = "";
        if (idAttribute != 0 && key == idAttribute) {
            selected = "selected";
        } else if (idAttribute == 0 && combinations['default_on'] == 1) {
            selected = "selected";
        }

        s.append('<option ' + selected + ' value="' + key + '" title="' + combinations[key]['price'] + '">' + combinations[key]['attribute_designation'] + ' [' + combinations[key]['reference'] + '] (' + combinations[key]['price'] + ')</option>');
    }

    $('#declinaisonsProd_' + randomId).append(s);
    //add hidden field last id attribute
    var hidden_field_value = $('#select_attribute_' + randomId).val();
    var hidden_field = '<input type="hidden" value="' + hidden_field_value + '" id="last_selected_attribute_' + randomId + '" />';
    $('#declinaisonsProd_' + randomId).append(hidden_field);

    opartBindOnChange();
    opartDevisToggleSubmitBtn(1);
}

function opartDevisGetTotalCart() {
    opartDevisToggleSubmitBtn(0);

    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'GetTotalCart'},
        {name: 'token', value: token},
        {name: 'id_cart', value: $('#opart_devis_id_cart').val()}
    );

    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: $.param(data),
        success: function(data){
            $('#totalProductHt').html(data.total_products.toFixed(2));
            $('#totalDiscountsHt').html(data.total_discounts_tax_exc.toFixed(2));
            $('#totalShippingHt').html(data.total_shipping_tax_exc.toFixed(2));
            $('#totalTax').html(data.total_tax.toFixed(2));

            if (data.group_tax_method) {
                $('#totalTax').html('<strike>'+(data.total_tax.toFixed(2))+'</strike>');
                $('#totalQuotationWithTax').html((data.total_price-data.total_tax).toFixed(2));
            } else {
                $('#totalTax').html(data.total_tax.toFixed(2));
                $('#totalQuotationWithTax').html(data.total_price.toFixed(2));
            }

            $('#opart_devis_id_cart').val(data.id_cart);

            opartDevisToggleSubmitBtn(1);
            $('#opart_devis_refresh_total_quotation').removeClass('disabled');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
            $('#opart_devis_refresh_total_quotation').removeClass('disabled');
        }
    });
}

function opartDevisAddCustomerToQuotation(customerId, firstname, lastname, email) {
    var newHtml = '(' + customerId + ') ' + lastname + ' ' + firstname + ' - ' + email;
    $('#opart_devis_customer_info').html(newHtml);
    $('#opart_devis_customer_id').val(customerId);

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'GetAddresses',
            token: token,
            id_customer: customerId
        },
        success: function(data){
            if (data.return) {
                opartDevisPopulateSelectAddress(data.addresses);
            } else {
                console.log(data.error);
                return false;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisPopulateSelectAddress(addresses) {
    var invoiceSelect = $('#opart_devis_invoice_address_input');
    var deliverySelect = $('#opart_devis_delivery_address_input');

    invoiceSelect.html('');
    deliverySelect.html('');

    $.each(addresses, function(index, address) {
        if ($('#selected_invoice').val() == address.id_address) {
            var selectedInvoice = 'selected';
        } else {
            var selectedInvoice = '';
        }

        if ($('#selected_delivery').val() == address.id_address) {
            var selectedDelivery = 'selected';
        } else {
            var selectedDelivery = '';
        }

        invoiceSelect.append(
            '<option ' + selectedInvoice + ' value="' + address.id_address + '">'
            + '[' + address.alias + ']'
            + ' - ' + address.company
            + ' - ' + address.lastname + ' ' + address.firstname
            + ' - ' + address.address1
            + ' - ' + address.address2
            + ' - ' + address.postcode
            + ' - ' + address.city
            + ' - ' + address.country_name
            + '</option>'
        );

        deliverySelect.append(
            '<option ' + selectedDelivery + ' value="' + address.id_address + '">'
            + '[' + address.alias + ']'
            + ' - ' + address.company
            + ' - ' + address.lastname + ' ' + address.firstname
            + ' - ' + address.address1
            + ' - ' + address.address2
            + ' - ' + address.postcode
            + ' - ' + address.city
            + ' - ' + address.country_name
            + '</option>'
        );
    });

    //opartDevisLoadCarrierList();
}

function opartDevisDeleteSpecificPrice() {
    var id_cart = $('#opart_devis_id_cart').val();

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'DeleteSpecificPrice',
            token: token,
            id_cart: id_cart,
        },
        success: function(data) {
            if (data) {
                console.log('Specific prices successfully deleted.');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisDeleteProd(idRandom) {
    $('#trProd_'+idRandom).hide("fast", function() {
        if ($('#select_attribute_'+idRandom).length) {
            var id_attribute = $('#select_attribute_'+idRandom).val();
        } else {
            var id_attribute = null;
        }

        opartDevisDeleteSpecificPrice();
        $('#trProd_'+idRandom).remove();
    });
}

function opartDevisDeleteRule(ruleId) {
    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'DeleteCartRule',
            token: token,
            id_cart: function () {
                return $('#opart_devis_id_cart').val();
            },
            id_cart_rule: ruleId
        },
        cache: false,
        success: function(data){
            console.log(data);
        }, error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });

    $('#trCartRule_'+ruleId).hide("fast", function() {
        $('#trCartRule_'+ruleId).remove();
    });
}

function opartDevisGetReducedPrices() {
    opartDevisToggleSubmitBtn(0);

    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'GetReducedPrices'},
        {name: 'token', value: token},
        {name: 'id_cart', value:
            function () {
                return $('#opart_devis_id_cart').val();
            }
        }
    );

    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: $.param(data),
        cache: false,
        success: function(data){
            if (data.return) {
                $('#opart_devis_id_cart').val(data.id_cart);

                $.each(data.reduced_prices, function(randomId, reduced_price) {
                    $('#stockAvailable_' + randomId).html(reduced_price.stock_available);
                    $('#prodPrice_' + randomId).html(reduced_price.real_price);
                    $('#prodReducedPrice_' + randomId).html(reduced_price.reduced_price);
                    $('#specificPriceInput_'+ randomId).val(reduced_price.your_price);
                    $('#specificDiscount_'+ randomId).val(((reduced_price.real_price  - reduced_price.your_price)/reduced_price.real_price*100).toFixed(2));
                    $('#prodTotal_'+ randomId).html(reduced_price.total);
                });
            } else {
                console.log(data.error);
            }

            opartDevisToggleSubmitBtn(1);
        }, error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisGetReducedPricesCombinations() {
    opartDevisToggleSubmitBtn(0);

    $('#specificPriceInput_'+ randomId).val('');

    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'GetReducedPrices'},
        {name: 'token', value: token},
        {name: 'id_cart', value:
            function () {
                return $('#opart_devis_id_cart').val();
            }
        }
    );

    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: $.param(data),
        cache: false,
        success: function(data){
            if (data.return) {
                $('#opart_devis_id_cart').val(data.id_cart);

                $.each(data.reduced_prices, function(randomId, reduced_price) {
                    $('#stockAvailable_' + randomId).html(reduced_price.stock_available);
                    $('#prodPrice_' + randomId).html(reduced_price.real_price);
                    $('#prodReducedPrice_' + randomId).html(reduced_price.reduced_price);
                    $('#specificPriceInput_'+ randomId).val('');
                    $('#prodTotal_'+ randomId).html(reduced_price.total);
                });
            } else {
                console.log(data.error);
            }

            opartDevisToggleSubmitBtn(1);
        }, error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisDeleteUploadedFile(element){
    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        data: {
            ajax: true,
            action: 'DeleteUploadedFile',
            token: token,
            upload_name: $(element).attr('data-name'),
            upload_id: $(element).attr('data-id')
        },
        success: function(data) {
            console.log(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisToggleSubmitBtn(showMe) {
    if (showMe == 0) {
        $('#opartBtnSubmit').prop('disabled',true);
    } else {
        $('#opartBtnSubmit').prop('disabled',false);
    }
}

function opartDevisLoadCarrierList() {
    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'LoadCarrierList'},
        {name: 'id_cart', value:
            function () {
                return $('#opart_devis_id_cart').val();
            }
        }
    );

    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        data: $.param(data),
        cache: false,
        dataType: 'JSON',
        success: function(data){
            $('#opart_devis_id_cart').val(data.id_cart);
            opartDevisPopulateSelectCarrier(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisPopulateSelectCarrier(data) {
    var carrierSelect = $('#opart_devis_carrier_input');
    carrierSelect.html('');

    if (data['prefered_order']) {
        // get prefered carrier order
        var order = data['prefered_order'].split(',');

        for (var k=0; k < order.length; k++) {
            if ($('#selected_carrier').val() == order[k]) {
                var selected = 'selected';
            } else {
                 var selected = '';
            }

            carrierSelect.append('<option value="' + order[k] + '" ' + selected + '>' + data[order[k]]['name'] + ' - ' + data[order[k]]['price'] + ' (' + data[order[k]]['taxOrnot'] + ')</option>');
        }
    }
}


// Fonction pour stocker l'ID de l'élément déplacé lorsqu'il est commencé à être déplacé
function dragstart(event) {
  event.dataTransfer.setData('text/plain', event.target.id);
  event.target.classList.add('dragging');
}

// Fonction pour empêcher le comportement par défaut de l'élément survolé lorsqu'un élément est déplacé au-dessus de lui
function dragover(event) {
  event.preventDefault();
}

// Fonction pour ajouter une classe CSS lorsqu'un élément est survolé par un élément déplacé
function dragenter(event) {
  event.target.classList.add('hovered');
}

// Fonction pour supprimer une classe CSS lorsqu'un élément déplacé quitte un élément survolé
function dragleave(event) {
  event.target.classList.remove('hovered');
}

// Fonction pour déplacer l'élément déplacé à l'endroit où il est lâché
function drop(event) {
  event.preventDefault();
  event.target.classList.remove('hovered');
  const draggedElement = document.querySelector('.dragging');
  if (event.target.parentElement.id === 'opartDevisProdList') {
    const targetElement = event.target;
    const draggedPosition = Array.from(targetElement.parentElement.children).indexOf(draggedElement);
    const targetPosition = Array.from(targetElement.parentElement.children).indexOf(targetElement);
    if (draggedPosition < targetPosition) {
      targetElement.parentElement.insertBefore(draggedElement, targetElement.nextSibling);
    } else {
      targetElement.parentElement.insertBefore(draggedElement, targetElement);
    }
  }
  draggedElement.classList.remove('dragging');
}