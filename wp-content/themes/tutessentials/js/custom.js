(function($) {
    $(document).ready(function(){

		// Slick init        
		$('.school-logos').slick({
		  infinite: true,
		  slidesToShow: 3,
		  slidesToScroll: 1,
		  speed: 700,
		  autoplay: true,
		  autoplaySpeed: 1500,
		  arrows: false,
		  variableWidth: true
		});   

		$('.testimonials').slick({
		  infinite: true,
		  speed: 700,
		  autoplay: true,
		  autoplaySpeed: 4000,
		  arrows: false
		});

    });
})(jQuery);