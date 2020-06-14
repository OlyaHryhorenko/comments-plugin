<div class="wrap">
	<h2>Custom Comments</h2>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<form method="post">
						<?php
						$this->comments_object->views();
						$this->comments_object->prepare_items();
						$this->comments_object->search_box( 'search', 'search_id' );
						$this->comments_object->display(); ?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
