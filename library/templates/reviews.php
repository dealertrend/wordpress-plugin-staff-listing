<?php global $post; global $current_user; ?>

<?php if( have_comments() ): ?>

  <h3 class="comments-label">Comments</h3>
  <strong><?php comments_number('No Responses', 'One Response', '% Responses' );?> to &#8220;<?php the_title(); ?>&#8221;</strong>
  <ol class="comment-list">
    <?php wp_list_comments('type=comment&avatar_size=0'); ?>
  </ol>

  <div class="navigation">
    <div class="alignleft"><?php previous_comments_link() ?></div>
    <div class="alignright"><?php next_comments_link() ?></div>
  </div>
  
<?php else: ?>

  <?php if( 'open' == $post->comment_status): ?>

  <?php else: ?>
    <p class="nocomments">Reviews are closed.</p>
  <?php endif; ?>

<?php endif; ?>

<?php if( 'open' == $post->comment_status): ?>

  <div id="respond">
    <h3>Add Reviews</h3>
      <?php if( $current_user->ID ): ?>
        <form action="<?php echo get_option( 'siteurl' ); ?>/wp-comments-post.php" method="post" id="commentform">
          <p> 
            Logged in as <a href="<?php echo get_option( 'siteurl' ); ?>/wp-admin/profile.php"><?php echo $current_user->user_nicename; ?></a>.
            <a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="Log out of this account">Log out &raquo;</a>
          </p>

          <p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>

          <p> 
            <input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
            <?php comment_id_fields(); ?>&nbsp;<?php cancel_comment_reply_link( "Cancel reply" ); ?>
          </p>

          <?php do_action( 'comment_form' , $post->ID ); ?>
      </form>
    </div>
  <?php endif; ?>

<?php endif; ?>

  </div>
 </div>
</div>

<?php get_footer(); exit; ?>
