jQuery(function($) {
	
	$("a.popup1").click(function() {
			loading(); 
			setTimeout(function(){ 
				loadPopup(); 
			}, 500); 
	return false;
	});
	
	$("div.close").hover(
					function() {
						$('span.popup_tooltip').show();
					},
					function () {
    					$('span.popup_tooltip').hide();
  					}
				);
	
	$("div.close").click(function() {
		disablePopup();  
	});
	
	$(this).keyup(function(event) {
		if (event.which == 27) { 
			disablePopup();  
		}  	
	});
	
	$("div#backgroundPopup").click(function() {
		disablePopup(); 
	});
	

	function loading() {
		$("div.loader").show();  
	}
	function closeloading() {
		$("div.loader").fadeOut('normal');  
	}
	
	var popupStatus = 0; 
	
	function loadPopup() { 
		if(popupStatus == 0) { 
			closeloading(); 
			$("#Popup").fadeIn(0500); 
			$("#backgroundPopup").css("opacity", "0.7"); 
			$("#backgroundPopup").fadeIn(0001); 
			popupStatus = 1; 
		}	
	}
		
	function disablePopup() {
		if(popupStatus == 1) { 
			$("#Popup").fadeOut("normal");  
			$("#backgroundPopup").fadeOut("normal");  
			popupStatus = 0; 
		}
	}
	
	$("a.popup2").click(function() {
			loading(); 
			setTimeout(function(){ 
				loadPopup(); 
			}, 500); 
	return false;
	});
	
	$("div.close").hover(
					function() {
						$('span.popup_tooltip').show();
					},
					function () {
    					$('span.popup_tooltip').hide();
  					}
				);
	
	$("div.close").click(function() {
		disablePopup();  
	});
	
	$(this).keyup(function(event) {
		if (event.which == 27) { 
			disablePopup();  
		}  	
	});
	
	$("div#backgroundPopup").click(function() {
		disablePopup(); 
	});
	

	function loading() {
		$("div.loader").show();  
	}
	function closeloading() {
		$("div.loader").fadeOut('normal');  
	}
	
	var popupStatus = 0; 
	
	function loadPopup() { 
		if(popupStatus == 0) { 
			closeloading(); 
			$("#Popup").fadeIn(0500); 
			$("#backgroundPopup").css("opacity", "0.7"); 
			$("#backgroundPopup").fadeIn(0001); 
			popupStatus = 1; 
		}	
	}
		
	function disablePopup() {
		if(popupStatus == 1) { 
			$("#Popup").fadeOut("normal");  
			$("#backgroundPopup").fadeOut("normal");  
			popupStatus = 0; 
		}
	}
});