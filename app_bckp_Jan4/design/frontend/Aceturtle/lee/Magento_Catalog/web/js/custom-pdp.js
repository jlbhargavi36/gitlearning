require(['jquery','Magento_Swatches/js/swatch-renderer'],function($,swatch){
     jQuery(document).on('swatch.initialized', function() {
	 var redirect_color = jQuery("#redirect_color").val();
                if(redirect_color != "NA") {
                         if(jQuery("[data-option-id]").length != 0) {
				if(jQuery("[data-option-id="+redirect_color+"]").hasClass('selected')){ 
					 jQuery('.swatch-option.text:first-child').trigger( "click" );			
				}
				else{
                             		jQuery("[data-option-id="+redirect_color+"]").trigger('click');  
					 jQuery('.swatch-option.text:first-child').trigger( "click" );
				} 
                         }                  
 		 }
	
});
});
