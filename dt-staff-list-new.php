<?php 
  global $post;
  get_header(); 
?>
<div id="dealerpress-content" class="dt-staff-listing grid_24">
<?php
  $loop = new WP_Query( array( 'post_type' => 'dt_staff', 'posts_per_page' => 10 ) );
  while ( $loop->have_posts() ) : $loop->the_post();
    the_title();
    echo '<div class="entry-content">';
      the_content();
      echo '</div>';
endwhile;

?>

<?php 
    global $query_string; query_posts($query_string . "&orderby=title&order=ASC&post_type=dt_staff");


  ?>
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <div class="dt-staff-list-item">
    <div class="dt-staff-member-image"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a></div>
    <h2 class="dt-staff-member-name"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
    <h4 class="dt-staff-member-title">
    <?php
    $dtstaffMeta = get_post_custom($post->ID);
		$dtstaffTitle = $dtstaffMeta["dt-staff-title"][0];
    echo $dtstaffTitle;
    ?>
    </h4>
    <div class="dt-staff-member-content">
    <?php the_content(__('Read more'));?><div class="clear"></div>
    </div>
    <?php echo $ram_list = get_the_term_list( $post->ID, 'departments', '<div class="dt-staff-member-departments"><strong>Departments:</strong><br/>', ', ', '</div>' );?>
    <!--
    <?php trackback_rdf(); ?>
    -->
  </div>
  <?php endwhile; else: ?>
  <p>Sorry, no listings matched your criteria.</p><?php endif; ?>		
</div>
<?php get_footer(); ?>
