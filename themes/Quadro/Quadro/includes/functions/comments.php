<?php if ( ! function_exists( 'et_custom_comments_display' ) ) :
function et_custom_comments_display($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div class="bottom_bg">
		   <div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
				<div class="alignleft authordata">
					<?php echo get_avatar($comment,$size='60'); ?>
					
					<div class="clear"></div>
					<div class="comment-author vcard">
						<?php printf('<span class="fn">%s</span>', get_comment_author_link()) ?><br/>
						<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php echo(get_comment_date()) ?>
		</a><?php edit_comment_link(esc_html__('(Edit)','Quadro'),'  ','') ?></div>
					</div>
				</div>
				
				<div class="comment-wrap">
					
					<?php if ($comment->comment_approved == '0') : ?>
						<em class="moderation"><?php esc_html_e('Your comment is awaiting moderation.','Quadro') ?></em>
						<br />
					<?php endif; ?>
					
					<div class="comment-content"><?php comment_text() ?></div> <!-- end comment-content-->
					<div class="reply-container"><?php comment_reply_link(array_merge( $args, array('reply_text' => esc_html__('Reply','Quadro'),'depth' => $depth, 'max_depth' => $args['max_depth']))) ?></div>
				</div> <!-- end comment-wrap-->  
			</div> <!-- end comment-body-->
		</div> <!-- end .bottom_bg-->
	
<?php }
endif; ?>