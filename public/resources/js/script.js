
$(document).ready(function(){
    
    /* ---------- Tooltip ---------- */
    // Init bootstrap 2 tooltip (theme tooltip can also be used)
    $('[data-toggle="tooltip"]').tooltip({});
    $('[rel="tooltip"],[data-rel="tooltip"]').tooltip({
        "placement":"bottom",
        delay: { show: 400, hide: 200 }
    });
        
        
    /**
     * BOOTSTRAP
     * Activation des dropDown
     */
    $('.dropdown-toggle[dropdown-toggle-throw!=false]').dropdown()

    
    // Look&Feel features, the submit button with this class is changed by "ongoing treatment"
    $('form').submit(function(e){
        $(this).find('.btn-submit').attr("data-loading-text", "Traitement en cours").button('loading');
    });
    $(document).keyup(function(e){
        switch(e.keyCode){
            case 27 :   // escape pressed
                $('.btn-submit').button('reset');  // reset "ongoing treatment" state
                break;
        }
    });
    
    
    $(".chzn-select").chosen();
    
    loadPopover();
    
    
    
    /**
     * Switch jquery ui datepicker to FR
     * 
     */
    $.datepicker.regional['fr-CA'] = {
        closeText: 'Fermer',
        prevText: 'Précédent',
        nextText: 'Suivant',
        currentText: 'Aujourd\'hui',
        monthNames: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin',
                'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
        monthNamesShort: ['janv.', 'févr.', 'mars', 'avril', 'mai', 'juin',
                'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'],
        dayNames: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'],
        dayNamesShort: ['dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.'],
        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
        weekHeader: 'Sem.',
        dateFormat: 'dd/mm/yy',
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['fr-CA']);
    
    
});
    
$(window).load(function() {

});


/**
 * BOOTSTRAP
 * Popover (messages d'aides)
 */
function loadPopover(){
    
    $('[rel="tooltip-bottom"]').tooltip({
        placement : 'bottom'
    })
    $('[rel="tooltip"]').tooltip({
        placement : 'top'
    });
    $('[rel="tooltip-top"]').tooltip({
        placement : 'top'
    });
    $('[rel="tooltip-right"]').tooltip({
        placement : 'right'
    });
    $('[rel="popover"]').popover({
        trigger : 'hover'
    });
}