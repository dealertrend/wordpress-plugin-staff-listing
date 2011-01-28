<?php

  # Bring objects into scope.
  global $post , $query_string , $wp_query;

  $results = query_posts( $query_string . '&post_type=staff_listing&orderby=post_name&order=ASC' );

  get_header(); 

?>
  <div id="staff-listing">
    <div class="wrapper">
      <div id="single">
        <?php
          if( isset( $wp_query->query_vars[ 'name' ] ) ) { 
            echo '<h3>' . get_the_term_list( $results[ 0 ]->ID , 'departments' , 'Department(s) ' , ', ' , '' ) . '</h3>';
          }   
        ?>  
        <?php if( have_posts() ) : while ( have_posts() ) : the_post(); ?>
          <div class="member">
            <div class="image">
              <?php if( has_post_thumbnail( $post->ID ) ) : ?>
                <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
              <?php else: ?>
                <div class="no-thumb"><span>No Image</span></div>
              <?php endif; # Do we have a thumbnail? ?>
            </div><!-- .image -->
            <h2 class="name">
              <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
                <?php the_title(); ?>
              </a>
            </h2><!-- .name -->
            <h4 class="title">
            <?php
              $meta = get_post_custom( $post->ID );
              echo ( isset( $meta[ 'staff_listing_title' ][ 0 ] ) ) ? $meta[ 'staff_listing_title' ][ 0 ] : NULL;
            ?>  
            </h4><!-- .title -->
            <div class="content">
              <?php the_content( __( 'Read more' ) ); ?>
              <div class="departments"><?php echo get_the_term_list( $post->ID , 'departments' , '<strong>Departments:</strong> ' , ', ' , '' ); ?></div>
              <div class="clear"></div>
            </div><!-- .content -->
            <div class="postcomments">
              <?php comments_template(); ?> 
            </div><!-- .postcomments -->
          </div><!-- .member -->
        <?php endwhile; else: ?>
          <p>Sorry, no listings matched your criteria.</p>
        <?php endif; ?>
      </div><!-- #single -->
    </div><!-- .wrapper -->
  </div><!-- #staff-listing -->
<?php get_footer(); ?>
