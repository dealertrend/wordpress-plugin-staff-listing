<?php
/*
 * Plugin Name: Staff Listing
 * Plugin URI: http://www.automotivewordpresswebsites.com/website-plugin/
 * Version: 1.0
 * Author: <a href="http://www.dealertrend.com/">DealerTrend Inc.</a>
 * Description: A staff listing using custom post types.
*/

if ( !class_exists( 'Staff_Listing' ) ) {

  class Staff_Listing {

    var $meta_fields = array( 'staff_listing_title' );

    # PHP 4 Constructor
    function Staff_Listing() {

      add_action( 'init' , array( &$this , 'register_custom_post_type' ) );
      add_action( 'init' , array( &$this , 'register_custom_taxonomy' ) );

      add_action( 'comments_template' , array( &$this , 'comments' ) );

      # Admin interface init
      add_action( 'admin_init' , array( &$this , 'admin_init' ) );

      # Produce content for listing pages.
      add_action( 'template_redirect' , array( &$this , 'template_redirect' ) );
    
      # Insert post hook
      add_action( 'wp_insert_post' , array ( &$this , 'wp_insert_post' ) , 10 , 2 );

      # Only show the stylesheet if we are on the front end.
      if( !is_admin() ) {
        add_action( 'wp_print_styles' , array( &$this, 'staff_styles' ), 5 , null );
      }

    } # End Constructor

    function comments() {

      if( $this->is_staff_listing() ) {
        include( 'comments.php' );
        exit;
      }

    }

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

    } # End reigster_custom_post_type()

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
    function edit_columns( $columns ) {

      $columns = array( 
        'cb' => '<input type="checkbox" />',
        'staff_listing_photo' => 'Photo',
        'staff_listing_title' => 'Name and Position',
        'staff_listing_description' => 'Description',
        'staff_listing_departments'  => 'Departments',
      );

      return $columns;

    } # End edit_columns()
  
    # Configure custom columns to add to the member list in admin
    function custom_columns( $column ) {

      # Bring the post into scope.
      global $post; 

      switch( $column ) {

        case 'staff_listing_photo':
          echo the_post_thumbnail( array( 100 , 100 ) );
          break; 

        case 'staff_listing_description':
          the_excerpt();
          break;
 
        case 'staff_listing_title':
          $custom = get_post_custom();
          echo the_title( '<h3 style="margin:0px;">' , '</h3>' );
          echo '<h4 style="margin:0px;">' . $custom[ 'staff_listing_title' ][0] . '</h4>';
          echo edit_post_link( 'edit' , '<span style="margin:0px;">' , '</span>&nbsp;-&nbsp;<a target= "_new" href="' );
          echo the_permalink();
          echo '">view</a>';
          break;

        case 'staff_listing_departments':
          $departments = get_the_terms( 0 , 'departments' );
          $departments_html = array();
          foreach( $departments as $department )
            array_push( $departments_html , '<a href="' . get_term_link( $department->slug, 'departments' ) . '" target=_"blank">' . $department->name . '</a>' );        
            echo implode( $departments_html , ', ' );
          break;

        default:
          echo 'Invalid Column Value';
          break;

      }

    } # End custom_columns()
 
    function is_staff_listing() {

      # Bring wp_query into scope.
      global $wp_query;

      if( $wp_query->query_vars[ 'post_type' ] == 'staff_listing' || 
        $wp_query->query_vars[ 'category_name' ] == 'staff' || 
        $wp_query->query_vars[ 'category_name' ] == 'departments' || 
        ( isset( $wp_query->query_vars[ 'taxonomy' ] ) && $wp_query->query_vars[ 'taxonomy' ] == 'departments'  )
      ) {
        return true;
      }

      return false;

    }
 
    # Redirect template based on post list or single post
    function template_redirect() {

      # Bring wp_query into scope.
      global $wp_query;

      if( isset( $wp_query->query_vars ) ) {
        if( $this->is_staff_listing() ) {
          if( $wp_query->query_vars[ 'name' ] ):
            include( 'single.php' );
          else:
            include( 'listing.php' );
          endif;

          exit;

        }
      }      

    } # End tepmlate_redirect()
  
    # When a post is inserted or updated
    function wp_insert_post( $post_id, $post = null ) {

      if( $post->post_type == 'staff_listing' ) {

        # Loop through the POST data  
        foreach( $this->meta_fields as $key ) {

          $value = isset( $_POST[$key] ) ? $_POST[$key] : NULL;
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
            delete_post_meta( $post_id, $key );
          
            # Loop through the array adding new values to the post meta as different entries with the same name
            foreach( $value as $entry )
             add_post_meta( $post_id , $key , $entry );
          }
 
        }

      }

    } # End wp_insert_option()
  
    function staff_styles() {

      wp_register_style( 'staff_listing_style' , WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) ) . '/style.css' , 5 , NULL );
      wp_enqueue_style( 'staff_listing_style' );

    } # End staff_styles()
  
    function admin_init() {

      # These categories prevenet WordPress from serving a 404 error when people goto /staff
      $required_category = array(
        'cat_name' => 'Staff Listing',
        'category_description' => 'DO NOT USE: This is a place holder for the Staff Listing Plugin!',
        'category_nicename' => 'staff',
        'category_parent' => ''
      );
      wp_insert_category($required_category);
      $required_category = array(
          'cat_name' => 'Departments',
          'category_description' => 'DO NOT USE: This is a place holder for the Staff Listing Plugin!',
          'category_nicename' => 'departments',
          'category_parent' => ''
      );
      wp_insert_category($required_category);

      # http://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
      add_filter( 'manage_edit-staff_listing_columns', array( &$this , 'edit_columns' ) );
      add_action( 'manage_posts_custom_column', array( &$this , 'custom_columns' ) );

      # Custom meta boxes for the edit staff screen
      add_meta_box( 'staff_listing_title', 'Position or Title' , array( &$this , 'meta_options' ), 'staff_listing', 'normal' , 'high' );

    } # End admin_init()
  
    # Admin post meta contents
    function meta_options() {

      # Bring the post into scope.
      global $post;

      $custom = get_post_custom( $post->ID );

      # TODO: Verify index existancd before comparing values.
      if( $custom[ 'staff_listing_title' ][ 0 ]) {
        $title = $custom[ 'staff_listing_title' ][ 0 ];
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
