<?php
/*
 * Plugin Name: Staff Listing
 * Plugin URI: http://www.automotivewordpresswebsites.com/website-plugin/
 * Version: 2.5
 * Author: <a href="http://www.dealertrend.com/">DealerTrend Inc.</a>
 * Description: A staff listing using custom post types.
*/

# TODO: Create a new taxonomy for reviews and stop depending on the comments template.
# TODO: Figure out how to make certain fields required in the adding of staff members.
# TODO: Fix template titles. When going to list pages, the page titles are all crap.

# Sanity check.
if ( !class_exists( 'Staff_Listing' ) ) {

  class Staff_Listing {

    # PHP 4 Constructor
    function Staff_Listing() {

      add_action( 'rewrite_rules_array' , array( &$this , 'add_rewrite_rules' ) , 1 );

      add_action( 'init' , array( &$this , 'register_custom_post_type' ) );
      add_action( 'init' , array( &$this , 'register_custom_taxonomy' ) );
      add_action( 'init' , array( &$this , 'flush_rewrite_rules' ) , 1 );

      add_action( 'admin_init' , array( &$this , 'admin_init' ) );
      add_action( 'wp_insert_post' , array ( &$this , 'add_custom_fields' ) , 10 , 2 );

      add_action( 'wp_print_styles' , array( &$this, 'front_styling' ), 5 , null );
      add_action( 'admin_print_styles' , array( &$this, 'admin_styling' ), 5 , null );

      # Hijack the content.
      add_action( 'template_redirect' , array( &$this , 'hijack_content' ) );
      add_action( 'comments_template' , array( &$this , 'hijack_reviews' ) );

      add_action( 'wp_title' , array( &$this , 'set_page_titles' ) );

    } # End Constructor

    # Strip the taxonomy name from the title.
    function set_page_titles( $title ) { 
      global $wp_query;

      if( $wp_query->is_tax ) { 
        $title = str_replace( 'Manage Departments' , '' , $title );
      }   

      return $title;
    } # End set_page_titles();

    # Add our own rewrite rules for our custom post type and taxonomy.
    function add_rewrite_rules( $existing_rules ) { 

      $new_rules = array();

      $new_rules[ '^(departments)$' ] = 'index.php?post_type=staff_listing&taxonomy=departments';
      $new_rules[ '^(departments)/([^/]+)' ] = 'index.php?post_type=staff_listing&taxonomy=departments&departments=$matches[2]&orderby=date&order=ASC';
      $new_rules[ '^(staff)$' ] = 'index.php?post_type=staff_listing&taxonomy=staff_listing';
      $new_rules[ '^(staff)/([^/]+)$' ] = 'index.php?post_type=staff_listing&taxonomy=staff_listing&name=$matches[2]';
      $new_rules[ '^(staff)/([^/]+)/comment-page-([0-9]{1,})/?$' ] = 'index.php?post_type=staff_listing&taxonomy=staff_listing&name=$matches[2]&cpage=$matches[3]';

      return $new_rules + $existing_rules;

    } # End add_rewrite_rules()

    # Rebuild the rewrite rules.
    function flush_rewrite_rules() {

      global $wp_rewrite;

      return $wp_rewrite->flush_rules();

    } # End flush_rewrite_rules()

    # Hijack the output of the comments template.
    function hijack_reviews() {

      global $wp_query;

      $taxonomy = ( isset( $wp_query->query_vars[ 'taxonomy' ] ) ) ? $wp_query->query_vars[ 'taxonomy' ] : NULL;
      $name = ( isset( $wp_query->query_vars[ 'name' ] ) ) ? $wp_query->query_vars[ 'name' ] : NULL;

      if( $taxonomy == 'staff_listing' && $name != NULL ) {
        include( dirname( __FILE__ ) . '/library/templates/reviews.php' );
        exit();
      }

    } # End hijack_reviews()

    # Register new post type for staff.
    function register_custom_post_type() {

      register_post_type(
        'staff_listing',
        array(
          'labels' => array(
            'name' => 'Staff Listings',
            'add_new_item' => 'Add a Staff Member',
            'new_item' => 'Staff Member',
            'add_new' => 'Add a Staff Member',
            'singular_name' => 'Staff Member'
          ),
          'public' => true,
          'publicly_queryable' => true,
          'show_in_nav_menus'  => false,
          'exclude_from_search' => false,
          'show_ui' => true, 
          'hierarchical' => false,
          'rewrite' => array(
            'slug' => 'staff',
            'with_front' => false
          ),
          'query_var' => 'staff', 
          'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'comments',
            'revisions'
          )
        )
      );

    } # End register_custom_post_type()

    # Register a new group classification.
    function register_custom_taxonomy() {

      register_taxonomy(
        'departments',
        'staff_listing',
        array( 
          'labels' => array(
            'name' => 'Manage Departments',
            'add_new_item' => 'Add New Department',
            'new_item_name' => 'New Department Name',
            'add_new' => 'Add New Department',
            'singular_name' => 'Department'
          ),
          'public' => true,
          'publicly_queryable' => true,
          'show_ui' => true,
          'hierarchical' => true,
          'rewrite' => array(
            'slug' => 'departments',
            'with_front' => false
          ),
          'query_var' => 'departments'
        )
      );
 
    } # End register_custom_taxonomy()
  
    # Add custom columns to the member list page in the admin section.
    function add_custom_columns( $columns ) {

      $columns = array( 
        'cb' => '<input type="checkbox" />',
        'staff_listing_photo' => 'Photo',
        'staff_listing_title' => 'Name and Position',
        'staff_listing_description' => 'Description',
        'staff_listing_departments'  => 'Departments',
      );

      return $columns;

    } # End add_custom_columns()
  
    # Configure custom columns to add to the member list in the administration area.
    function configure_custom_columns( $column ) {

      # Bring the post into scope.
      global $post; 

      switch( $column ) {

        case 'staff_listing_photo':
          $thumb = the_post_thumbnail( array( 100 , 100 ) );
          if( !has_post_thumbnail( $post->ID ) ) { echo '<div class="staff-listing-admin no-thumb"><span>No Picture</span></div>'; }

          break; 

        case 'staff_listing_description':
          the_excerpt();
          break;
 
        case 'staff_listing_title':
          $custom = get_post_custom();
          $title = ( isset( $custom[ 'staff_listing_title' ][ 0 ] ) ) ? $custom[ 'staff_listing_title' ][ 0 ] : NULL;
          echo the_title( '<h3 style="margin:0px;">' , '</h3>' );
          echo '<h4 style="margin:0px;">' . $title . '</h4>';
          echo edit_post_link( 'edit' , '<span style="margin:0px;">' , '</span>&nbsp;-&nbsp;<a target= "_new" href="' );
          echo the_permalink();
          echo '">view</a>';
          break;

        case 'staff_listing_departments':
          $departments = get_the_terms( 0 , 'departments' );
          $departments_html = array();

          if( !$departments ) continue;

          foreach( $departments as $department )
            array_push( $departments_html , '<a href="' . get_term_link( $department->slug , 'departments' ) . '" target=_"blank">' . $department->name . '</a>' );        
            echo implode( $departments_html , ', ' );

          break;

        default:
          echo 'Invalid Column Value: Please contact the plugin developer.';
          break;

      }

    } # End configure_custom_columns()
 
    # Hijack the content when determining what page/post to display.
    function hijack_content() {

      # Bring wp_query into scope.
      global $wp_query;

      if( !isset( $wp_query->query_vars ) )
        return false;

      $taxonomy = ( isset( $wp_query->query_vars[ 'taxonomy' ] ) ) ? $wp_query->query_vars[ 'taxonomy' ] : NULL;
      $departments = ( isset( $wp_query->query_vars[ 'departments' ] ) ) ? $wp_query->query_vars[ 'departments' ] : NULL;
      $name = ( isset( $wp_query->query_vars[ 'name' ] ) ) ? $wp_query->query_vars[ 'name' ] : NULL;

      $template_file = false;

      if( $taxonomy == 'staff_listing' ) {
        $template_file = ( $name == NULL ) ? 'staff-list-page.php' : 'staff-detail-page.php';
      } elseif( $taxonomy == 'departments' ) {
        $template_file = ( $departments == NULL ) ? 'department-list-page.php' : 'department-detail-page.php';
      }

      if( $template_file ) {
        include( dirname( __FILE__ ) . '/library/templates/' . $template_file );
        exit();
      }

    } # End hijack_content()
  
    # Add custom fields for storing data when writing posts.
    function add_custom_fields( $post_id , $post = null ) {
    
      $meta_fields = array( 'staff_listing_title' );

      if( $post->post_type == 'staff_listing' ) {

        # Loop through the POST data  
        foreach( $meta_fields as $key ) {

          $value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : NULL;
          if( empty( $value ) ) {
            delete_post_meta( $post_id , $key );
            continue;
          }

          # If value is a string it should be unique
          if( !is_array( $value ) ) {
            # Update meta
            if( !update_post_meta( $post_id , $key , $value ) ) {
              # Or add the meta data 
              add_post_meta( $post_id , $key , $value );
            } 
          } else {
            # If passed along is an array, we should remove all previous data
            delete_post_meta( $post_id , $key );
          
            # Loop through the array adding new values to the post meta as different entries with the same name
            foreach( $value as $entry ) {
              add_post_meta( $post_id , $key , $entry );
            }
          }
 
        }

      }

    } # End add_custom_fields()
  
    # Display styling on the front end.
    function front_styling() {

      wp_register_style( 'staff_listing_style' , plugins_url( '' , __FILE__ ) . '/' . basename( dirname( __FILE__ ) ) . '/library/styles/staff-listings-front.css' , 5 , NULL );
      wp_enqueue_style( 'staff_listing_style' );

    } # End front-styling()

    function admin_styling() {
      
      wp_register_style( 'staff_listing_style' , plugins_url( '' , __FILE__ ) . '/' . basename( dirname( __FILE__ ) ) . '/library/styles/staff-listings-admin.css' , 5 , NULL );
      wp_enqueue_style( 'staff_listing_style' );

    }
  
    function admin_init() {

      # Naming of hook is intentional:
      # http://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
      add_filter( 'manage_edit-staff_listing_columns' , array( &$this , 'add_custom_columns' ) );
      add_action( 'manage_posts_custom_column' , array( &$this , 'configure_custom_columns' ) );

      # Custom meta boxes for the edit staff screen
      add_meta_box( 'staff_listing_title' , 'Position or Title' , array( &$this , 'meta_options' ) , 'staff_listing', 'normal' , 'high' );

    } # End admin_init()
  
    # Obtain the new custom field values and have them represented on the edit screen of our new custom post type.
    function meta_options() {

      # Bring the post into scope.
      global $post;

      $custom_post = get_post_custom( $post->ID );

      if( isset( $custom_post[ 'staff_listing_title' ][ 0 ] ) ) {
        $title = $custom_post[ 'staff_listing_title' ][ 0 ];
      } else {
        $title = NULL;
      }

      echo '<input name="staff_listing_title" value="' . $title . '" style="width:95%;"/>';

    } # End meta_options()

  } # End Class

} # End Class Check

# Create a new instance of the plugin so that it's ready for use.
if ( class_exists( 'Staff_Listing' ) and !isset( $staff_listing ) ) {
  $staff_listing = new Staff_Listing();
}

?>
