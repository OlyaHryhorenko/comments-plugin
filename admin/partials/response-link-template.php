<?php if ( $item['type'] == 'post' ) : ?>

	<div class="response-links">
		<a href="<?php echo get_edit_post_link( $item['obj_id'] ); ?>"
		   class="comments-edit-item-link"><?php echo get_the_title( $item['obj_id'] ); ?></a>
		<a href="<?php echo get_the_permalink( $item['obj_id'] ); ?>" class="comments-view-item-link">View Post</a>

	</div>
<?php else : ?>
	<div class="response-links">
		<a href="<?php echo get_edit_term_link( get_term( $item['obj_id'] ), get_term( $item['obj_id'] )->taxonomy ); ?>"
		   class="comments-edit-item-link"><?php echo get_term( $item['obj_id'] )->name; ?></a>
		<a href="<?php echo get_term_link( get_term( $item['obj_id'] ), get_term( $item['obj_id'] )->taxonomy ); ?>"
		   class="comments-view-item-link">View Term</a>
	</div>
<?php endif; ?>
