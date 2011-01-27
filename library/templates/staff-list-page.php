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
?>

  <div id="staff-listing">
    <h3>Staff Listing</h3>
    <?php if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : $the_query->the_post(); ?>
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
          echo $meta[ 'staff_listing_title' ][ 0 ];
        ?>  
        </h4>
        <div class="content">
          <?php the_content( __( 'Read more' ) ); ?><div class="clear"></div>
        </div>
        <?php echo get_the_term_list( $post->ID, 'departments', '<div class="departments"><strong>Departments:</strong><br/>' , ', ' , '</div>' ); ?>
      </div>
    <?php endwhile; else: ?>
      <p>Sorry, no listings matched your criteria.</p>
    <?php endif;?>
    <?php wp_reset_query(); ?>
  </div><!-- #staff-listing -->

<?php get_footer(); ?>
