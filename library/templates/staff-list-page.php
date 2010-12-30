<?php 

  # Bring objects into scope.
  global $post;

  $parameters = array(
    'post_type' => 'staff_listing',
    'posts_per_page' => 10, 
    'orderby' => 'date',
    'order' => 'ASC'
  );  
  $the_query = new WP_Query( $parameters );

  get_header();

  echo '<h3>Staff Listing</h3>';

  echo '<div id="staff-listing">';

  if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : $the_query->the_post();
    ?>
    <div class="staff-listing-item">
      <div class="staff-listing-member-image">
        <?php if( has_post_thumbnail( $post->ID ) ) { ?>
          <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
        <?php } else { ?>
          <div style="height:100px; width:100px; background:#e1e1e1; border:1px solid #CCC; margin:auto;"><span style="display:block; margin-top:40px; text-align:center;">No Image</span></div>
        <?php } ?>
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
  <?php endif;?>

<?php wp_reset_query(); ?>

  </div>

<?php get_footer(); ?>
