<?php

define( 'DISALLOW_FILE_EDIT', true );

class bne_rest_api_theme{

	var $custom_post_types = array( 'Service', 'Link' );

	function __construct(){
		add_action( 'init', array ( $this, 'init' ) );
	}

	function init(){

        add_filter( "rest_prepare_post", array( $this, 'rest_prepare') );

		foreach ( $this->custom_post_types as $post_type ){

            $slug = strtolower($post_type);

			register_post_type( $slug,
				array(
					'labels' => array(
						'name' => __( $post_type . 's' ),
						'singular_name' => __( $post_type )
					),
					'public' => true,
					'has_archive' => true,
                    'show_in_rest' => true, //Adds the custom post type to the rest api
				)
			);

            add_filter( "rest_prepare_{$slug}", array( $this, 'rest_prepare') );

        }

    }


    function rest_prepare( $data, $post, $request ){

        $response_data = $data->get_data();

        $response_data['_meta'] = get_post_meta( $response_data['id'] );

        $data->set_data( $response_data );

        return $data;
    }

}

$bne_rest_api_theme = new bne_rest_api_theme();




