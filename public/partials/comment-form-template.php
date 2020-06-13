<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Custom_Comments
 * @subpackage Custom_Comments/public/partials
 */
?>
<form class="custom-comments comment-form" method="post" id="commentform">
	<?php if ( $this->options['form_title'] ): ?>
        <div><?php echo $this->options['form_title']; ?></div>
	<?php endif; ?>
	<?php if ( is_user_logged_in() ):
		global $current_user;
		wp_get_current_user();
		?>
        <input type="hidden" name="name" value="<?php echo $current_user->display_name; ?>">
        <input type="hidden" name="email" value="<?php echo $current_user->user_email ?>">
	<?php else: ?>
		<?php if ( $this->options['name']['placeholder'] ): ?>
            <div class="form-item comment-form-author">
				<?php if ( $this->options['name']['label'] ): ?>
                    <label><?php echo $this->options['name']['label'] ?></label><?php endif; ?>
                <input type="text" placeholder="<?php echo $this->options['name']['placeholder'] ?>" <?php echo
				$this->options['name']['required'] ? 'required' : ''; ?> name="name">
            </div>
		<?php endif; ?>
		<?php if ( $this->options['email']['placeholder'] ): ?>
            <div class="form-item comment-form-email">
				<?php if ( $this->options['email']['label'] ): ?>
                    <label><?php echo $this->options['email']['label'] ?></label><?php endif; ?>
                <input type="email" placeholder="<?php echo $this->options['email']['placeholder'] ?>" <?php echo
				$this->options['email']['required'] ? 'required' : ''; ?> name="email">
            </div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $this->options['additional_fields'] ): foreach ( $this->options['additional_fields'] as $key => $item ) : ?>
        <div class="form-item">
			<?php if ( $item['label'] ): ?><label><?php echo $item['label']; ?></label> <?php endif; ?>
            <?php if ($item['slug']):?><input type="text" placeholder="<?php echo $item['slug']; ?>" name="<?php echo $item['slug']; ?>"> <?php endif; ?>
        </div>
	<?php endforeach;endif; ?>
	<?php if ( $this->options['text']['placeholder'] ): ?>
        <div class="form-item comment-form-comment">
			<?php if ( $this->options['text']['label'] ): ?>
                <label><?php echo $this->options['text']['label'] ?></label><?php endif; ?>
            <textarea id="custom_comments_text"
                      placeholder="<?php echo $this->options['text']['placeholder'] ?>"<?php echo
			$this->options['text']['required'] ? 'required' : ''; ?> name="text"></textarea>
        </div>
	<?php endif; ?>
	<?php if ( $this->options['send'] ): ?>
        <div class="form-item form-submit">
            <input type="submit" value="<?php echo $this->options['send'] ?>" class="custom-comment-submit">
        </div>
	<?php endif; ?>
	<?php if ( is_singular() ): ?>
        <input type="hidden" name="type" value="post">
        <input type="hidden" name="id" value="<?php echo get_queried_object()->ID ?>">
	<?php else: ?>
        <input type="hidden" name="type" value="term">
        <input type="hidden" name="id" value="<?php echo get_queried_object()->term_id ?>">
	<?php endif; ?>
    <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
    <input type="hidden" name="comment_parent" value="0">
</form>
