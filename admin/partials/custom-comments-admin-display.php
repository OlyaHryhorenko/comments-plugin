<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Custom_Comments
 * @subpackage Custom_Comments/admin/partials
 */
?>
<h2><?php echo get_admin_page_title(); ?></h2>
<p>Настройки формы комментариев</p>

<div class="form-name">
	<form action="options.php" method="post" name="options">

		<?php settings_fields( $this->plugin_name ); ?>
		<table class="custom-comment_options">
			<tr>
				<td></td>
				<td><strong>Label</strong></td>
				<td><strong>Placeholder</strong></td>
				<td><strong>Required?</strong></td>
			</tr>
			<tr>
				<td><label> Название формы на сайте</label></td>
				<td><input type="text" name="custom_comments[form_title]"
						   placeholder="Название формы на сайте"
						   value="<?php echo $this->options['form_title']; ?>"/></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td><label>Ваше имя</label></td>
				<td><input type="text"
						   name="custom_comments[name][label]" placeholder="Ваше имя"
						   value="<?php echo $this->options['name']['label']; ?>"/></td>
				<td>
					<input type="text"
						   name="custom_comments[name][placeholder]" placeholder="Имя (placeholder)"
						   value="<?php echo $this->options['name']['placeholder']; ?>"/>
				</td>
				<td><input type="checkbox" name="custom_comments[name][required]" value="1" 
				<?php
				checked( $this->options['name']['required'], 1 );
				?>
					></td>
			</tr>
			<tr>
				<td>
					<label>Ваше e-mail адрес</label>
				</td>
				<td>
					<input type="text"
						   name="custom_comments[email][label]" placeholder="Ваше e-mail адрес"
						   value="<?php echo $this->options['email']['label']; ?>"/>
				</td>
				<td>
					<input type="text"
						   name="custom_comments[email][placeholder]" placeholder="e-mail (placeholder)"
						   value="<?php echo $this->options['email']['placeholder']; ?>"/>
				</td>
				<td><input type="checkbox" name="custom_comments[email][required]" value="1" 
				<?php
				checked( $this->options['email']['required'], 1 );
				?>
					></td>
			</tr>
			<tr>
				<td><label>Текст комментария</label></td>
				<td><input type="text"
						   name="custom_comments[text][label]" placeholder="Текст комментария"
						   value="<?php echo $this->options['text']['label']; ?>"/></td>
				<td>
					<input type="text"
						   name="custom_comments[text][placeholder]" placeholder="Текст комментария (placeholder)"
						   value="<?php echo $this->options['text']['placeholder']; ?>"/>
				</td>
				<td><input type="checkbox" name="custom_comments[text][required]" value="1" 
				<?php
				checked( $this->options['text']['required'], 1 );
				?>
					></td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<!--            <tr>-->
			<!--                <td><strong>Additional fields</strong></td>-->
			<!--            </tr>-->
			<!--            <tbody class="repeater">-->
			<!--            <tr>-->
			<!--                <td></td>-->
			<!--                <td></td>-->
			<!--                <td><a href="#" class=" button-primary-->
			<!--                            repeater-add-btn"><span class="dashicons dashicons-plus"></span></a></td>-->
			<!--            </tr>-->
			<!--			-->
			<?php
			// foreach ( $this->options['additional_fields'] as $key => $item ):;
			?>
			<!--                <tr>-->
			<!--                    <td><input type="text" name="custom_comments[additional_fields][-->
			<?php // echo $key ?><!--][label]"-->
			<!--                               data-number="--><?php // echo $key; ?><!--" placeholder="Field label"-->
			<!--                               value="--><?php // echo $item['label']; ?><!--"></td>-->
			<!--                    <td><input type="text" name="custom_comments[additional_fields][-->
			<?php // echo $key ?><!--][slug]"-->
			<!--                               data-number="--><?php // echo $key; ?><!--" placeholder="Field slug"-->
			<!--                               value="--><?php // echo $item['slug']; ?><!--"></td>-->
			<!--                    <td><a href="#" class="button remove-btn"><span class="dashicons dashicons-no"></span></a></td>-->
			<!--                </tr>-->
			<!--			--><?php // endforeach; ?>
			<!--			--><?php // if ( empty( $this->options['additional_fields'] ) ): ?>
			<!--                <tr>-->
			<!--                    <td><input type="text" name="custom_comments[additional_fields][0][label]" data-number="0"-->
			<!--                               placeholder="Field label"-->
			<!--                               value=""></td>-->
			<!--                    <td><input type="text" name="custom_comments[additional_fields][0][slug]" data-number="0"-->
			<!--                               placeholder="Field slug"-->
			<!--                               value=""></td>-->
			<!--                    <td><a href="#" class="button remove-btn"><span class="dashicons dashicons-no"></span></a></td>-->
			<!--                </tr>-->
			<!--			--><?php // endif; ?>
			<!--            </tbody>-->
			<tr>
				<td><label>Отправить</label></td>
				<td><input type="text"
						   name="custom_comments[send]" placeholder="Отправить"
						   value="<?php echo $this->options['send']; ?>"/></td>
				<td></td>
			</tr>
		</table>
		<h4>Переводы ошибок валидации</h4>
		<table class="custom-comment_options">
			<tr>
				<td><label>Ошибка валидации в имени</label></td>
				<td><input type="text" name="custom_comments[error_name]"
						   value="<?php echo $this->options['error_name']; ?>"></td>
			</tr>
			<tr>
				<td><label>Ошибка валидации в email</label></td>
				<td><input type="text" name="custom_comments[error_email]"
						   value="<?php echo $this->options['error_email']; ?>"></td>
			</tr>
			<tr>
				<td><label>Ошибка валидация в тексте</label></td>
				<td><input type="text" name="custom_comments[error_text]"
						   value="<?php echo $this->options['error_text']; ?>"></td>
			</tr>
			<tr>
				<td><label>Комментарий успешно отправлен</label></td>
				<td><input type="text" name="custom_comments[success_text]"
						   value="<?php echo $this->options['success_text']; ?>"></td>
			</tr>
			<tr>
				<td></td>
			</tr>

		</table>

		<h4>Дополнительные функции</h4>
		<table class="custom-comment_options">
			<tr>
				<td><label>Отключить скрипт js</label></td>
				<td>
					<input type="checkbox" name="custom_comments[disable_js]"
						   value="1" <?php checked( ! empty( $this->options['disable_js'] ), 1 ); ?> />
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td><input type="submit" value="Save" class="button button-primary"></td>
			</tr>
		</table>
	</form>
</div>
