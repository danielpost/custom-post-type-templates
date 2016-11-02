<?php
/**
 * Plugin Name: Custom Post Type Templates
 * Plugin URI: http://www.danielpost.com
 * Description: Adds the ability to use templates on custom post types. Template naming: {cpt-slug}-{template-name}.php. 
 * Author: Daniel Post
 * Version: 1.0
 * Author URI: http://www.danielpost.com
 */
namespace DanielPost;

use DanielPost\CPTT\WordPress\Plugin;

spl_autoload_register(__NAMESPACE__ . '\\autoload');

new Plugin();

function autoload( $class ) {
    if( ! strstr( $class, 'DanielPost\CPTT' ) ) {
        return;
    }

    $result = str_replace( 'DanielPost\CPTT\\', '', $class );
    $result = str_replace( '\\', '/', $result );

    require $result . '.php';
}