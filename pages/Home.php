<?php

class Sertifier_Credentials_Home {

    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_menu' ));
    }

    public function add_menu(){
        add_submenu_page( 
            'sertifier_home',
            'Home',
            'Home',
            'list_users',
            'sertifier_home',
            array( $this, 'home_page' )
        );
    }

    public function home_page(){
        include(sprintf("%s/sertifier-certificates-open-badges/templates/home.php", WP_PLUGIN_DIR));
    }
}
