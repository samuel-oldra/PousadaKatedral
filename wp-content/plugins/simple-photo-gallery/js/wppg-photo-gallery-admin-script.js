jQuery(document).ready(function($){
    //Add Generic Admin Dashboard JS Code in this file
    //
    //Triggers the more info toggle link
    $(".wppg_more_info_body").hide();//hide the more info on page load
    $(".wppg_more_info_anchor").click(function(){
        $(this).next(".wppg_more_info_body").animate({ "height": "toggle"});
        var toogle_char_ref = $(this).find(".wppg_more_info_toggle_char");
        var toggle_char_value = toogle_char_ref.text();
        if(toggle_char_value === "+"){
            toogle_char_ref.text("-");
        }
        else{
             toogle_char_ref.text("+");
        }
    });
    //End of more info toggle
    
});