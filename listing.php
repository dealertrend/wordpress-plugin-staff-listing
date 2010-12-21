<?php 

  # Bring objects into scope.
  global $wp_query, $post;

  switch( $wp_query->query_vars[ 'category_name' ] ) { 
    case 'departments':
      $parameters = array(
        'orderby'            => 'name',
        'order'              => 'ASC',
        'style'              => 'none',
        'hierarchical'       => true,
        'taxonomy'           => 'departments',
      );  
      break;
    default:
      $parameters = array(
        'post_type' => 'staff_listing',
        'posts_per_page' => 10, 
        'orderby' => 'title',
        'order' => 'ASC'
      );  
      $the_query = new WP_Query( $parameters );
      break;
  }

  get_header();

  echo '<div id="staff-listing">';

  if( !$the_query ) { 
    echo '<h3>Departments</h3>';
    echo '<div class="staff-listing-item">';
    wp_list_categories( $parameters );
    echo '</div>';
  } else {
    if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : 
      $the_query->the_post();
?>
      <div class="staff-listing-item">
        <div class="staff-listing-member-image">
          <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
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
<?php
    endif;
    wp_reset_query();
  }

  echo '</div>';

  get_footer();

?>
