<?php 

  # Bring objects into scope.
  global $post;

  $parameters = array(
    'orderby' => 'name',
    'order' => 'ASC',
    'style' => 'none',
    'hierarchical' => true,
    'taxonomy' => 'departments',
    'echo' => false
  );

  $departments = array_filter( explode( '<br />' , wp_list_categories( $parameters ) ), 'trim' );

  get_header();
?>

  <div id="department-listing">
    <h3>Departments</h3>
    <?php if( $departments[ 0 ] != 'No categories' ) : foreach( $departments as $department ) : ?>
      <div class="list-item">
        <h2 class="name">
          <?php echo trim( $department ); ?>
        </h2>
      </div>
    <?php endforeach; else: ?>
      <p>Sorry, there are currently no active departments.</p>
    <?php endif; ?>

    <?php wp_reset_query(); ?>
  </div><!-- #department-listing -->

<?php get_footer(); ?>
