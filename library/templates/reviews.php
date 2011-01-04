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
    <h3>Reviews</h3>
      <?php if( get_option('comment_registration') && !$current_user->ID ): ?>
        <p>You must be <a href="<?php echo get_option( 'siteurl' ); ?>/wp-login.php?redirect_to=<?php echo urlencode( get_permalink() ); ?>">logged in</a> to post a comment.</p></div>
      <?php else: ?>
        <form action="<?php echo get_option( 'siteurl' ); ?>/wp-comments-post.php" method="post" id="commentform">
          <?php if( $current_user->ID ): ?>
            <p> 
              Logged in as <a href="<?php echo get_option( 'siteurl' ); ?>/wp-admin/profile.php"><?php echo $current_user->user_nicename; ?></a>.
              <a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="Log out of this account">Log out &raquo;</a>
            </p>
      <?php else: ?>

        <p> 
          <input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
          <label for="author"><small>Name <?php if ($req) echo "(required)"; ?></small></label>
        </p>

        <p> 
          <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
          <label for="email"><small>Mail (will not be published) <?php if ($req) echo "(required)"; ?></small></label>
        </p>

        <p> 
          <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
          <label for="url"><small>Website</small></label>
        </p>

      <?php endif; ?>

        <p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>

        <p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
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
