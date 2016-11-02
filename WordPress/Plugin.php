<?php
namespace DanielPost\CPTT\WordPress;

class Plugin {
    
    const TEXT_DOMAIN = 'dp-cppt';

    public function __construct() {
        // Initialize admin functionality
        if( is_admin() ) {
            new Admin();
        }

        add_filter( 'single_template', array( $this, 'custom_single_template' ) );
    }

    public function custom_single_template( $template ) {
        global $post;

        $post_meta = ( $post ) ? get_post_meta( $post->ID ) : null;
        if ( isset($post_meta['_wp_page_template'][0]) && ( $post_meta['_wp_page_template'][0] != 'default' ) ) {
            $template = get_stylesheet_directory() . '/' . $post_meta['_wp_page_template'][0];
        }

        return $template;
    }

}