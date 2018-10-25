jQuery(document).ready(function () {

	$('.sss').on('change input',function(){
		if ( $('.sss').val().length <= 2 ) {
			return false;
		} else {
			$('#searchform').submit()
		}
	})

	$('.mobi').on('click',function(){
		if ( $('.header .nav').hasClass('active') ) {
			$('.header .nav').removeClass('active');
		} else {
			$('.header .nav').addClass('active');
		}
	});

	h = $('.header').height();

	$(window).scroll(function(){
        var s = $(this).scrollTop();
        $('.header .nav').removeClass('active');
        if(s > $('.header').height() + 100){
        	$('.header').addClass('fix');
        	$('.header').css('margin-bottom',h);
        }else{
        	$('.header').removeClass('fix');
        	$('.header').css('margin-bottom','0');
        }
    });

	$('.slider_home').owlCarousel({
	    loop:false,
	    nav:false,
	    autoplay:true,
	    responsive:{
	        0:{
	            items:1
	        },
	        600:{
	            items:1
	        },
	        1000:{
	            items:1
	        }
	    }
	});

	$('.slide_blog').owlCarousel({
	    loop:true,
	    margin:30,
	    nav:true,
	    responsive:{
	        0:{
	            items:1
	        },
	        600:{
	            items:2
	        },
	        1000:{
	            items:4
	        }
	    }
	});

	$('.limit a').click(function(){
		var text = $(this).html();

		if ( $(this).parent().hasClass('active') ) {
			$('.limit').removeClass('active');
			$(this).html('Читать ещё');
		} else {
			$('.limit').addClass('active');
			$(this).html('Свернуть');
		};
		return false;
	});

});