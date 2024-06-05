Modernizr.load([{
	// media queries for bad browsers
    test : Modernizr.mq('only all'),
    nope : '/js/respond.js'
}]);

(function($){
   	$(window).load(function(){
		
		var $welcome = $('#welcome');
		
		var $isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
		
		function resize_panel(){
			var $win_height = $(window).innerHeight();
			//var $welcome_height = $welcome.find('.container').height();		
			
			var $welcome_height = $welcome.height();
			
			var $content_height = $welcome_height > $win_height ? $welcome_height + 60 : $win_height;
			
			if($isIOS){
				$content_height = $content_height + 60;
			}
			
			$welcome.animate( {'height':$content_height}, 'fast' );
		}
		
		resize_panel();
		
		
		
		$('.header-nav a, .nav a, a.inline-link').not('.thumbnail a').click(function(e){
			e.preventDefault();
		    $('html, body').animate({
		        scrollTop: $( $.attr(this, 'href') ).offset().top
		    }, 500);
		    return false;
		});
		
		
		function debounce(fn, delay) {
			var timer = null;
			return function () {
				var context = this, args = arguments;
				clearTimeout(timer);
				timer = setTimeout(function () {
					fn.apply(context, args);
				}, delay);
			};
		}
		
		$(window).scroll(debounce(function(){
			if($(this).scrollTop()>300){
		        $('.menu-bar').removeClass('folded');
		    } else {
				$('.menu-bar').addClass('folded');
			}
		}, 250));
		
		
		
		
		
		$('.expander-title').click(function(){
			$(this).toggleClass('active').next().slideToggle();
		});
		
		
		
		
		
		var $proj_headers = $('.project-header');
		
		$proj_headers.click(function(){
			
			var $ph = $(this);
		
			$('.project-header').not( $ph ).removeClass('active').next().slideUp('fast', function(){});
			
			$ph.toggleClass('active').next().slideToggle('fast', function(){});
			
			setTimeout(function(){
				$('html, body').animate({
			        scrollTop: $ph.offset().top - 60
			    }, 500);
			}, 300);

			
		});
		
		var $thumbs = $('.header-nav .thumbnail a');
		
		$thumbs.click(function(event){
			event.preventDefault();
			//console.log( $(this).data('project') );
			//console.log( $proj_headers.eq( $(this).index() ) )
			$proj_headers.eq( $(this).data('project') ).trigger('click');
		});
		
		
		
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
					url: "rndmail.php",
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