$(document).ready(function(){
    
    /* ---------- Login Box Styles ---------- */
    // Overwrite from custom.js theme
    if($(".login-box")){
        $(".special-prepend").focus(function() {
            $(this).parent(".input-prepend").addClass("input-prepend-focus");
        });

        $(".special-prepend").focusout(function() {
            $(this).parent(".input-prepend").removeClass("input-prepend-focus");
        });
    }
    
});