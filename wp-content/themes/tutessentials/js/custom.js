(function($) {

	$(document).foundation()


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


    // Tabs fix
    $('#privacy-policy').addClass('is-active');


    // Expandable text
    $('a[data-toggle=more-info]').on('click',function() {
    	$('#more-info').slideToggle();
    });


    // Word count

    const $field = $('textarea.wpProQuiz_questionEssay');
    let min = 3;


    $field.after('<div class="character-count"><span>0</span> / ' + min + ' minimum word count</div>')

	$('[name="startQuiz"]').on('click',function(){
		$('.wpProQuiz_button').addClass('disabled');
	});

    $field.on('keyup',function(e){

    	// Count words
    	var wordSplit = $(this).val().split(/\s+/);
    	var wordCount = 0;

    	// but skip spaces
    	wordSplit.forEach((el)=>{
    		if (el !== "") wordCount++;
    	});

    	$('.character-count span').text(wordCount);

    	if(wordCount >= min) {
    		$('.character-count').addClass('good');
			$('.wpProQuiz_button').removeClass('disabled');
    	} else {
			$('.wpProQuiz_button').addClass('disabled');
    		$('.character-count').removeClass('good');
    	}

    });




})(jQuery);