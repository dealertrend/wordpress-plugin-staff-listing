<?php
  # Bringing required objects into scope.
  global $post , $current_user;
?>

<div id="staff-listing-comments">

<?php if( have_comments() ): ?>

  <div class="reviews-header">
    <h3>Reviews</h3>
    <strong><?php comments_number('No Reviews', 'One Reviews', '% Reviews' );?> of <?php the_title(); ?></strong>
  </div><!-- .reviews-header -->

  <div class="reviews-list">
    <ul>
      <?php wp_list_comments('type=comment&avatar_size=0'); ?>
    </ul>
  </div><!-- .reviews-list -->

  <div class="navigation">
    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
      <?php paginate_comments_links('prev_text=Previous&next_text=Next'); ?>
    <?php endif; # Are there reviews to navigate through? ?>
  </div><!-- .navigation -->

<?php endif; # I can haz reviews? ?>


<?php if( 'open' == $post->comment_status): ?>

<div id="review">
  <?php if( $current_user->ID ): ?>
    <h3>Add Reviews</h3>
    <form action="<?php echo get_option( 'siteurl' ); ?>/wp-comments-post.php" method="post" id="commentform">
      <p>
        Logged in as <a href="<?php echo get_option( 'siteurl' ); ?>/wp-admin/profile.php"><?php echo $current_user->user_nicename; ?></a>. | 
        <a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="Log out of this account">Log out &raquo;</a>
      </p>
      <p><textarea name="comment" id="comment" cols="100" rows="10" tabindex="4"></textarea></p>
      <p>
        <input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
        <?php comment_id_fields(); ?>&nbsp;<?php cancel_comment_reply_link( "Cancel reply" ); ?>
      </p>
      <?php do_action( 'comment_form' , $post->ID ); ?>
    </form>
  <?php endif; # Do we know who the user is? ?>
</div><!-- #review -->

<?php endif; # Is the staff member open for review? ?>

</div><!-- #staff-listing-comments -->

<?php get_footer(); ?>
