
$(document).ready(function(){
    
    // Librairie google qui colorie le code dans les balises <code> et <pre>
    prettyPrint();
    
    
    // Slider parallax pour les articles flash sur l'index
    $('#da-slider').cslider({
        current     : 0,    
        // index of current slide
        bgincrement : 0,   
        // increment the background position 
        // (parallax effect) when sliding
        autoplay    : true,
        // slideshow on / off
        interval    : 7000  
        // time between transitions
    });
      
      
    /**
     * BOOTSTRAP
     * Modal de deconnection
     */
    $('#logout').click(function(e){
        e.preventDefault();
        $($(this).attr('data-target')).modal('show');
    });
    

    /**
     * Animation des liens archives pour switcher entre les dates
     * liens en footer + sidebar
     */
    $("a.archives-year").click(function(e){
        e.preventDefault();

        // On récupere l'id de l'ul enfant
        var relParent = $(this).attr('rel');
        
        // on récupère l'area (sidebar) ou (footer)
        var area = $(this).attr('rel2');
        
        // On l'affiche
        $("#" + relParent).show(100);
           
        // On cache tous les autres liens !attention on gère à part la sidebar et le footer grâce au area
        $("a.archives-year-" + area).each(function(){
            if($(this).attr("rel") != relParent){
                $("#" + $(this).attr('rel')).hide(100);
            }
        });
    });



    /**
     * Redimension à la largeur de l'ecran pour le background footer
     */
    $("#footer-background-img").width($(window).width());
    
    
    /**
     * Dropawn automatique sur hover
     * ! methode jquery meilleurs que la methode css car elle supprime l'event onclick de bootstrap
     * Ouvre les .dropdown-menu au survol d'une .dropdown
     */
    jQuery('.navbar-toggle-hover ul.nav li.dropdown').hover(function() {
        jQuery(this).find('.dropdown-menu').stop(true, true).delay(0).fadeIn();
    }, function(){
        jQuery(this).find('.dropdown-menu').stop(true, true).delay(0).fadeOut();
    });
    $('.dropdown-menu a').click(function(){
        $('.dropdown-menu').hide();
    });
  
   /**
    * Ajout de flèches sur les dropdown parents
    */
    jQuery('.navbar-toggle-hover .submenu').hover(function () {
        jQuery(this).children('ul').removeClass('submenu-hide').addClass('submenu-show');
    }, function (){
        jQuery(this).children('ul').removeClass('.submenu-show').addClass('submenu-hide');
    })/*.find("a:first").append(" &rarr; ")*/;


    $(window).resize(function(){
        var largeurMin = 1700;
        var marginLeft = 380; // -380
        if( parseInt($(window).width()) > 1700){
            // Nouvelle marge = (marge minimum + (nouvelle largeur - largeur minimum)) / 2
            var nouvelleMarge = ( marginLeft + ( parseInt($(window).width()) - largeurMin) / 2 );
            //alert(nouvelleMarge);
            $("#footer-background-img").width(parseInt($(window).width()));
            $("#footer-background-img").css("margin-left", -1 * nouvelleMarge);
        }
    });
    
});
    
    
$(window).load(function() {
    
    
    var largeurMin = 1700;
    var marginLeft = 380; // -380
    if( parseInt($(window).width()) > 1700){
        // Nouvelle marge = (marge minimum + (nouvelle largeur - largeur minimum)) / 2
        var nouvelleMarge = ( marginLeft + ( parseInt($(window).width()) - largeurMin) / 2 );
        $("#footer-background-img").width(parseInt($(window).width()));
        $("#footer-background-img").css("margin-left", -1 * nouvelleMarge);
    }
    else{
        $("#footer-background-img").width(largeurMin);
        $("#footer-background-img").css("margin-left", -1 * marginLeft);
    }
});