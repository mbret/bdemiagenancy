
function resizeContent(){
    //alert($('body').css("height"));
    var height = $('body').height();// - $('.navbar').height();
    
    $("#content").css( 'min-height', height + "px");
}

    
$(document).ready(function(){

    resizeContent();

    $(window).resize(function(){
        resizeContent();
    });
    
      
    
    /**
     * Customise la fenêtre modal de confirmation avant ouverture
     * 
     * - ajoute un lien pour le bouton valider
     * - ajoute un contenu personnalisé dans la fenêtre
     */
    $(document).on('click', ".confirm-action", function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var message = $(this).data('message');
        $('#modal-confirm').find('.btn-danger').attr('href', href);
        $('#modal-confirm').find('.modal-body').html(message);
        $('#modal-confirm').modal('show');
    });
    
    /**
     * Plugin dataTable qui permet de trier des dates Euro
     * Voir ici http://datatables.net/plug-ins/sorting#how_to_type
     * Pour l'activer sur les champs faire ajouter ça :
     *   "aoColumnDefs":[
     *       {"aTargets":["date-eu"],"sType":"date-eu","bSortable": true,"sSortDataType":"dom-text"}
     *   ],
     * 
     */
    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "date-eu-pre": function ( date ) {
            var date = date.replace(" ", "");
            
            if(date == "") date = "01/01/0001";

            if (date.indexOf('.') > 0) {
                /*date a, format dd.mn.(yyyy) ; (year is optional)*/
                var eu_date = date.split('.');
            } else if (date.indexOf('/') > 0){
                /*date a, format dd/mn/(yyyy) ; (year is optional)*/
                var eu_date = date.split('/');
            } else {
                /*date a, format dd-mn-(yyyy) ; (year is optional)*/
                var eu_date = date.split('-');
            }
            /*year (optional)*/
            if (eu_date[2])  var year = eu_date[2];
            else  var year = 0;
            /*month*/
            var month = eu_date[1];
            if (month.length == 1) month = 0+month;
            /*day*/
            var day = eu_date[0];
            if (day.length == 1) day = 0+day;
            return (year + month + day) * 1;
        },
        "date-eu-asc": function ( a, b ) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },
        "date-eu-desc": function ( a, b ) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    } );
    
    
    /**
     * Input limiter plugin
     * http://rustyjeans.com/jquery-plugins/input-limiter
     * Style overwriten
     */
    $('textarea.limited').inputlimiter({
       remText: '%n caractères restants...',
       limitText: 'Maximum autorisé : %n.'
    });
});