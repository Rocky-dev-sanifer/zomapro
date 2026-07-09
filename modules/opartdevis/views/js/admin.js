/**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

var pendingReducedPricesRequests = 0;

$(document).ready(function(){



    // Drop and dragover on tbody; on table we remove placeholder when over invalid zone (e.g. header) so user sees the bar disappear
    var opartDevisProdListTbody = document.getElementById('opartDevisProdList');
    var opartDevisTable = opartDevisProdListTbody ? opartDevisProdListTbody.closest('table') : null;
    if (opartDevisProdListTbody) {
        opartDevisProdListTbody.addEventListener('dragover', function (e) {
            e.preventDefault();
        });
        opartDevisProdListTbody.addEventListener('drop', function (e) {
            drop(e);
        });
    }
    if (opartDevisTable) {
        opartDevisTable.addEventListener('dragover', function (e) {
            e.preventDefault();
            var targetRow = e.target.closest('tr');
            if (!targetRow || !targetRow.parentElement || targetRow.parentElement.id !== 'opartDevisProdList') {
                opartDevisRemovePlaceholder();
                return;
            }
            var insertBeforeRef = null;
            if (targetRow.id && targetRow.id.indexOf('trProd_') === 0) {
                return;
            }
            if (targetRow.querySelector && targetRow.querySelector('.row-separator')) {
                insertBeforeRef = targetRow.nextElementSibling;
            } else if (targetRow.id && targetRow.id.indexOf('trComment_') === 0) {
                var rect = targetRow.getBoundingClientRect();
                var midY = rect.top + rect.height / 2;
                insertBeforeRef = (e.clientY < midY)
                    ? targetRow.previousElementSibling
                    : targetRow.nextElementSibling.nextElementSibling;
            }
            opartDevisUpdatePlaceholder(insertBeforeRef);
        });
    }

     // initialize tinymce
    tinySetup({
        editor_selector : "autoload_rte",
        plugins : 'code advlist autolink link lists charmap print textcolor colorpicker style',
        forced_root_block : ""
    });


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

                    var price = parseFloat(products[i].price).toFixed(2);
                    var reduced_price = parseFloat(products[i].reduced_price).toFixed(2);

                    formated_products[i] = {
                        data: products[i],
                        value: (
                            products[i].id_product
                            + ' - ' + products[i].name
                            + ' - ' + price
                            + ' - ' + reduced_price
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
                product.minimal_quantity,
                product.id_product_attribute,
                product.reduced_price,
                product.reduced_price_whitout_group,
                product.percentage_reduc,
                product.wholesale_price,
                null,
                product.reduced_price,
                product.customizable,
                product.percentage_reduc_groupe
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


    




  $(' #opart_devis_carrier_input').click(function(e) {
        e.preventDefault();


        opartDevisGetTotalCart();
        setTimeout(function() {
                 opartDevisLoadCarrierList();
            }, 5000);
       
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

    $(document).on('input', 'input.discount-group-input', function() {
        var val = $(this).val();
        if (val.indexOf(',') !== -1)
            $(this).val(val.replace(',', '.'));
    });

    $(document).on('input', 'input.discount-input', function() {
        var val = $(this).val();
        if (val.indexOf(',') !== -1)
            $(this).val(val.replace(',', '.'));
    });

    //vérifie si les champs de customization obligatoire sont bien renseigné
    $('#opartBtnSubmit').on('click', function() {
        var errorMessages = [];  
        var allFieldsValid = true;  

        $('#opartDevisForm [required]').each(function() {
            if ($.trim($(this).val()) === '') {
                allFieldsValid = false;
                errorMessages.push(errorpersonnalisation + ' : ' + ($(this).attr('placeholder') || $(this).attr('name')));
            }
        });

        if (!allFieldsValid) {
            alert('Erreur :\n\n' + errorMessages.join('\n'));
        } else {
            $('#opartBtnSubmit').submit();  
        }
    });


     // --- Create CartRule modal ---
   $(document).on('click', '#opart_devis_create_cartrule', function(e){
    e.preventDefault();

    $('#opartDevisCreateCartRuleError').hide().html('');

    try {
        var now = new Date();
        var to = new Date();
        to.setDate(to.getDate() + 30);

        function pad(n){ return (n < 10 ? '0'+n : ''+n); }
        function fmt(d){
            return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
        }

        $('#opartDevisCartRuleForm [name="date_from"]').val(fmt(now));
        $('#opartDevisCartRuleForm [name="date_to"]').val(fmt(to));
        $('#opartDevisCartRuleForm [name="name"]').val('Discount');
        $('#opartDevisReductionType').val('percent');
        $('#opartDevisCartRuleForm [name="reduction_percent"]').val(10);
    } catch (err) {}

    opartDevisToggleCartRuleFields();
    $('#opartDevisCreateCartRuleModal').modal('show');
});

$(document).on('change', '#opartDevisReductionType', function(){
    opartDevisToggleCartRuleFields();
});

$(document).on('click', '.opartdevis-toggle-comment', function(e){
    e.preventDefault();
    var id = $(this).data('random-id');
    var $tr = $('#trComment_' + id);
    $tr.toggleClass('comment-row-collapsed');
    var $icon = $(this).find('i');
    if ($tr.hasClass('comment-row-collapsed')) {
        $icon.removeClass('icon-chevron-up').addClass('icon-chevron-down');
        $(this).attr('title', typeof show_comment !== 'undefined' ? show_comment : 'Show comment');
    } else {
        $icon.removeClass('icon-chevron-down').addClass('icon-chevron-up');
        $(this).attr('title', typeof hide_comment !== 'undefined' ? hide_comment : 'Hide comment');
    }
});

$(document).on('click', '#opartDevisCreateCartRuleSubmit', function(e){
    e.preventDefault();

    var $box = $('#opartDevisCartRuleForm');
    $('#opartDevisCreateCartRuleError').hide().html('');

    // validation required (dans le modal)
    var ok = true;
    $box.find('[required]').each(function(){
        if ($.trim($(this).val()) === '') ok = false;
    });

    if (!ok) {
        $('#opartDevisCreateCartRuleError').html('Champs obligatoires manquants.').show();
        return false;
    }

    // récupérer valeurs du modal
    var data = [
        {name:'ajax', value:1},
        {name:'action', value:'CreateCartRule'},
        {name:'token', value: token},
        {name:'name', value: $box.find('[name="name"]').val()},
        {name:'reduction_type', value: $box.find('[name="reduction_type"]').val()},
        {name:'reduction_percent', value: $box.find('[name="reduction_percent"]').val()},
        {name:'reduction_amount', value: $box.find('[name="reduction_amount"]').val()},
        {name:'date_from', value: $box.find('[name="date_from"]').val()},
        {name:'date_to', value: $box.find('[name="date_to"]').val()}
    ];

    $('#opartDevisCreateCartRuleSubmit').prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'json',
        data: $.param(data),
        cache: false
    }).done(function(resp){
        $('#opartDevisCreateCartRuleSubmit').prop('disabled', false);

        if (!resp || !resp.success) {
            $('#opartDevisCreateCartRuleError').html((resp && resp.error) ? resp.error : 'Erreur création réduction.').show();
            return;
        }

        var label = (resp.name && resp.name[id_lang_default]) ? resp.name[id_lang_default] : '';
        opartDevisAddDiscountToQuotation(resp.id_cart_rule, resp.code || '', resp.description || '', label);

        $('#opartDevisCreateCartRuleModal').modal('hide');
        opartDevisGetTotalCart();
    }).fail(function(xhr){
        $('#opartDevisCreateCartRuleSubmit').prop('disabled', false);
        console.log('AJAX FAIL', xhr.status, xhr.responseText);
        $('#opartDevisCreateCartRuleError').html('Erreur AJAX.').show();
    });

    return false;
});


 // --- Create customer modal ---
$(document).on('click', '#opartDevisCreateCustomerSubmit', function(e){
    e.preventDefault();

    var $box = $('#opartDevisCustomerForm');
    $('#opartDevisCreateCustomerError').hide().html('');

    // validation required (dans le modal)
    var ok = true;
    $box.find('[required]').each(function(){
        if ($.trim($(this).val()) === '') ok = false;
    });

    if (!ok) {
        $('#opartDevisCreateCustomerError').html('Champs obligatoires manquants.').show();
        return false;
    }

    // récupérer valeurs du modal
    var data = [
        {name:'ajax', value:1},
        {name:'action', value:'CreateCustomer'},
        {name:'token', value: token},
        {name:'firstname', value: $box.find('[name="firstname"]').val()},
        {name:'lastname', value: $box.find('[name="lastname"]').val()},
        {name:'email', value: $box.find('[name="email"]').val()}
    ];

    $('#opartDevisCreateCustomerSubmit').prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'json',
        data: $.param(data),
        cache: false
    }).done(function(resp){
        $('#opartDevisCreateCustomerSubmit').prop('disabled', false);

        if (!resp || !resp.success) {
            $('#opartDevisCreateCustomerError').html((resp && resp.error) ? resp.error : 'Erreur création réduction.').show();
            return;
        }

        opartDevisAddCustomerToQuotation(resp.id_customer, resp.firstname, resp.lastname, resp.email);

        $('#opartDevisCreateCustomerModal').modal('hide');
    }).fail(function(xhr){
        $('#opartDevisCreateCustomerError').prop('disabled', false);
         $('#opartDevisCreateCustomerSubmit').prop('disabled', false);
        $('#opartDevisCreateCustomerError').html('Erreur AJAX.').show();
    });

    return false;
});


// --- Create adresse modal ---
$(document).on('click', '#opartDevisCreateAdresseSubmit', function(e){
    e.preventDefault();

    var $box = $('#opartDevisAdresseForm');
    $('#opartDevisCreateAdresseError').hide().html('');

    // validation required (dans le modal)
    var ok = true;
    $box.find('[required]').each(function(){
        if ($.trim($(this).val()) === '') ok = false;
    });

    if (!ok) {
        $('#opartDevisCreateAdresseError').html('Champs obligatoires manquants.').show();
        return false;
    }

    // récupérer valeurs du modal
    var data = [
        {name:'ajax', value:1},
        {name:'action', value:'CreateAdresse'},
        {name:'token', value: token},
        {name:'adresse', value: $box.find('[name="adresse"]').val()},
        {name:'postcode', value: $box.find('[name="postcode"]').val()},
        {name:'country', value: $box.find('[name="country"]').val()},
        {name:'city', value: $box.find('[name="city"]').val()},
        {name:'id_customer', value: $('#opart_devis_customer_id').val()},
    ];

    $('#opartDevisCreateAdresseSubmit').prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'json',
        data: $.param(data),
        cache: false
    }).done(function(resp){
        $('#opartDevisCreateAdresseSubmit').prop('disabled', false);

        if (!resp || !resp.success) {
            $('#opartDevisAdresseCustomerError').html((resp && resp.error) ? resp.error : 'Erreur création adresse.').show();
            return;
        }

         $.ajax({
            type: 'POST',
            url: ajaxUrl,
            dataType: 'JSON',
            data: {
                ajax: true,
                action: 'GetAddresses',
                token: token,
                id_customer: $('#opart_devis_customer_id').val()
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

        $('#opartDevisCreateAdresseModal').modal('hide');
    }).fail(function(xhr){
        $('#opartDevisCreateAdresseError').prop('disabled', false);
         $('#opartDevisCreateAdresseSubmit').prop('disabled', false);
        $('#opartDevisCreateAdresseError').html('Erreur AJAX.').show();
    });

    return false;
});







});


function opartDevisToggleCartRuleFields() {
    var t = $('#opartDevisReductionType').val();
    if (t === 'percent') {
        $('#opartDevisReductionPercentWrap').show().find('input').prop('required', true);
        $('#opartDevisReductionAmountWrap').hide().find('input').prop('required', false);
    } else {
        $('#opartDevisReductionPercentWrap').hide().find('input').prop('required', false);
        $('#opartDevisReductionAmountWrap').show().find('input').prop('required', true);
    }
}

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

function opartDevisAddProductToQuotation(prodId, prodName, stockAvailable, prodPrice, qty, idAttribute, specificPrice, yourPrice,DiscountPrice,wholesale_price, customization_datas_json, total, customizable, reduc_groupe = 0, commentaire, last = 1,copy = false) {
    opartDevisToggleSubmitBtn(0);

    var id_attribute = (idAttribute == null) ? idAttribute : null;
    var specificPrice = (specificPrice != undefined) ? parseFloat(specificPrice).toFixed(2) : '';
    var specificQty = (specificQty != undefined) ? specificQty : '';
    var yourPrice = (yourPrice != undefined) ? parseFloat(yourPrice).toFixed(6) : '';
    var DiscountPrice = (DiscountPrice != undefined) ? parseFloat(DiscountPrice).toFixed(2) : '';
     var commentaire = (commentaire != undefined) ? commentaire : '';
     var wholesalePrice = parseFloat(wholesale_price);
        if (isNaN(wholesalePrice)) {
            wholesalePrice = 0;
        }
    var wholesalePriceFormatted = wholesalePrice.toFixed(2);

    randomId = new Date().getTime() + Math.floor(Math.random() * 1000000);

    var customization_datas = $.parseJSON(customization_datas_json);
    var displayedCustomizationDatas = '';
    var qtyInputType = 'text';
    var onChangeCustomizationPrice = '';
    var customPriceClass = ''

    if (customization_datas && customization_datas.length) {
        for (var i = 0; i < customization_datas.length; i++){
            displayedCustomizationDatas += '<div class="col-md-12 tdAdminCustomizationDataValue"><div class="col-md-12">';

            var customization_datas_array = customization_datas[i]['datas'][1];

            for (var j = 0; j < customization_datas_array.length; j++){
                var addBr = (j > 0) ? '<br />' : '';
                displayedCustomizationDatas += addBr + customization_datas_array[j]['name'] + ' : ' + customization_datas_array[j]['value'];
            }

            displayedCustomizationDatas += '</div></div>';
        }

        customPriceClass = 'customprice_' + prodId + '_' + idAttribute;
        onChangeCustomizationPrice = 'onchange="opartDevisAutoChangePrice(this,\'' + customPriceClass + '\')"';
    }

    var margeValue = (yourPrice != '' && yourPrice != false)
        ? (((yourPrice - wholesalePrice) / yourPrice) * 100).toFixed(2)
        : (((specificPrice - wholesalePrice) / specificPrice) * 100).toFixed(2);
    var qtyExtra = (customization_datas && customization_datas.length) ? '<span></span>' : '';

    var actionsHtml = '';
    if (customizable != 0)
        actionsHtml += '<button type="button" class="btn btn-link" data-toggle="modal" data-target="#customization' + randomId + '">' + personalize + '</button>';
    if (customization_datas == null || customization_datas == '') {
        actionsHtml += '<a href="#" onclick="opartDevisDeleteProd(\'' + randomId + '\'); return false;"><i class="icon-trash"></i></a>';
        if (idAttribute != null && idAttribute != 0)
            actionsHtml += ' <a href="#" onclick="event.preventDefault();opartDevisAddProductToQuotation(' + prodId + ',\'' + prodName + '\',' + stockAvailable + ', ' + prodPrice + ', ' + qty + ', ' + idAttribute + ', ' + specificPrice + ', null, null,' + wholesale_price + ',null, ' + total + ', \'' + customizable + '\', '+reduc_groupe+',\'' + commentaire + '\',true);"><i class="icon-copy"></i></a>';
        actionsHtml += '<i class="icon-move"></i>';
        actionsHtml += ' <a href="#" class="opartdevis-toggle-comment" data-random-id="' + randomId + '" title="' + (typeof show_comment !== 'undefined' ? show_comment : 'Show comment') + '"><i class="icon-chevron-down"></i></a>';
    }

    var modalHtml = '';
    if (customizable != 0)
        modalHtml = '<div class="modal fade" id="customization' + randomId + '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exampleModalLabel">' + personalize + '</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body">...</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">' + savecustom + '</button></div></div></div></div>';

    var commentaireEscaped = commentaire.replace(/<br\s*\/?>/gi, '\r');
    var prodPriceFormatted = parseFloat(prodPrice).toFixed(2);
    var totalFormatted = parseFloat(total).toFixed(2);

    var newTr = (typeof productLineTemplate !== 'undefined' ? productLineTemplate : '')
        .replace(/__RANDOM_ID__/g, randomId)
        .replace(/__PROD_ID__/g, prodId)
        .replace(/__PROD_NAME__/g, prodName)
        .replace(/__STOCK_AVAILABLE__/g, stockAvailable)
        .replace(/__WHOLESALE_PRICE_FORMATTED__/g, wholesalePriceFormatted)
        .replace(/__PROD_PRICE__/g, prodPriceFormatted)
        .replace(/__DISCOUNT_PRICE__/g, DiscountPrice)
        .replace(/__ONCHANGE_CUSTOMIZATION_PRICE__/g, onChangeCustomizationPrice)
        .replace(/__YOUR_PRICE__/g, yourPrice)
        .replace(/__CUSTOM_PRICE_CLASS__/g, customPriceClass)
        .replace(/__REDUC_GROUPE__/g, reduc_groupe)
        .replace(/__MARGE_VALUE__/g, margeValue)
        .replace(/__WHOLESALE_PRICE__/g, wholesale_price)
        .replace(/__SPECIFIC_PRICE__/g, specificPrice)
        .replace(/__QTY__/g, qty)
        .replace(/__QTY_EXTRA__/g, qtyExtra)
        .replace(/__TOTAL__/g, totalFormatted)
        .replace(/__ACTIONS_HTML__/g, actionsHtml)
        .replace(/__COMMENTAIRE__/g, commentaireEscaped)
        .replace(/__MODAL_HTML__/g, modalHtml);

    newTr += displayedCustomizationDatas;

    


    $('#opartDevisProdList').append(newTr);
    var rowId = randomId;
    $('#trProd_'+rowId).find('.icon-move').on('mousedown', function() {
        document.getElementById('trProd_'+rowId).setAttribute('draggable', 'true');
    });
    $('#trProd_'+rowId).find('.icon-move').on('mouseup mouseleave', function() {
        var row = document.getElementById('trProd_'+rowId);
        if (row && !row.classList.contains('dragging'))
            row.setAttribute('draggable', 'false');
    });
    $('#trProd_'+randomId).show('fast');

    if(copy == true){
         var position = $('#trProd_'+randomId).offset().top - 150;


      $('html, body').animate({
        scrollTop: position
      }, 1000);

    }

   
    if(customizable != 0){
        opartDevisAddCustomization(prodId,randomId,customization_datas);
    }


    
        opartDevisLoadProductCombinations(randomId, idAttribute, last);
    
    
    if(last == 1){
         setTimeout(function() {
            OpartPriceDiscountChange();
          opartBindOnChange();
           if(idAttribute == 0){
                opartDevisGetReducedPrices();
            }
        }, 1000)
    }
    $('.calcTotalOnChangeDiscount').unbind( "change" );
     $('.calcTotalOnChangeDiscount').change(function() {
         var id = $(this).attr('id');
        var randomId = id.substring(id.lastIndexOf('_') + 1);
        opartDevisSetManualPriceFlag(randomId, true);
        var article = document.getElementById('specificDiscount_' + randomId);
        var prodPrice = article.dataset.price // "3"
        var pourcentage = $('#specificDiscount_' + randomId).val();
        var coeff = (100 - pourcentage) / 100;
        var remise = prodPrice * coeff;
        var wholesale_price = $('#marge'+ randomId).data('price');
        document.getElementById('specificPriceInput_' + randomId).value = remise.toFixed(2);
         $('#marge' + randomId).text(((remise - wholesale_price)/remise*100).toFixed(2));
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


}

function OpartPriceDiscountChange(prodPrice) {
    $('.calcTotalOnChangeDiscount').unbind( "change" );
     $('.calcTotalOnChangeDiscount').change(function() {
         var id = $(this).attr('id');
        var randomId = id.substring(id.lastIndexOf('_') + 1);
        opartDevisSetManualPriceFlag(randomId, true);
        var article = document.getElementById('specificDiscount_' + randomId);
        var prodPrice = article.dataset.price // "3"
        var pourcentage = $('#specificDiscount_' + randomId).val();
        var coeff = (100 - pourcentage) / 100;
        var remise = prodPrice * coeff;
        var wholesale_price = $('#marge'+ randomId).data('price');
        document.getElementById('specificPriceInput_' + randomId).value = remise.toFixed(2);
         $('#marge' + randomId).text(((remise - wholesale_price)/remise*100).toFixed(2));
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
        var id = $(this).attr('id');
        var randomId = id.substring(id.lastIndexOf('_') + 1);
        opartDevisApplySelectedCombinationMinimum(randomId);

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

        $('#specificPriceInput_' + randomId).val('');
        opartDevisSetManualPriceFlag(randomId, false);

        //opartDevisGetReducedPricesCombinations(randomId);
    });
}

function opartDevisAutoChangePrice(currentInput, inputClass) {
    $('.' + inputClass).each(function() {
        $(this).val(currentInput.value);
    });
}

function opartDevisSetManualPriceFlag(randomId, isManual) {
    var $manualInput = $('#manualPrice_' + randomId);
    if ($manualInput.length) {
        $manualInput.val(isManual ? '1' : '0');
    }
}

function opartDevisIsManualPrice(randomId) {
    return $('#manualPrice_' + randomId).val() === '1';
}

function opartDevisUpdateWholesalePrice(randomId, wholesalePrice) {
    var price = parseFloat(wholesalePrice);
    if (isNaN(price)) {
        price = 0;
    }

    $('#wholesalePrice' + randomId).html(price.toFixed(2));
    $('#marge' + randomId).data('price', price);

    var currentPrice = parseFloat($('#specificPriceInput_' + randomId).val());
    if (isNaN(currentPrice) || currentPrice <= 0) {
        currentPrice = parseFloat($('#prodReducedPrice_' + randomId).html());
    }

    if (!isNaN(currentPrice) && currentPrice > 0) {
        $('#marge' + randomId).text(((currentPrice - price) / currentPrice * 100).toFixed(2));
    }
}

    function opartBindOnChange() {
    $('.calcTotalOnChange').unbind( "change" );
    $('.calcTotalOnChange').change(function() {
        var id = $(this).attr('id');
        var randomId = id.substring(id.lastIndexOf('_') + 1);


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

        if (id.indexOf('specificPriceInput_') !== -1) {
            opartDevisSetManualPriceFlag(randomId, true);
            var remise = parseFloat($('#specificPriceInput_' + randomId).val());
            if (!isNaN(remise) && remise > 0) {
                var wholesale_price = parseFloat($('#marge' + randomId).data('price'));
                if (!isNaN(wholesale_price)) {
                    $('#marge' + randomId).text(((remise - wholesale_price) / remise * 100).toFixed(2));
                }
            }
        }

        opartDevisGetReducedPrices();
    });

    $('.calcTotalOnChangeDec').unbind( "change" );
    $('.calcTotalOnChangeDec').change(function() {
       var id = $(this).attr('id');
       var randomId = id.substring(id.lastIndexOf('_') + 1);
       opartDevisApplySelectedCombinationMinimum(randomId);

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

        $('#specificPriceInput_' + randomId).val('');
        opartDevisSetManualPriceFlag(randomId, false);

        opartDevisGetReducedPricesCombinations(randomId);
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

function opartDevisLoadProductCombinations(randomId, idAttribute,last) {
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
                idAttribute,
                last
            );
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisPopulateDeclinaisons(combinations, randomId, idAttribute,last) {
    if (!Object.keys(combinations).length) {
        return false;
    }

    //select soit defaut soit selected
    var s = $('<select id="select_attribute_' + randomId + '" name="add_attribute[' + randomId + ']" class="calcTotalOnChangeDec" />');
    for (var key in combinations) {
        var selected = "";
        if (idAttribute != 0 && key == idAttribute) {
            selected = "selected";
        } else if (idAttribute == 0 && combinations[key]['default_on'] == 1) {
            selected = "selected";
        }

        s.append('<option ' + selected + ' value="' + key + '" title="' + combinations[key]['price'] + '" data-min-quantity="' + combinations[key]['minimal_quantity'] + '">' + combinations[key]['attribute_designation'] + ' [' + combinations[key]['reference'] + '] (' + combinations[key]['price'] + ')</option>');
    }

    $('#declinaisonsProd_' + randomId).append(s);
    opartDevisApplySelectedCombinationMinimum(randomId);
    //add hidden field last id attribute
    var hidden_field_value = $('#select_attribute_' + randomId).val();
    var hidden_field = '<input type="hidden" value="' + hidden_field_value + '" id="last_selected_attribute_' + randomId + '" />';
    $('#declinaisonsProd_' + randomId).append(hidden_field);

    opartBindOnChange();
    opartDevisToggleSubmitBtn(1);
    if(last == 1){
          opartDevisGetTotalCart();
    }
}

function opartDevisApplySelectedCombinationMinimum(randomId) {
    var minimumQuantity = parseInt($('#select_attribute_' + randomId + ' option:selected').data('min-quantity'), 10);
    var $quantityInput = $('#inputQty_' + randomId);

    if (!isNaN(minimumQuantity) && minimumQuantity > 0 && $quantityInput.length) {
        var currentQuantity = parseInt($quantityInput.val(), 10);
        if (isNaN(currentQuantity) || currentQuantity < minimumQuantity) {
            $quantityInput.val(minimumQuantity);
        }
    }
}

function opartDevisDisplayQuantityErrors(data) {
    var errors = [];

    if (data && $.isArray(data.errors)) {
        errors = data.errors;
    } else if (data && data.error) {
        errors = [data.error];
    }

    if (!errors.length) {
        return false;
    }

    alert(errors.join("\n"));
    opartDevisToggleSubmitBtn(0);

    return true;
}

function opartDevisGetTotalCart() {

    opartDevisToggleSubmitBtn(0);

    var data = $('#opartDevisForm').serializeArray();

    data.push(

        {name: 'ajax', value: true},
        {name: 'action', value: 'GetTotalCart'},
        {name: 'token', value: token},
        {name: 'id_cart', value: $('#opart_devis_id_cart').val()},
        {name: 'update', value: 1}

    );


    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict


    $.ajax({

        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: $.param(data),

        success: function(data){
            if (opartDevisDisplayQuantityErrors(data) || data.hasError || data.return === false) {
                $('#opart_devis_refresh_total_quotation').removeClass('disabled');
                return;
            }

            opartDevisDisplayQuantityErrors({});

            $('#totalProductHt').html(data.total_products.toFixed(2));
            $('#totalProductWithTax').html(data.total_products_wt.toFixed(2));
            $('#totalDiscountsHt').html(data.total_discounts_tax_exc.toFixed(2));
            $('#totalDiscountsWithTax').html(data.total_discounts.toFixed(2));
            $('#totalShippingHt').html(data.total_shipping_tax_exc.toFixed(2));
            $('#totalShippingWithTax').html(data.total_shipping.toFixed(2));
            $('#totalQuotationHt').html(data.total_price_without_tax.toFixed(2));
            $('#totalTax').html(data.total_tax.toFixed(2));
            $('#totalMarge').html(((data.total_products - data.wholesale_price)/data.total_products * 100).toFixed(2));
            $('#totalQuotationWithTax').html(data.total_price.toFixed(2));
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

    console.log('Suppression des prix spécifiques');
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

function opartDevisDupplicateProd(idRandom) {
        if ($('#select_attribute_'+idRandom).length) {
            var id_attribute = $('#select_attribute_'+idRandom).val();
        } else {
            var id_attribute = null;
        }

        opartDevisDeleteSpecificPrice();
        var ligne = document.getElementById('trProd_'+idRandom);
        var newligne = ligne;
        
        ligne.appendTo(newligne);
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
        },
        {name: 'update', value: 1}
    );

    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: $.param(data),
        cache: false,
        success: function(data){
            if (opartDevisDisplayQuantityErrors(data) || data.hasError || data.return === false) {
                console.log(data.error || data.errors);
                pendingReducedPricesRequests--;
                checkAllReducedPricesDone();
                return;
            } else if (data.return) {
                opartDevisDisplayQuantityErrors({});
                $('#opart_devis_id_cart').val(data.id_cart);
                console.log(data);

                $.each(data.reduced_prices, function(randomId, reduced_price) {
                    var wholesale_price = $('#marge'+ randomId).data('price');
                    $('#stockAvailable_' + randomId).html(reduced_price.stock_available);
                    $('#prodPrice_' + randomId).html(parseFloat(reduced_price.real_price).toFixed(2));
                    $('#prodReducedPrice_' + randomId).html(parseFloat(reduced_price.reduced_price).toFixed(2));                 
                    $('#prodTotal_'+ randomId).html(parseFloat(reduced_price.total).toFixed(2));
                    $('#specificDiscount_' + randomId).attr('data-price', reduced_price.real_price);
                    opartDevisUpdateWholesalePrice(randomId, reduced_price.wholesale_price);
                    var isManual = opartDevisIsManualPrice(randomId);

                    if (!isManual) {
                        var autoPrice = reduced_price.auto_your_price;
                        if (autoPrice !== undefined && autoPrice !== '' && !isNaN(parseFloat(autoPrice))) {
                            $('#specificPriceInput_' + randomId).val(parseFloat(autoPrice).toFixed(2));
                        } else {
                            $('#specificPriceInput_' + randomId).val('');
                        }

                        var basePrice = parseFloat(reduced_price.real_price);
                        var autoDiscountPrice = parseFloat(reduced_price.reduced_price_whitout_group);
                        if (!isNaN(basePrice) && basePrice > 0 && !isNaN(autoDiscountPrice)) {
                            var autoDiscount = ((basePrice - autoDiscountPrice) / basePrice) * 100;
                            $('#specificDiscount_' + randomId).val(autoDiscount.toFixed(2));
                        } else {
                            $('#specificDiscount_' + randomId).val('0');
                        }
                    } else if (reduced_price.your_price !== undefined && reduced_price.your_price !== '' && !isNaN(parseFloat(reduced_price.your_price))) {
                        var discount = ((reduced_price.real_price - parseFloat(reduced_price.your_price)) / reduced_price.real_price) * 100;
                        $('#specificDiscount_' + randomId).val(discount.toFixed(2));
                    }
                    
                });
            } else {
                console.log(data.error);
            }

            opartDevisToggleSubmitBtn(1);
            pendingReducedPricesRequests--;
            checkAllReducedPricesDone();
        }, error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
            pendingReducedPricesRequests--;
            checkAllReducedPricesDone();
        }
    });
}

function checkAllReducedPricesDone() {
    if (pendingReducedPricesRequests <= 0) {
    }
}

function opartDevisGetReducedPricesCombinations(randomId) {
    opartDevisToggleSubmitBtn(0);

    $('#specificPriceInput_'+ randomId).val('');

    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'GetReducedPrices'},
        {name: 'token', value: token},
        {name: 'randomId', value: randomId},
        {name: 'id_cart', value:
            function () {
                return $('#opart_devis_id_cart').val();
            }
        },
        {name: 'update', value: 1}
    );

    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: $.param(data),
        cache: false,
        success: function(data){
            if (opartDevisDisplayQuantityErrors(data) || data.hasError || data.return === false) {
                console.log(data.error || data.errors);
                return;
            } else if (data.return) {
                opartDevisDisplayQuantityErrors({});
                $('#opart_devis_id_cart').val(data.id_cart);
                console.log(data);

                $.each(data.reduced_prices, function(randomId, reduced_price) {
                    $('#stockAvailable_' + randomId).html(reduced_price.stock_available);
                    $('#prodPrice_' + randomId).html(reduced_price.real_price);
                    $('#prodReducedPrice_' + randomId).html(reduced_price.reduced_price);
                    $('#prodTotal_'+ randomId).html(reduced_price.total);
                    $('#specificDiscount_' + randomId).attr('data-price', reduced_price.real_price);
                    opartDevisUpdateWholesalePrice(randomId, reduced_price.wholesale_price);

                    var isManual = opartDevisIsManualPrice(randomId);
                    var realPrice = parseFloat(reduced_price.real_price);
                    var autoDiscountPrice = parseFloat(reduced_price.reduced_price_whitout_group);

                    if (!isManual) {
                        if (realPrice > 0 && !isNaN(autoDiscountPrice)) {
                            var discountFromInitial = ((realPrice - autoDiscountPrice) / realPrice) * 100;
                            $('#specificDiscount_' + randomId).val(discountFromInitial.toFixed(2));
                        } else {
                            $('#specificDiscount_' + randomId).val('0');
                        }

                        var autoPrice = reduced_price.auto_your_price;
                        if (autoPrice !== undefined && autoPrice !== '' && !isNaN(parseFloat(autoPrice))) {
                            $('#specificPriceInput_' + randomId).val(parseFloat(autoPrice).toFixed(2));
                        } else {
                            $('#specificPriceInput_' + randomId).val('');
                        }
                    } else if (reduced_price.your_price !== undefined && reduced_price.your_price !== '' && !isNaN(parseFloat(reduced_price.your_price)) && !isNaN(autoDiscountPrice) && autoDiscountPrice > 0) {
                        var discount = ((autoDiscountPrice - parseFloat(reduced_price.your_price)) / autoDiscountPrice) * 100;
                        $('#specificDiscount_' + randomId).val(discount.toFixed(2));
                    }
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

var opartDevisDragPlaceholder = null;
var opartDevisLastInsertBeforeRef = null;
var opartDevisInsertAtEnd = false;

function opartDevisGetPlaceholderContainer() {
  var tbody = document.getElementById('opartDevisProdList');
  if (!tbody) return null;
  var table = tbody.closest('table');
  return table ? table.parentElement : null;
}

function opartDevisCreatePlaceholder() {
  var bar = document.createElement('div');
  bar.className = 'opartdevis-drag-placeholder';
  return bar;
}

function opartDevisRemovePlaceholder() {
  if (opartDevisDragPlaceholder && opartDevisDragPlaceholder.parentNode)
    opartDevisDragPlaceholder.remove();
  opartDevisLastInsertBeforeRef = null;
  opartDevisInsertAtEnd = false;
}

function opartDevisUpdatePlaceholder(insertBeforeRef) {
  var container = opartDevisGetPlaceholderContainer();
  if (!container)
    return;
  if (!opartDevisDragPlaceholder)
    opartDevisDragPlaceholder = opartDevisCreatePlaceholder();
  if (container.style.position !== 'relative')
    container.style.position = 'relative';
  if (!opartDevisDragPlaceholder.parentNode)
    container.appendChild(opartDevisDragPlaceholder);
  var tbody = document.getElementById('opartDevisProdList');
  if (!tbody)
    return;
  if (!insertBeforeRef) {
    opartDevisLastInsertBeforeRef = null;
    opartDevisInsertAtEnd = true;
    var lastRow = tbody.lastElementChild;
    var contRect = container.getBoundingClientRect();
    if (lastRow) {
      var rowRect = lastRow.getBoundingClientRect();
      var top = rowRect.bottom - contRect.top + container.scrollTop;
      opartDevisDragPlaceholder.style.top = top + 'px';
    } else {
      opartDevisDragPlaceholder.style.top = '0px';
    }
    return;
  }
  if (insertBeforeRef.parentElement.id !== 'opartDevisProdList')
    return;
  opartDevisInsertAtEnd = false;
  var rowRect = insertBeforeRef.getBoundingClientRect();
  var contRect = container.getBoundingClientRect();
  var top = rowRect.top - contRect.top + container.scrollTop;
  opartDevisDragPlaceholder.style.top = top + 'px';
  opartDevisLastInsertBeforeRef = insertBeforeRef;
}

// Fonction pour stocker l'ID de l'élément déplacé lorsqu'il est commencé à être déplacé
function dragstart(event) {
  event.dataTransfer.setData('text/plain', event.target.id);
  event.target.classList.add('dragging');
}

// Fonction pour empêcher le comportement par défaut de l'élément survolé lorsqu'un élément est déplacé au-dessus de lui
function dragover(event) {
  event.preventDefault();
  var targetRow = event.target.closest('tr');
  if (!targetRow || targetRow.parentElement.id !== 'opartDevisProdList')
    return opartDevisRemovePlaceholder();
  if (!targetRow.id || targetRow.id.indexOf('trProd_') !== 0)
    return;
  var rect = targetRow.getBoundingClientRect();
  var midY = rect.top + rect.height / 2;
  var insertBeforeRef = (event.clientY < midY)
    ? targetRow
    : targetRow.nextElementSibling.nextElementSibling.nextElementSibling;
  opartDevisUpdatePlaceholder(insertBeforeRef);
}

// Fonction pour ajouter une classe CSS lorsqu'un élément est survolé par un élément déplacé
function dragenter(event) {
  const targetRow = event.target.closest('tr');
  if (targetRow)
    targetRow.classList.add('hovered');
}

// Fonction pour supprimer une classe CSS lorsqu'un élément déplacé quitte un élément survolé
function dragleave(event) {
  const targetRow = event.target.closest('tr');
  if (targetRow)
    targetRow.classList.remove('hovered');
}

// Fonction pour déplacer le bloc produit (ligne produit + commentaire + séparateur) à l'endroit où il est lâché
// On ne déplace que si une position a été mémorisée (placeholder visible pendant le drag) : évite les incohérences si dragend s'exécute avant drop.
function drop(event) {
    console.log('drop');
  event.preventDefault();
  const targetRow = event.target.closest('tr');
  if (targetRow)
    targetRow.classList.remove('hovered');
  const draggedElement = document.querySelector('.dragging');
  if (!draggedElement)
    return;
  var insertAtEnd = opartDevisInsertAtEnd;
  var insertBeforeRef = opartDevisLastInsertBeforeRef;
  opartDevisRemovePlaceholder();
  if (!insertBeforeRef && !insertAtEnd)
    return;
  var row2 = draggedElement.nextElementSibling;
  var row3 = row2 && row2.nextElementSibling;
  if (!row2 || !row3)
    return;
  if (!row2.id || row2.id.indexOf('trComment_') !== 0)
    return;
  if (!row3.querySelector || !row3.querySelector('.row-separator'))
    return;
  var block = [draggedElement, row2, row3];
  block[0].remove();
  block[1].remove();
  block[2].remove();
  var tbody = document.getElementById('opartDevisProdList');
  if (insertAtEnd) {
    tbody.appendChild(block[0]);
    tbody.appendChild(block[1]);
    tbody.appendChild(block[2]);
  } else {
    tbody.insertBefore(block[0], insertBeforeRef);
    tbody.insertBefore(block[1], insertBeforeRef);
    tbody.insertBefore(block[2], insertBeforeRef);
  }
}




function insertAdjacentNode(newNode, referenceNode, position) {
  if (position === 'before') {
    referenceNode.parentNode.insertBefore(newNode, referenceNode);
  } else {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
  }
}




// Fonction pour supprimer la classe CSS lorsque l'élément déplacé a été déposé
function dragend(event) {
  event.target.classList.remove('dragging');
  event.target.setAttribute('draggable', 'false');
  opartDevisRemovePlaceholder();
  opartDevisLastInsertBeforeRef = null;
}

function opartDevisAddCustomization(id_product, randomId,datacustom){


      $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'GetFieldsCustomization',
            token: token,
            IdProduct: id_product,
        },
        success: function(fields){

            var textcutsom = "";

            fields.forEach(function(field) {
              var name = field.name;
              var required = field.required;
              var id = field.id;

              if(required == 1){
                 textcutsom += '<textarea placeholder="'+name+'"  maxlength="250" required name="textField['+randomId+']['+id+']">';
                 if(datacustom){
                    $.each(datacustom[0].datas[1], function(index, item) {
                        if (item.index == id) {
                            textcutsom += item.value; 
                            return false
                        }
                   });
                        
                }
                 textcutsom += '</textarea>';
              }
              else{
                 textcutsom += '<textarea placeholder="'+name+'"  maxlength="250" name="textField['+randomId+']['+id+']" >';
                 if(datacustom){
                     $.each(datacustom[0].datas[1], function(index, item) {
                        if (item.index == id) {
                            textcutsom += item.value; 
                            return false
                        }
                   });
                }
                 textcutsom += '</textarea>';

              }
            });

            $('#customization'+randomId+' .modal-body').html(textcutsom);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });

}
