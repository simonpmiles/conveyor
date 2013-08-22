<?php
/*
Plugin Name: Conveyor
Plugin URI: http://badgersaregreat.com
Description: A simple carousel slider plugin for WordPress built with Twitter Bootstrap and Flexslider by Woothemes.
Version: 1.0
Author: BigSpring "the home of ping"
Author URI: http://www.bigspring.co.uk
License: GPL2
*/

function conveyor_setup() {


	//register the conveyor post type
	add_action( 'init', 'register_cpt_conveyor' );
	
	function register_cpt_conveyor() {
	
	    $labels = array( 
	        'name' => _x( 'slides', 'conveyor' ),
	        'singular_name' => _x( 'slide', 'conveyor' ),
	        'add_new' => _x( 'Add New', 'conveyor' ),
	        'add_new_item' => _x( 'Add New slide', 'conveyor' ),
	        'edit_item' => _x( 'Edit slide', 'conveyor' ),
	        'new_item' => _x( 'New slide', 'conveyor' ),
	        'view_item' => _x( 'View slide', 'conveyor' ),
	        'search_items' => _x( 'Search slides', 'conveyor' ),
	        'not_found' => _x( 'No slides found', 'conveyor' ),
	        'not_found_in_trash' => _x( 'No slides found in Trash', 'conveyor' ),
	        'parent_item_colon' => _x( 'Parent slide:', 'conveyor' ),
	        'menu_name' => _x( 'slides', 'conveyor' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => true,
	        
	        'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
	        
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'menu_position' => 5,
	        'register_meta_box_cb' => 'add_conveyor_meta_boxes',
			'menu_icon' => plugins_url() . '/conveyor/insert pic path here',
	        
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'has_archive' => true,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => true,
	        'capability_type' => 'post'
	    );
	
	    register_post_type( 'conveyor', $args );
	}
	
	//================ META BOXES ====================== //
	
	/**
	 *
	 * Handler when a post is inserted / updated
	 * @param int $post_id
	 * @param object $post
	 */
	function wp_cpt_insert_post($post_id, $post = null)
	{
		global $post;
		
		// these should match the name attribute for the form elements we want to save
		$meta_fields = array('button_url', 'button_url_text');
		$custom_post_types = array('conveyor');
		
		if (in_array($post->post_type,  $custom_post_types))
		{
			// Loop through the POST data
			foreach ($meta_fields as $key)
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
	add_action('save_post', 'wp_cpt_insert_post', 1, 2); // save the custom fields
	
	
	//===============BANNER META BOXES BWOY=============//
	/**
	 *
	 * Callback to initialise the meta boxes
	 */
	function add_conveyor_meta_boxes()
	{
		add_meta_box('button_url', 'Button website link', 'banner_meta_url', 'banner', 'normal', 'high');
	
	}
	
	/**
	 *
	 * Generates the meta boxes
	 */
	function banner_meta_url() // BANNER META BOX
	{
		global $post;
	
		// get the current meta values
		$button_url = get_post_meta($post->ID, 'button_url', true);
		$button_url_text = get_post_meta($post->ID, 'button_url_text', true);
		
	
		echo '<table width="100%">';
		echo '<tr><th style="text-align:left">URL</th><th style="text-align:left">Button text</th></tr>';
		echo '<tr><td><input size="40" type="text" name="button_url" id="button_url"  value="'. $button_url .'" /></td>
				  <td><input size="80" type="text" name="button_url_text" id="button_url_text"  value="'. $button_url_text .'" /></td>
			  </tr>'; 
		echo '</table>'; 
	
		// create a custom nonce for submit verification later
		echo '<input type="hidden" name="my_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
	} 
	

}//end function conveyor_setup