<?php 

  # Bring objects into scope.
  global $post;

  # TODO: Add pagination...right now I think this is limited to 10 entries...by default.
  $parameters = array(
    'orderby' => 'name',
    'order' => 'ASC',
    'style' => 'none',
    'hierarchical' => true,
    'taxonomy' => 'departments',
    'echo' => false
  );

  $departments = array_filter( explode( '<br />' , wp_list_categories( $parameters ) ), 'trim' );

  $limit = count( $departments );

  $counter = 0;
  $rows = 0;

  get_header();
?>
  <div id="staff-listing" class="department-listing">
    <div class="wrapper">
      <h3 class="header">Departments</h3>
      <?php if( $departments[ 0 ] != 'No categories' ) : foreach( $departments as $department ) : ?>
      <?php if( $counter % 4 == 0 || $counter == 0 ): $rows++; ?>
        <div class="row">
      <?php endif; $counter++; ?>
        <div class="list-item">
          <h2 class="name">
            <?php echo trim( $department ); ?>
          </h2>
        </div>
        <?php if( $counter % 4 == 0 || $counter == $limit ):  ?>
          <div style="clear:both;"></div>
          </div><!-- .row -->
        <?php endif; ?>
      <?php endforeach; else: ?>
        <p>Sorry, there are currently no active departments.</p>
      <?php endif; ?>
      <?php wp_reset_query(); ?>
    </div><!-- .wrapper -->
  </div><!-- #staff-listing .department-listing -->
<?php get_footer(); ?>
