<?php
/**
 * Template name: API Test

 */


get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<div class="container">
				<?php
					while ( have_posts() ) : the_post();

						get_template_part( 'template-parts/content', 'page' );


						$data = 'nina.mulkey@gmail.com';
						
						$apiresult = get_tutor_philosophy($data);

						echo '<pre>';
						var_dump($apiresult);
						echo '</pre>';

					endwhile; // End of the loop.
				?>						
			</div><!-- .container -->
			
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
