<?php
/*
Plugin Name: DealerTrend Staff Listing
Plugin URI: http://www.automotivewordpresswebsites.com/website-plugin/
Version: 1.0
Author: <a href="http://www.dealertrend.com/">DealerTrend Inc.</a>
Description: A staff listing using custom post types. Requires CarDealerPress theme.
*/

class DTStaffListing {
  var $meta_fields = array("dt-staff-title");
  function DTStaffListing()
  {
print_r($this);
          // Register custom post types
    register_post_type('dt_staff', array(
      'labels' => array(
        'name' => 'Staff Listings',
        'add_new_item' => 'Add a staff member',
        'new_item' => 'Staff member',
        'add_new' => 'Add a staff member',
        'singular_name' => 'Staff member'
      ),
      'public' => false,
      'publicly_queryable' => true,
      'show_ui' => true, // UI in admin panel
      '_builtin' => false, // It's a custom post type, not built in
      '_edit_link' => 'post.php?post=%d',
      'capability_type' => 'post',
      'hierarchical' => false,
      'rewrite' => array("slug" => "staff", 'with_front' => FALSE), // Permalinks
      'query_var' => "staff", // This goes to the WP_Query schema
      'supports' => array('title','editor','thumbnail',/*'excerpt',*/'comments','revisions')
    ));
    
    add_filter("manage_edit-dt_staff_columns", array(&$this, "edit_columns"));
    add_action("manage_posts_custom_column", array(&$this, "custom_columns"));
    
    // Register custom taxonomy
    register_taxonomy( 'departments', 'dt_staff', array( 
      'hierarchical' => true, 
      'label' => 'Departments',
      'labels' => array(
        'add_new_item' => 'Add New Department',
        'new_item_name' => 'New Department Name'
      ), 'query_var' => true,
      'rewrite' => array("slug" => "departments", 'with_front' => FALSE), // Permalinks
    ));  

    // Admin interface init
    add_action("admin_init", array(&$this, "admin_init"));

    add_action("template_redirect", array(&$this, 'template_redirect'));
    
    // Insert post hook
    add_action("wp_insert_post", array(&$this, "wp_insert_post"), 10, 2);
    if (!is_admin()) {
      add_action('wp_print_styles', array( &$this, 'staff_styles'), 5, null); 
    }
  }
  
  // Add custom colmns to the member list in admin
  function edit_columns($columns)
  {
    $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "dt_photo" => "Photo",
      "dt_title" => "Name and Position",
      "dt_description" => "Description",
      "dt_departments" => "Departments",
    );
    return $columns;
  }
  
  // Configure custom columns to add to the member list in admin
  function custom_columns($column)
  {
    global $post;
    switch ($column)
    {
      case "dt_photo":
        echo the_post_thumbnail( array(100,100) );
        break;
      case "dt_description":
        the_excerpt();
        break;
      case "dt_title":
        $custom = get_post_custom();
        echo the_title('<h3 style="margin:0px;">', '</h3>');
        echo '<h4 style="margin:0px;">'.$custom["dt-staff-title"][0].'</h4>';
        echo edit_post_link('edit', '<span style="margin:0px;">', '</span>&nbsp;-&nbsp;<a target= "_new" href="');
        echo the_permalink();
        echo '">view</a>';
        break;
      case "dt_departments":
        $departments = get_the_terms(0, "departments");
        $departments_html = array();
        foreach ($departments as $department)
          array_push($departments_html, '<a href="' . get_term_link($department->slug, "departments") . '" target=_"blank">' . $department->name . '</a>');        
          echo implode($departments_html, ", ");
        break;
    }
  }
  
  // Redirect template based on post list or single post
  function template_redirect()
  {
    global $wp_query;
    if ($wp_query->query_vars["post_type"] == "dt_staff" || $wp_query->query_vars["taxonomy"] == "departments" || $wp_query->query_vars['category_name'] == "staff" || $wp_query->query_vars['category_name'] == "departments" )
    {
      if($wp_query->query_vars["name"]):
        include('dt-staff-single.php');
        die();
      else:
      include('dt-staff-list.php');
        die();
      endif;
    }
  }
  
  // When a post is inserted or updated
  function wp_insert_post($post_id, $post = null)
  {
    if ($post->post_type == "dt_staff")
    {
      // Loop through the POST data
      foreach ($this->meta_fields as $key)
      {
        $value = @$_POST[$key];
        if (empty($value))
        {
          delete_post_meta($post_id, $key);
          continue;
        }

        // If value is a string it should be unique
        if (!is_array($value))
        {
          // Update meta
          if (!update_post_meta($post_id, $key, $value))
          {
            // Or add the meta data
            add_post_meta($post_id, $key, $value);
          }
        }
        else
        {
          // If passed along is an array, we should remove all previous data
          delete_post_meta($post_id, $key);
          
          // Loop through the array adding new values to the post meta as different entries with the same name
          foreach ($value as $entry)
            add_post_meta($post_id, $key, $entry);
        }
      }
    }
  }
  
  function staff_styles() {
    wp_register_style('staff-style', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__))."/dt-staff-listing.css", 5, null);
    wp_enqueue_style('staff-style');
  }
  
  // 
  function admin_init() 
  {
    // Custom meta boxes for the edit staff screen
    add_meta_box("dt-staff-title", "Position or Title", array(&$this, "meta_options"), "dt_staff", "normal", "high");
  }
  
  // Admin post meta contents
  function meta_options()
  {
    global $post;
    $dt_custom = get_post_custom($post->ID);
    if ($dt_custom["dt-staff-title"][0]){
      $dt_title = $dt_custom["dt-staff-title"][0];
    }else{
      $dt_title = "";
    }
    echo '<input name="dt-staff-title" value="'.$dt_title.'" style="width:95%;"/>';
  }

}

// Initiate the plugin
add_action("init", "DTStaffListingInit");
function DTStaffListingInit() { global $p30; $p30 = new DTStaffListing(); }

add_action('init', 'my_rewrite');

function my_rewrite() {
    global $wp_rewrite;
    $wp_rewrite->add_permastruct('typename', 'typename/%postname%/', true, 1);
    add_rewrite_rule('typename/(.+)/?$', 'index.php?typename=$matches[1]', 'top');
    $wp_rewrite->flush_rules(); // !!!
}
?>
