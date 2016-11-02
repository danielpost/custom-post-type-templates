<?php
namespace DanielPost\CPTT\WordPress;

class Plugin {
    
    const TEXT_DOMAIN = 'dp-cppt';

    public function __construct() {
        // Initialize admin functionality
        if( is_admin() ) {
            new Admin();
        }
    }

}