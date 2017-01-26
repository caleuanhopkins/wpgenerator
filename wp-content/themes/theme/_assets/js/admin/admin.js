;(function( $ ) {

	$(document).ready(function(){

    });

    example_ajax_function = function(){
        var data =  '';
        $.ajax({
            url: ajaxurl, 
            type: "POST",
            data: {
                action: 'themeInitials_AuthorInitials_example_admin_ajax_method',
                data: data
            },
            dataType: 'JSON',
            success: function(res, status){
                console.log(res);
            },
            error: function(res,status, err){
                console.log(res);
            }
        });
    }
	
})( jQuery );
