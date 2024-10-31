<?php

class Sertifier_Credentails_Settings {

    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_menu' ));
        add_action( 'admin_init', array( $this, 'settings_init' ));
    }

    public function add_menu(){
        add_submenu_page( 
            'sertifier_home',
            'Settings',
            'Settings',
            'list_users',
            'sertifier_settings',
            array( $this, 'settings_page' )
        );
    }

    public function settings_page(){
        if(!current_user_can('manage_options'))
        {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        
        if($_POST){
            global $wpdb;
            $wpdb->query("TRUNCATE TABLE `" . $wpdb->prefix . "sertifier_issues" . "`");

            update_option("sertifier_api_key", sanitize_text_field($_POST["api_key"]));
        }

        include(sprintf("%s/sertifier-certificates-open-badges/templates/settings.php", WP_PLUGIN_DIR));
    }

    public function settings_init(){
        register_setting('sertifier_certificate-group', 'sertifier_api_key');
    }
}