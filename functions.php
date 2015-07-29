<?php

add_action( 'init', 'create_post_type' );
function create_post_type() {
   
register_post_type( 'service',
    array(
      'labels' => array(
        'name' => __( 'Services' ),
        'singular_name' => __( 'Service' )
      ),
      'public' => true,
      'has_archive' => true,
    )
  );

 register_post_type( 'link',
    array(
      'labels' => array(
        'name' => __( 'Links' ),
        'singular_name' => __( 'Link' )
      ),
      'public' => true,
      'has_archive' => true,
    )
  );

}
	
if ( ! function_exists( 'bne_add_custom_post_types' ) ) {

	function bne_add_custom_post_types(){

		global 	$wp_post_types;
			$custom_post_types = array( 'link', 'service' );

			foreach ( $custom_post_types as $type ) {
			
				$wp_post_types[$type]->show_in_rest 		= true;
				$wp_post_types[$type]->rest_base 		= $type;
				$wp_post_types[$type]->rest_controller_class 	= 'WP_REST_Posts_Controller';
			
			}

	#	echo "<pre>";
	#	print_r ( $wp_post_types );
		
	}

}

add_action('init', 'bne_add_custom_post_types');

if ( ! function_exists( 'bne_add_meta_to_json' ) ){

	function bne_add_meta_to_json( $data, $post, $request ){
		$response_data = $data->get_data();
		if  ( $request['context'] !== 'view' ||  is_wp_error( $data ) ) {
			return $data;
		}
		if ( in_array( $post->post_type, array('post', 'link', 'service') ) ) {
			
			$response_data['_meta'] = get_post_meta( $post->ID );
		}
		$data->set_data( $response_data );
		return $data;
	}
}

add_filter( 'rest_prepare_post', 'bne_add_meta_to_json', 10, 3 ); 
add_filter( 'rest_prepare_link', 'bne_add_meta_to_json', 10, 3 ); 
add_filter( 'rest_prepare_service', 'bne_add_meta_to_json', 10, 3 );


if ( ! function_exists( 'bne_add_custom_post_tax' ) ) {

	function bne_add_custom_post_tax(){

		register_taxonomy(
		'importance',
		'post',
		array(
			'label' => __( 'Importance' ),
			'rewrite' => array( 'slug' => 'importance' ),
		)
	);

	}
}

#add_action( 'init', 'bne_add_custom_post_tax' ); 
