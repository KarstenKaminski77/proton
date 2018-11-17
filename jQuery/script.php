<script type="text/javascript">
jQuery(function($) {
	
	$("a.popup<?php echo $x; ?>").click(function() {
			loading(); 
			setTimeout(function(){ 
				loadPopup(); 
			}, 500); 
	return false;
	});
	
	$("div.close<?php echo $x; ?>").hover(
					function() {
						$('span.popup_tooltip<?php echo $x; ?>').show();
					},
					function () {
    					$('span.popup_tooltip<?php echo $x; ?>').hide();
  					}
				);
	
	$("div.close<?php echo $x; ?>").click(function() {
		disablePopup();  
	});
	
	$(this).keyup(function(event) {
		if (event.which == 27) { 
			disablePopup();  
		}  	
	});
	
	$("div#backgroundPopup<?php echo $x; ?>").click(function() {
		disablePopup(); 
	});
	

	function loading() {
		$("div.loader<?php echo $x; ?>").show();  
	}
	function closeloading() {
		$("div.loader<?php echo $x; ?>").fadeOut('normal');  
	}
	
	var popupStatus = 0; 
	
	function loadPopup() { 
		if(popupStatus == 0) { 
			closeloading(); 
			$("#Popup<?php echo $x; ?>").fadeIn(0500); 
			$("#backgroundPopup<?php echo $x; ?>").css("opacity", "0.7"); 
			$("#backgroundPopup<?php echo $x; ?>").fadeIn(0001); 
			popupStatus = 1; 
		}	
	}
		
	function disablePopup() {
		if(popupStatus == 1) { 
			$("#Popup<?php echo $x; ?>").fadeOut("normal");  
			$("#backgroundPopup<?php echo $x; ?>").fadeOut("normal");  
			popupStatus = 0; 
		}
	}
	
});
</script>