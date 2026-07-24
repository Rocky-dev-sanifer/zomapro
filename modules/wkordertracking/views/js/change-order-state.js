/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/

$(document).ready(function() {
    $(".order_state").on("change", function() {
        var id_state = $(this).val();
        var id_order = $(this).data('id-order');
        if (id_order) {
            $.ajax({
                url: path_admin_tracking_state,
                data: {
                    ajax: true,
                    action: "changeOrderState",
                    id_order: id_order,
                    id_state: id_state,
                },
                dataType: 'json',
                success: function(result) {
                    if (result == '1') {
                        showSuccessMessage(success_msg);
                    } else if (result == '0') {
                        showErrorMessage(error_msg);
                    }
                },
                error: function(xhr, status, error) {
                    return 0;
                }
            });
        }
    });
});

function showSuccessMessage(msg) {
    $.growl.notice({ title: "", message: msg });
}

function showErrorMessage(msg) {
    $.growl.error({ title: "", message: msg });
}