<?php
$comment_list = new Comments_List();
$table        = $_GET['type'] == 'post' ? $this->wpdb->prefix . 'comments' : $this->wpdb->prefix . 'term_comments';
$comment      = $comment_list->get_comment( $_GET['type'], $_GET['custom_comment'] );
?>
    <form name="post" action='admin-post.php' method="post" id="post">
        <div class="wrap">
            <h1><?php _e( 'Edit Comment' ); ?></h1>

            <div id="poststuff">
                <input type="hidden" name="action" value="edited_custom_comment"/>
                <input type="hidden" name="comment_ID" value="<?php echo esc_attr( $comment->comment_ID ); ?>"/>
                <input type="hidden"
                       name="<?php echo $_GET['type'] == 'post' ? 'comment_post_ID' : 'comment_term_ID'; ?>"
                       value="<?php echo esc_attr( $comment->comment_ID ); ?>"/>

                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content" class="edit-form-section edit-comment-section">
                        <div id="namediv" class="stuffbox">
                            <div class="inside">
                                <h2 class="edit-comment-author"><?php _e( 'Author' ); ?></h2>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php _e( 'Comment Author' ); ?></legend>
                                    <table class="form-table editcomment">
                                        <tbody>
                                        <tr>
                                            <td class="first"><label for="name"><?php _e( 'Name' ); ?></label></td>
                                            <td><input type="text" name="comment_author" size="30"
                                                       value="<?php echo esc_attr( $comment->comment_author ); ?>"
                                                       id="name"/></td>
                                        </tr>
                                        <tr>
                                            <td class="first"><label for="email"><?php _e( 'Email' ); ?></label></td>
                                            <td>
                                                <input type="text" name="comment_author_email" size="30"
                                                       value="<?php echo $comment->comment_author_email; ?>"
                                                       id="email"/>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </fieldset>
                            </div>
                        </div>
                        <div id="postdiv" class="postarea">
							<?php
							echo '<label for="content" class="screen-reader-text">' . __( 'Comment' ) . '</label>';
							$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
							wp_editor(
								$comment->comment_content,
								'content',
								array(
									'media_buttons' => false,
									'tinymce'       => false,
									'quicktags'     => $quicktags_settings,
								)
							);
							wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
							?>
                        </div>
                    </div><!-- /post-body-content -->

                    <div id="postbox-container-1" class="postbox-container">
                        <div id="submitdiv" class="stuffbox">
                            <h2><?php _e( 'Status' ); ?></h2>
                            <div class="inside">
                                <div class="submitbox" id="submitcomment">
                                    <div id="minor-publishing">

                                        <div id="misc-publishing-actions">

                                            <fieldset class="misc-pub-section misc-pub-comment-status"
                                                      id="comment-status-radio">
                                                <legend class="screen-reader-text"><?php _e( 'Comment status' ); ?></legend>
                                                <label><input
                                                            type="radio"<?php checked( $comment->comment_approved, '1' ); ?>
                                                            name="comment_status"
                                                            value="1"/><?php _ex( 'Approved', 'comment status' ); ?>
                                                </label><br/>
                                                <label><input
                                                            type="radio"<?php checked( $comment->comment_approved, '0' ); ?>
                                                            name="comment_status"
                                                            value="0"/><?php _ex( 'Pending', 'comment status' ); ?>
                                                </label><br/>
                                                <label><input
                                                            type="radio"<?php checked( $comment->comment_approved, 'spam' ); ?>
                                                            name="comment_status"
                                                            value="spam"/><?php _ex( 'Spam', 'comment status' ); ?>
                                                </label>
                                            </fieldset>

                                            <div class="misc-pub-section curtime misc-pub-curtime">
												<?php
												$datef = __( 'M j, Y @ H:i' );
												?>
                                                <span id="timestamp">
                                                <?php
                                                printf(
	                                                __( 'Submitted on: %s' ),
	                                                '<b>' . date_i18n( $datef, strtotime( $comment->comment_date ) ) . '</b>'
                                                );
                                                ?>
                                                </span>
                                            </div>

											<?php
											$post_id = $comment->comment_post_ID;
											if ( $_GET['type'] == 'post' ) {
												if ( current_user_can( 'edit_post', $post_id ) ) {
													$post_link = "<a href='" . esc_url( get_edit_post_link( $post_id ) ) . "'>";
													$post_link .= esc_html( get_the_title( $post_id ) ) . '</a>';
												} else {
													$post_link = esc_html( get_the_title( $post_id ) );
												}

											} else {
												$term = get_term( $post_id );
												if ( current_user_can( 'edit_post', $post_id ) ) {
													$post_link = "<a href='" . esc_url( get_edit_term_link( $term->term_id, $term->taxonomy ) ) . "'>";
													$post_link .= esc_html( $term->name ) . '</a>';
												} else {
													$post_link = esc_html( $term->name );
												}
											}

											?>

                                            <div class="misc-pub-section misc-pub-response-to">
												<?php
												printf(
												/* translators: %s: post link */
													__( 'In response to: %s' ),
													'<b>' . $post_link . '</b>'
												);
												?>
                                            </div>

											<?php
											if ( $comment->comment_parent ) :
												$parent = get_comment( $comment->comment_parent );
												if ( $parent ) :
													$parent_link = esc_url( get_comment_link( $parent ) );
													$name = get_comment_author( $parent );
													?>
                                                    <div class="misc-pub-section misc-pub-reply-to">
														<?php
														printf(
														/* translators: %s: comment link */
															__( 'In reply to: %s' ),
															'<b><a href="' . $parent_link . '">' . $name . '</a></b>'
														);
														?>
                                                    </div>
												<?php
												endif;
											endif;
											?>

                                        </div>
                                        <div class="clear"></div>
                                    </div>

                                    <div id="major-publishing-actions">
                                        <div id="delete-action">
											<?php echo '<a class="submitdelete deletion" href="?page=custom_comments_list&action=move_to_trash&comment_id' . $comment->comment_ID . '&type=' . $_GET['type'] . '">' . ( ! EMPTY_TRASH_DAYS ? __( 'Delete Permanently' ) : __( 'Move to Trash' ) ) . "</a>\n"; ?>
                                        </div>
                                        <div id="publishing-action">
											<?php submit_button( __( 'Update' ), 'primary large', 'save', false ); ?>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /submitdiv -->
                    </div>

                    <div id="postbox-container-2" class="postbox-container">
						<?php
						/** This action is documented in wp-admin/includes/meta-boxes.php */
						do_action( 'add_meta_boxes', 'comment', $comment );

						/**
						 * Fires when comment-specific meta boxes are added.
						 *
						 * @param WP_Comment $comment Comment object.
						 *
						 * @since 3.0.0
						 *
						 */
						do_action( 'add_meta_boxes_comment', $comment );

						do_meta_boxes( null, 'normal', $comment );

						$referer = wp_get_referer();
						?>
                    </div>

                    <input type="hidden" name="c" value="<?php echo esc_attr( $comment->comment_ID ); ?>"/>
                    <input type="hidden" name="p" value="<?php echo esc_attr( $comment->comment_post_ID ); ?>"/>
                    <input type="hidden" name="table" value="<?php echo esc_attr( $table ); ?>"/>
                    <input type="hidden" name="type" value="<?php echo esc_attr( $_GET['type'] ); ?>">
                    <input type="hidden" name="action" value="update_comment">
                    <input name="referredby" type="hidden" id="referredby"
                           value="<?php echo $referer ? esc_url( $referer ) : ''; ?>"/>
					<?php wp_original_referer_field( true, 'previous' ); ?>
                    <input type="hidden" name="noredir" value="1"/>

                </div><!-- /post-body -->
            </div>
        </div>
    </form>

<?php if ( ! wp_is_mobile() ) : ?>
    <script type="text/javascript">
        try {
            document.post.name.focus();
        } catch (e) {
        }
    </script>
<?php
endif;
