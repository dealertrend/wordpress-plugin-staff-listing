<?php

  # Bring objects into scope.
  global $post, $query_string, $wp_query;

  $results = query_posts( 'post_type=staff_listing&departments=' . $wp_query->query_vars[ 'name' ] );

  get_header(); 

  if( isset( $wp_query->query[ 'name' ] ) ) :

?>

  <div id="staff-listing">
    <?php
        echo '<h3>' . $wp_query->queried_object->name . '</h3>';
    ?>  
    <?php if( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div class="staff-listing-item">
      <div class="staff-listing-member-image">
        <?php if( has_post_thumbnail( $post->ID ) ) { ?>
          <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
        <?php } else { ?>
          <div style="height:100px; width:100px; background:#e1e1e1; border:1px solid #CCC; margin:auto;"><span style="display:block; margin-top:40px; text-align:center;">No Image</span></div>
        <?php } ?>
      </div>
      <h2 class="staff-listing-member-name">
        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
          <?php the_title(); ?>
        </a>
      </h2>
      <h4 class="staff-listing-member-title">
      <?php
        $meta = get_post_custom( $post->ID );
        $title = $meta[ 'staff_listing_title' ][ 0 ];
        echo $title;
      ?>  
      </h4>
      <div class="staff-listing-member-content">
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

<?php else: ?>
      <p>Sorry, no listings matched your criteria.</p>
<?php endif; ?>

<?php get_footer(); ?>
