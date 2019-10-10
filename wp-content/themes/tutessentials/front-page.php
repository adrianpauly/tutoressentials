<?php
/**
 * Front page template
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 */

get_header(); ?>

<section class="masthead-page tutor-essentials">

    <section class="masthead container-fluid">
    	<div class="container">
	        <div class="masthead-content">
	            <h1>Easily train all your tutors in a self-paced, online environment.</h1>
	            <p>Tutor Essentials is the only web-based tutor training course endorsed by <a href="https://www.crla.net/" target="_blank">CRLA</a> (College Reading and Learning Association).</p>
	            <div class="cta">
	                <a href="https://tutormatchingservice.com/#/schools-contact/training-demo/" class="btn btn-alt">Give it a try!</a>
	                <h5 class="button-subtext">No credit card, no obligations</h5>
	            </div>
	        </div>    		
    	</div>
    </section>

    <section class="description">
      <div class="container">
        <p>Tutor Essentials was developed by Purdue University in partnership with TMS, with input from CRLA certification volunteers, Emory University, Clemson University, IUPUI, and others. The course is interactive (through videos, quizzes, and the creation of a tutoring philosophy statement), highly engaging, self-paced, and offers the tools and tactics necessary to become a great in-person or online tutor. It’s self-paced and takes 3-5 hours to complete. The entire training program can be completed in one sitting and also gives tutors the ability to save their work and complete it in several sittings.</p>
        <p class="text-center">
          <script src="https://fast.wistia.com/embed/medias/tpkuo62l9o.jsonp" async></script><script src="https://fast.wistia.com/assets/external/E-v1.js" async></script><span class="wistia_embed wistia_async_tpkuo62l9o popover=true popoverAnimateThumbnail=true popoverContent=link" style="display:inline"><a class="btn btn-primary" href="#">Tour of Tutor Essentials</a></span>
        </p>  

      </div>
    </section>

	<?php get_template_part('template-parts/content', 'school-logos' ) ?>

	<?php get_template_part('template-parts/content', 'features' ) ?>

    <section class="stats">
        <div class="stat-block">
            <h2>67</h2>
            <h3>Schools</h3>
            <h5>use Tutor Essentials</h5>
        </div>
        <div class="stat-block">
            <h2>2000+</h2>
            <h3>Tutors</h3>
            <h5>have been trained<br>in Tutor Essentials</h5>
        </div>
    </section>

	<?php get_template_part('template-parts/content', 'testimonials' ) ?>

    <section class="why-use">
        <div class="container">
            <div class="col">
                <div class="wrapper">
                    <h2>Why use Tutor Essentials?</h2>
                    <ul>
                        <li>Counts towards 4 hours of Level 1 CRLA Certification - a great way to get on track for CRLA certification for your learning center, or if you are already CRLA certified it will free up those hours of in-person training to focus on other topics.</li>
                        <li>Allows you to easily train all of your tutors without having to find a common meeting time or big enough space. Tutors complete the training on their own time and at their own pace.</li>
                        <li>Ensures high quality training for your tutors that was developed by Purdue University and supported by research in tutoring best practices.</li>
                        <li>Provides tutors with a lifetime license to the training, allowing them to return to the material at any time.</li>
                        <li>Access an administrative panel to track your tutors’ progress in completing the training and be notified when they’re done.  </li>
                        <li>No minimum purchase, with each training only $20, and annual licenses available for institutions at a lower per-tutor cost.</li>
                        <li>Tutors receive a Trained in Tutor Essentials Badge</li>
                    </ul>
                    <p class="text-center">
                        <span class="badge">
                            <img src="<?php echo get_stylesheet_directory_uri() ?>/img/tutor-essentials-badge.png" alt="Trained in Tutor Essentials badge" width="150" height="67"></p> 
                        </span>
                </div>
            </div>
        </div>

    </section>

    <section class="pricing">
      <div class="container">
        <h2>Pricing</h2>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
              <p>Tutor Essentials is available at the individual price of $20/training. If you are looking to train larger numbers of tutors, you may want to consider our annual licenses. With this option, we will automatically add your desired number of trainings to your administrative panel for you to access all year long. These pricing options are for the fiscal year (July 1st to June 30th), however if this causes challenges for you, just let us know and we can work out the best option for your institution.</p>
              <div class="flex-row price-options">
                  <div class="col individual">
                      <h4>Individual</h4>
                      <h3><span class="amount">$20</span><span class="per-year">/year</span></h3>
                  </div>
                  <div class="col">
                      <h5>Up to</h5>
                      <h4>25</h4>
                      <p>Tutors</p>
                      <h3><span class="amount">$475</span><span class="per-year">/year</span></h3>
                  </div>
                  <div class="col">
                      <h5>Up to</h5>
                      <h4>50</h4>
                      <p>Tutors</p>
                      <h3><span class="amount">$895</span><span class="per-year">/year</span></h3>
                  </div>
                  <div class="col">
                      <h5>Up to</h5>
                      <h4>100</h4>
                      <p>Tutors</p>
                      <h3><span class="amount">$1695</span><span class="per-year">/year</span></h3>
                  </div>
                  <div class="col">
                      <h5>Up to</h5>
                      <h4>150</h4>
                      <p>Tutors</p>
                      <h3><span class="amount">$2395</span><span class="per-year">/year</span></h3>
                  </div>
                  <div class="col">
                      <h5>Up to</h5>
                      <h4>200</h4>
                      <p>Tutors</p>
                      <h3><span class="amount">$2995</span><span class="per-year">/year</span></h3>
                  </div>
              </div>
              <div class="cta">
                <p>Want to learn more about this course?</p>
                <p><a href="https://tutormatchingservice.com/#/schools-contact/training-demo/" class="btn btn-primary">Request a Free Demo</a></p>   
              </div>
            </div>
        </div>
      </div>
    </section>

<!--     <section class="training-faq page-faq-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h2>Want more answers?</h2>
                    <h3>Check out our Frequently Asked Questions!</h3>
                </div>
                <div class="col-md-4 faq-btn">
                    <a href="#/about/faq" class="btn btn-primary">Go to FAQs</a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <p>For more information, contact <a href="mailto:Schools@TutorMatchingService.com">Schools@TutorMatchingService.com</a></p>
                </div>
            </div>
                </div>
            </div>
        </div>
    </section>
 -->


</section>

<?php
get_footer();
