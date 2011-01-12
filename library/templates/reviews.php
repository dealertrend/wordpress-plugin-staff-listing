<?php global $post; global $current_user; ?>

<?php if( have_comments() ): ?>

  <h3 class="comments-label">Comments</h3>
  <strong><?php comments_number('No Reviews', 'One Response', '% Reviews' );?> for <?php the_title(); ?></strong>
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

</div>

<?php get_footer(); exit; ?>
