<?

class WpProQuiz_View_StartQuizButton extends WpProQuiz_View_FrontQuiz {

	private function showStartQuizBox() {
		?>
		<div class="wpProQuiz_text">

			<?php
			if ( $this->quiz->isFormActivated() && $this->quiz->getFormShowPosition() == WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START ) {
				$this->showFormBox();
			}
			?>

			<div>
				<input class="wpProQuiz_button" type="button" value="<?php
				//echo sprintf( esc_html_x( 'Start %s', 'Start Quiz Button Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) );
				echo esc_html( SFWD_LMS::get_template(
					'learndash_quiz_messages',
					array(
						'quiz_post_id'	=>	$this->quiz->getID(),
						'context' 		=> 	'quiz_start_button_label',
						'message' 		=> 	sprintf( esc_html_x( 'Start %s', 'Start Quiz Button Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) )
					)
				));
				?>"
				       name="startQuiz">
			</div>
		</div>
		<?php
	}

}