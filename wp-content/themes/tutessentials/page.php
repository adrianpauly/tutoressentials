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

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php if(defined( 'CMB2_LOADED' )): ?>

			<?php if ($learnpress_select == 'learnpress_single_three' & is_singular( 'lp_course' )): ?>
				<?php do_action('edumodo_lp_course_header_style_3'); ?>
		

				<?php elseif ('disable' !== $title_enable): ?>
					<?php do_action('edumodo-enable-or-disable-header-section'); ?>
				<?php endif;?>

			<?php else: ?>
				<?php if(! is_front_page()):?>
				<div class="page-details">
					<div class="container">
						<div class="row">
							<h2 class="page-title">
								<?php the_title(); ?>
							</h2>
						</div>
					</div>
				</div>
			<?php endif; ?> 
		<?php endif; ?>

	

			<div class="container">
				<?php
					while ( have_posts() ) : the_post();

						get_template_part( 'template-parts/content', 'page' );

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
				?>						
			</div><!-- .container -->
			
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
