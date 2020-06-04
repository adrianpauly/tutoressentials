<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Edumodo
 */
	$post_id = edumodo_get_id();
	// Global Options
	global $edumodo_options;
	// Prefix
    $prefix = '_edumodo_';
	// Page title enable
	$title_enable = get_post_meta($post_id, $prefix . 'title_enable', true);
	// Learnpress single course page
	$learnpress_select = $edumodo_options['learnpress_select'];


// Get Privacy page content from AN
$sections_result = get_privacy('https://www.algebranation.com/wp-json/acf/v3/posts/1341');
$sections = $sections_result->acf->section;

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<div class="container">
				<?php
					while ( have_posts() ) : the_post();

						//get_template_part( 'template-parts/content', 'page' );
						include(locate_template('template-parts/content-page-tabs.php'));


						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; ?>						
			</div><!-- .container -->
			
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();