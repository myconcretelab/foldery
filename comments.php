<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to twentytwelve_comment() which is
 * located in the functions.php file.
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area nopaddingleft nopaddingright">
	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
			<?php
				printf( _n( 'Comment (%1$s)', 'Comments (%1$s)', get_comments_number(), 'foldery' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h3>

		<ol class="comment-list">
            <?php
                wp_list_comments( array(
                    'style'      => 'ol',
                    'short_ping' => true,
                    'avatar_size' => 100,
                    'callback' => 'cms_comment_form'
                ) );
            ?>
        </ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="navigation" role="navigation">
			<h1 class="assistive-text section-heading"><?php esc_html_e( 'Comment navigation', 'foldery' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'foldery' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'foldery' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<?php
		/* If there are no comments and comments are closed, let's leave a note.
		 * But we only want the note on posts and pages that had comments in the first place.
		 */
		if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="nocomments"><?php esc_html_e( 'Comments are closed.' , 'foldery' ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php //comment_form(); ?>
	<?php
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name__mail' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$args = array(
			'id_form'           => 'commentform',
			'id_submit'         => 'submit',
			'title_reply'       => esc_html__( 'Post a comment', 'foldery' ),
			'title_reply_to'    => esc_html__( 'Leave a Reply to %s', 'foldery' ),
			'cancel_reply_link' => esc_html__( 'Cancel Reply', 'foldery' ),
			'label_submit'      => esc_html__( 'Submit', 'foldery' ),

			'comment_field' =>  '<p class="comment-form-comment col-xs-12 co-sm-12 col-md-12 col-lg-12 nopaddingleft nopaddingright"><textarea id="comment" name="comment" cols="45" rows="8" placeholder="'.esc_html__('Write your comment here.', 'foldery' ).'" aria-required="true">' .
			'</textarea></p>',
			'comment_notes_before' => '',
			'fields' => apply_filters( 'comment_form_default_fields', array(

					'author' =>
					'<p class="comment-form-author col-xs-12 co-sm-12 col-md-6 col-lg-6 nopaddingleft">'.
					'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
					'" size="30"' . $aria_req . ' placeholder="'.esc_html__('Name*', 'foldery' ).'"/></p>',

					'email' =>
					'<p class="comment-form-email col-xs-12 co-sm-12 col-md-6 col-lg-6 nopaddingright">'.
					'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
					'" size="30"' . $aria_req . ' placeholder="'.esc_html__('Email*', 'foldery' ).'"/></p>',

					'url' =>
					'<p class="comment-form-url col-xs-12 co-sm-12 col-md-12 col-lg-12 nopaddingleft nopaddingright">'.
					'<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
					'" size="30" placeholder="'.esc_html__('Website', 'foldery' ).'"/></p>'
			)
			),
	);
	comment_form($args);
	?>
</div><!-- #comments .comments-area -->