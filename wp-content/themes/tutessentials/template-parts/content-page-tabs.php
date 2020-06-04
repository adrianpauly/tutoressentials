	<div class="page-content page-tabs">

        <div class="tabs-container">
          <div class="row column">
            <ul class="tabs" data-tabs data-deep-link="true" id="agreement-page" data-deep-link-smudge="true">
				<? $i = 1 ?>
              	<? foreach($sections as $section) :
					
					$slug = sanitize_title($section->title); ?>

                 	<li class="tabs-title<?= $i == 1 ? ' is-active' : '' ?>">
                 		<a href="#<?= $slug ?>" <? echo $i == 1 ? '' : 'data-tabs-target="' . $slug . '"'  ?>><?= $section->title ?></a>
                 	</li>

              	<?php $i++; endforeach; ?>

            </ul>
          </div>
        </div>

		<div class="tabs-content" data-tabs-content="agreement-page">
          	<?php foreach($sections as $section) :
				
				$slug = sanitize_title($section->title); ?>
				<div class="tabs-panel" id="<?= $slug ?>">
					<div class="row column">
						<h2><?= $section->title ?></h2>
						<?= $section->content ?>
					</div>
				</div>
			<?php $i++; endforeach; ?>
		</div>

	</div>