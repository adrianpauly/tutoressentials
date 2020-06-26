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
    let min;

    if ($('.min-word-count-value').length) {
        min = $('.min-word-count-value').text();
    } 

    console.log('min', min);

    if(min && min > 0) {

        $field.after('<div class="character-count"><span>0</span> / ' + min + ' minimum word count</div>')

    	$('[name="startQuiz"]').on('click',function(){
    		//$('.wpProQuiz_button').addClass('disabled');
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

    }



    // Hack to remove Mark Complete button 

    if ( $('#sfwd-mark-complete').length && $('.next-link').length ) {
        $('.next-link').remove();
        $('#learndash_mark_complete_button').addClass('button-to-link').attr('value', 'Next Lesson →');
    }


    // Hack to hide completed quiz questions -- sorry, Learndash is finnicky ¯\_(ツ)_/¯ 
    if ( $('.wpProQuiz_lock').text().indexOf('You passed this quiz') > -1 ) {
        $('.wpProQuiz_list').hide();
        setTimeout(function(){
            $('.wpProQuiz_text').show();
        },1000)
    }



})(jQuery); 