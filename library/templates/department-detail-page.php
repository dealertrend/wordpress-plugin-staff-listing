<?php

  # Bring objects into scope.
  global $post , $query_string , $wp_query;

  get_header(); 

?>
  <div id="staff-listing" class="member-listing">
    <div class="wrapper">
      <?php
        echo '<h3>' . $wp_query->queried_object->name . '</h3>';
      ?>  
      <?php if( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <div class="list-item">
          <div class="member">
            <div class="image">
              <?php if( has_post_thumbnail( $post->ID ) ) : ?>
                <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
              <?php else: ?>
                <div class="no-thumb">
                  <span>No Image</span>
                </div><!-- .no-thumb -->
              <?php endif; # Does the staff member have a thumbnail? ?>
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
              <div class="departments">
                <?php echo get_the_term_list( $post->ID , 'departments' , '<strong>Departments:</strong> ' , ', ' , '' ); ?>
              </div><!-- .departments -->
              <div class="clear"></div>
            </div><!-- .content -->
          </div><!-- .member -->
        </div><!-- .list-item -->
      <?php endwhile; else: ?>
        <p>Sorry, no listings matched your criteria.</p>
      <?php endif; ?>
    </div><!-- .wrapper -->
  </div><!-- #staff-listing -->
<?php get_footer(); ?>
