<strong><img alt="" src="<?php echo get_avatar_url( $item['user_id'] ); ?>" class="avatar avatar-32 photo" height="32"
             width="32"><span class="c_comment_author"><?php echo $item['comment_author']; ?></span></strong>
<br><a class="c_comment_email"
       href="mailto:<?php echo $item['comment_author_email']; ?>"><?php echo $item['comment_author_email']; ?></a>
<br><a href="/"><?php echo $item['comment_author_IP'] ?></a>
<input type="hidden" class="c_comment_ID" value="<?php echo $item['ID']; ?>">