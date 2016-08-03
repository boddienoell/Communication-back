<?php

/**
 * disallows the edit feature in the admin b/c I don't like that feature.
 */
define('DISALLOW_FILE_EDIT', true);

/**
 * Avoid processing the logic in the template-loader.php file
 */
define('WP_USE_THEMES', false);


/**
 * Class bne_rest_api_theme
 */
class bne_rest_api_theme
{

    /**
     * @var array
     */
    var $custom_post_types = array('Service', 'Link');

    /**
     * adds the init callback to the init action
     */
    function __construct()
    {
        add_action('init',                  array($this, 'init'));

        add_action('do_feed',               array( $this, 'disable_feed'), 1 );
        add_action('do_feed_rdf',           array( $this, 'disable_feed'), 1 );
        add_action('do_feed_rss',           array( $this, 'disable_feed'), 1 );
        add_action('do_feed_rss2',          array( $this, 'disable_feed'), 1 );
        add_action('do_feed_atom',          array( $this, 'disable_feed'), 1 );
        add_action('do_feed_rss2_comments', array( $this, 'disable_feed'), 1 );
        add_action('do_feed_atom_comments', array( $this, 'disable_feed'), 1 );

    }

    function initB(){
	
	add_filter("rest_prepare_post", array($this, 'rest_prepare'));

	global $wp_post_types;    

	foreach ( $this->custom_post_types as $post_type_name ) {
		
		if ( isset( $wp_post_types[$post_type_name] ) ) {

			 $slug = sanitize_title_with_dashes($post_type_name);

			$wp_post_types[$post_type_name]->show_in_rest = true;
	  		$wp_post_types[$post_type_name]->rest_base = $post_type_name;
  			$wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';

			add_filter("rest_prepare_{$slug}", array($this, 'rest_prepare'));
		}
		
	}
    }

    function init()
    {

        /**
         * adds the custom post metadata filter to the posts
         */
        add_filter("rest_prepare_post", array($this, 'rest_prepare'));


        /**
         * loops through the custom post types in the $custom_post_types
         * array and registers then with WP
         */
        foreach ($this->custom_post_types as $post_type) {

            $slug = sanitize_title_with_dashes($post_type);

            register_post_type($slug,
                array(
                    'labels' => array(
                        'name' => __($post_type . 's'),
                        'singular_name' => __($post_type)
                    ),
                    'public' => true,
                    'has_archive' => true,
                    'show_in_rest' => true, //Adds the custom post type to the rest api
                )
            );

            /**
             * adds the custom post metadata to the custom post types
             */
            add_filter("rest_prepare_{$slug}", array($this, 'rest_prepare'));

        }

        register_taxonomy(
            'link-types',
            'link',
            array(
                'label' => __( 'Link Types' ),
                'rewrite' => array( 'slug' => 'link-types' ),
                'show_in_rest' => true,
		'rest_base' => 'link-types',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
            )
        );

    }


    /**
     * Adds all metadata, including custom metadata, to a "_meta" key with the
     * returned post object response.
     *
     * @param $data
     * @param $post
     * @param $request
     * @return mixed
     */
    function rest_prepare($data, $post = null, $request = 0)
    {

        $response_data = $data->get_data();

        $response_data['_meta'] = get_post_meta($response_data['id']);
        $response_data['_terms'] = wp_get_post_terms( $response_data['id'], 'link-types' );
        $data->set_data($response_data);

        return $data;
    }

    function disable_feed() {
        die('Not available');
    }


}

/**
 * initializes the rest api class
 */
$bne_rest_api_theme = new bne_rest_api_theme();




