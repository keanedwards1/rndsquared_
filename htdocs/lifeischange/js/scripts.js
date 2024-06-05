Modernizr.load([{
	// media queries for bad browsers
    test : Modernizr.mq('only all'),
    nope : '/js/respond.js'
}]);

(function($){
   	$(window).load(function(){
		

		
		
		$('#response').fadeOut();
		var $sending = false;
		
		$('#contact-send').click(function(e){
			e.preventDefault();
			
			if( $sending === false ){
				
				$sending = true;
				$(this).prop('disabled', true).html('Sending...');
				$('#response').fadeOut('fast').html('');

				var $data = $('#contact-form').serialize();

				$.ajax({
					type: "POST",
					url: "licmail.php",
					data: $data,
					success: function( data ){
						var $return = $.parseJSON( data );

						if( 'success' == $return.result ){
							$('#response').addClass('success').html('<p>Thank you, your message was sent.</p>').fadeIn();
							$('#contact-send').html('Sent!');
							return;
						}
						
						var $message = '<p>There were some problems with your message:</p><ul>';
						for(index = 0; index < $return.details.length; ++index) {
							$message += '<li>' + $return.details[index] + '</li>';
						}
						$message += '</ul>';
						$('#response').stop().html( $message ).fadeIn();
						$sending = false;
						$('#contact-send').prop('disabled', false).html('Send');
					}
				});
			}
			
		});

			
	});
})(jQuery);