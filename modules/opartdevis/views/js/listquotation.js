/**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

function RenameQuote(id){
    var newname = document.getElementById('newname_'+id).value;

     $.ajax({
        type: 'POST',
        url: opart_ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'RenameQuote',
           id_quote: id,
            newname: newname
        },
        success: function(data){
        	var oldname = document.getElementById('name_'+id);
        	oldname.innerText = data;
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });

}
