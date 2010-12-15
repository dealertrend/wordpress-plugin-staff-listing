<?php 
  global $post;
  get_header(); 
?>
<div id="dealerpress-content" class="dt-staff-single grid_24">
  <?php 
    global $query_string; query_posts($query_string . "&orderby=title&order=ASC&post_type=dt_staff");
  ?>
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <div class="dt-staff-member">
    <div class="dt-staff-image"><?php the_post_thumbnail('medium'); ?></div>
    <h2 class="dt-staff-name"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
    <h4 class="dt-staff-title">
    <?php
      $dtstaffMeta = get_post_custom($post->ID);
  		$dtstaffTitle = $dtstaffMeta["dt-staff-title"][0];
      echo $dtstaffTitle;
    ?>
    </h4>
    <div class="dt-staff-content">
      <?php the_content(__('Read more'));?>
      <br/>
      <div class="dt-staff-departments"><?php echo $ram_list = get_the_term_list( $post->ID, 'departments', '<strong>Departments:</strong> ', ', ', '' );?></div>
      <div class="clear"></div>
    </div>
    <!--
    <?php trackback_rdf(); ?>
    -->
    <div class="postcomments">
      <?php comments_template( WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/staff-comments.php' , TRUE); ?> 
    </div>
  </div>
  <?php endwhile; else: ?>
  <p>Sorry, no listings matched your criteria.</p><?php endif; ?>		
</div>
<?php get_footer(); ?>
