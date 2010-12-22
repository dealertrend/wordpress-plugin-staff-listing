<?php 

  # Bring objects into scope.
  global $post;

  $parameters = array(
    'orderby' => 'post_name',
    'order' => 'ASC',
    'style' => 'none',
    'hierarchical' => true,
    'taxonomy' => 'departments',
    'echo' => false
  );

  $departments = array_filter( explode( '<br />' , wp_list_categories( $parameters ) ), 'trim' );

  get_header();

  echo '<h3>Departments</h3>';

  echo '<div id="department-listing">';

  if( $departments[ 0 ] != 'No categories' ) : foreach( $departments as $department ) :
    ?>
    <div class="department-listing-item">
      <h2 class="department-listing-name">
        <?php echo trim( $department ); ?>
      </h2>
    </div>
  <?php endforeach; else: ?>
    <p>Sorry, there are currently no active departments.</p>
  <?php endif;?>

<?php wp_reset_query(); ?>

  </div>

<?php get_footer(); ?>
