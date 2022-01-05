require([
    "jquery",
    "jquery/ui"
], function($v){
//<![CDATA[
    $v = jQuery.noConflict();
    $v(document).ready(function() 
    { 
    
     jQuery.ajax({
        showLoader: true, 
        url: "admin/videoupload/Uploadvideoinfo/Index", 
        data: {currentproduct:"1"},
        type: "POST", 
        dataType: 'json'
    }).done(function (data) { 
        alert(data); console.log(data); 
    });
    
    });
//]]>
});
