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
        add_action('init', array($this, 'init'));

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
    function rest_prepare($data, $post, $request = 0)
    {

        $response_data = $data->get_data();

        $response_data['_meta'] = get_post_meta($response_data['id']);

        $data->set_data($response_data);

        return $data;
    }

}

/**
 * initializes the rest api class
 */
$bne_rest_api_theme = new bne_rest_api_theme();




