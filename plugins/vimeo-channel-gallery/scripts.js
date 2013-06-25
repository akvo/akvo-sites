jQuery(document).ready(function($) {
	var f = $('.vmcplayerdiv iframe'),
	url = f.attr('src').split('?')[0],
	IframeColor = f.attr('data-color'),
	action;

	// Listen for messages from the player
	if (window.addEventListener){
		window.addEventListener('message', onMessageReceived, false);
	}
	else {
		window.attachEvent('onmessage', onMessageReceived, false);
	}

	// Handle messages received from the player
	function onMessageReceived(e) {
		var data = JSON.parse(e.data);

		switch (data.event) {
			case 'ready':
			onReady();
			break;
		}
	}

	// Call the API when a button is pressed
	$('.vmgallery .vmcthumb').click(function(){
		var currentIframeId = $(this).attr('data-playerid');
		var currentSrc = $(this).attr('href');
		var currentvideoId = currentSrc.substring(currentSrc.lastIndexOf('/') + 1);

		f.attr('id', currentIframeId);

		f.attr('src', 'http://player.vimeo.com/video/' + currentvideoId + '?api=1&player_id=' + currentIframeId + '&color=' + IframeColor);

		action = 'play';
		
		//Scroll
		checkIfInView($('#' + currentIframeId));
		return false;
	});


	// Helper function for sending a message to the player
	function post(action, value) {
		var data = { method: action };
		if (value) {
			data.value = value;
		}
		f[0].contentWindow.postMessage(JSON.stringify(data), url);
	}

	function onReady() {
		if(action){
			post(action);
		}
	}

	//Scroll to element only if not in view - jQuery
	//http://stackoverflow.com/a/10130707/1504078
	function checkIfInView(element){
		if($(element).position()){
			if($(element).position().top < $(window).scrollTop()){
			//scroll up
			$('html,body').animate({scrollTop:$(element).position().top - 10}, 500);
		}
		else if($(element).position().top + $(element).height() > $(window).scrollTop() + (window.innerHeight || document.documentElement.clientHeight)){
			//scroll down
			$('html,body').animate({scrollTop:$(element).position().top - (window.innerHeight || document.documentElement.clientHeight) + $(element).height() + 10}, 500);}
		}
	}

});//]]>  