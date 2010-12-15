<?php 

  # Bring objects into scope.
  global $wp_query, $post;

  $args = array(
    'post_type' => 'staff_listing',
    'posts_per_page' => 10,
    'orderby' => 'title',
    'name' => $wp_query->query_vars[ 'name' ] ,
    'order' => 'ASC'
  );

  if( isset( $wp_query->query_vars[ 'departments' ] ) ) {
      $args = array_merge( $args, array( 'departments' => $wp_query->query_vars[ 'departments' ] ) );
  }
  if( isset( $wp_query->query_vars[ 'staff' ] ) ) {
      $args = array_merge( $args, array( 'staff' => $wp_query->query_vars[ 'staff' ] ) );
  }

  $the_query = new WP_Query($args);
  get_header();
  echo '<div id="staff-listing">';

  if( $the_query->have_posts()) : while( $the_query->have_posts() ) : $the_query->the_post();

?>
  <div class="staff-listing-item">
    <div class="staff-listing-member-image">
      <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
    </div>
    <h2 class="staff-listing-member-name">
      <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
    </h2>
    <h4 class="staff-listing-member-title">
    <?php
      $meta = get_post_custom( $post->ID );
		  $title = $meta[ 'staff_listing_title' ][ 0 ];
      echo $title;
    ?>
    </h4>
    <div class="staff-listing-member-content">
      <?php the_content( __( 'Read more' ) ); ?><div class="clear"></div>
    </div>
    <?php echo get_the_term_list( $post->ID, 'departments', '<div class="staff-listing-member-departments"><strong>Departments:</strong><br/>' , ', ' , '</div>' ); ?>
    <!-- <?php trackback_rdf(); ?> -->
  </div>

  <?php endwhile; else: ?>

  <p>Sorry, no listings matched your criteria.</p>
  <?php endif; wp_reset_query(); ?>		

</div>

<?php get_footer(); ?>
