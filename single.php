<?php

  # Bring objects into scope.
  global $post, $query_string, $wp_query;

  switch( $wp_query->query_vars[ 'category_name' ] ) { 
    case 'departments':
      $results = query_posts( 'post_type=staff_listing&departments=' . $wp_query->query_vars[ 'name' ] );
      break;
    default:
      $results = query_posts( $query_string . '&orderby=title&order=ASC&post_type=staff_listing' );
      break;
  }

  get_header(); 

?>
  <div id="staff-listing-single">
    <?php
      if( isset( $wp_query->query_vars[ 'name' ] ) && $wp_query->query_vars[ 'category_name' ] == 'departments' ) { 
        echo '<h3>' . get_the_term_list( $results[ 0 ]->ID , 'departments' , 'Department [' , ', ' , ']' ) . '</h3>';
      }   
    ?>  
    <?php if( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div class="staff-listing-member">
      <div class="staff-listing-image"><?php the_post_thumbnail( 'medium' ); ?></div>
      <h2 class="staff-listing-name">
        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
          <?php the_title(); ?>
        </a>
      </h2>
      <h4 class="staff-listing-title">
      <?php
        $meta = get_post_custom( $post->ID );
        $title = $meta[ 'staff_listing_title' ][ 0 ];
        echo $title;
      ?>  
      </h4>
      <div class="staff-listing-content">
        <?php the_content( __( 'Read more' ) ); ?>
        <br/>
        <div class="staff-listing-departments"><?php echo get_the_term_list( $post->ID , 'departments' , '<strong>Departments:</strong> ' , ', ' , '' ); ?></div>
        <div class="clear"></div>
      </div>
      <!--
      <?php trackback_rdf(); ?>
      -->
      <div class="postcomments">
        <?php #comments_template(); ?> 
      </div>
    </div>
    <?php endwhile; else: ?>
      <p>Sorry, no listings matched your criteria.</p>
    <?php endif; ?>   
  </div>
<?php get_footer(); ?>
