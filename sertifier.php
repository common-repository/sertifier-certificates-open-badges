<?php
/**
* Plugin Name: Sertifier Certificates & Open Badges - Tutor LMS
* Plugin URI: https://www.sertifier.com/
* Description: Send certificates and badges to your participants via sertifier with automations specific to Tutor LMS.
* Version: 1.15
* Author: Sertifier
* Author URI: https://sertifier.com/
* License: GPLv2 or later
**/

define( 'SERTIFIER_FILE', __FILE__ );

class Sertifier {

    private $api;

    public function __construct(){
        ob_clean();
        ob_start();

        add_action( 'admin_menu', array( $this, 'sertifier_admin_menu_page' ));
        
        require_once( plugin_dir_path( __FILE__ ) . '/pages/Home.php' );
        $Sertifier_Credentials_Home = new Sertifier_Credentials_Home();

        require_once( plugin_dir_path( __FILE__ ) . '/pages/ManualAddOrUpdate.php' );
        $Sertifier_Credentails_ManualAddOrUpdate = new Sertifier_Credentails_ManualAddOrUpdate();

        require_once( plugin_dir_path( __FILE__ ) . '/pages/ManualIssues.php' );
        $Sertifier_Credentials_ManualIssues = new Sertifier_Credentials_ManualIssues();

        require_once( plugin_dir_path( __FILE__ ) . '/pages/AutoAddOrUpdate.php' );
        $Sertifier_Credentails_AutoAddOrUpdate = new Sertifier_Credentails_AutoAddOrUpdate();

        require_once( plugin_dir_path( __FILE__ ) . '/pages/AutoIssues.php' );
        $Sertifier_Credentials_AutoIssues = new Sertifier_Credentials_AutoIssues();

        require_once( plugin_dir_path( __FILE__ ) . '/pages/Settings.php' );
        $Sertifier_Credentails_Settings = new Sertifier_Credentails_Settings();

        require_once( plugin_dir_path( __FILE__ ) . '/classes/api.php' );

        require_once( plugin_dir_path( __FILE__ ) . '/classes/ajax.php' );
        $Sertifier_Ajax = new Sertifier_Ajax();

        $this->create_database();
        $this->tutor_hooks();

        wp_enqueue_style('styles', plugins_url( '/assets/css/style.css', __FILE__ ), array());
        
        if($_GET["page"] == "sertifier_manual_issues" || $_GET["page"] == "sertifier_auto_issues" || $_GET["page"] == "sertifier_auto_add_or_update" || $_GET["page"] == "sertifier_manual_add_or_update"){
            wp_enqueue_script( 'script-js', plugins_url( '/assets/js/admin.js', __FILE__ ), array('jquery'));
        }
        
        wp_localize_script( 'script-js', 'plugin_ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

        $this->api = new Sertifier_Api(get_option("sertifier_api_key"));
    }

    public function sertifier_admin_menu_page(){
        add_menu_page( 
            'Sertifier Certificates & Badges',
            'Sertifier Certificates & Badges',
            'list_users',
            'sertifier_home',
            array ( $this, 'home_page'),
            'dashicons-tablet'
        );
    }

    public function create_database(){
		global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        
		$sertifier_deliveries = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sertifier_issues (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			course_id bigint(20) DEFAULT NULL,
			lesson_id bigint(20) DEFAULT NULL,
			delivery_id text DEFAULT NULL,
            type int DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sertifier_deliveries );
    }

    public function tutor_hooks(){
        add_action("tutor_course_complete_after", array($this, 'tutor_course_complete_after'),10,2);
        add_action("tutor_lesson_completed_after", array($this, 'tutor_lesson_completed_after'),10,2);
    }

    public function tutor_course_complete_after($course_id,$user_id){
        global $wpdb;
        
        $integration = $wpdb->get_row(
            $wpdb->prepare("
                SELECT * FROM {$wpdb->prefix}sertifier_issues
                WHERE course_id = %s AND lesson_id is null
            ", $course_id )
        );
        if($integration != null){
            $user_data = get_user_by("id",$user_id);
            $res = $this->api->add_recipients($integration->delivery_id, [
                [
                    "name" => $user_data->data->display_name,
                    "email" => $user_data->data->user_email,
                    "quickPublish" => true,
                    "attributes" => [],
                    "issueDate" => date("Y-m-d")
                ]
            ]);
        }
    }

    public function tutor_lesson_completed_after($lesson_id, $user_id){
        global $wpdb;
        
        $integration = $wpdb->get_row(
            $wpdb->prepare("
                SELECT * FROM {$wpdb->prefix}sertifier_issues
                WHERE lesson_id = %s
            ", $lesson_id )
        );
        if($integration != null){
            $user_data = get_user_by("id",$user_id);
            $res = $this->api->add_recipients($integration->delivery_id, [
                [
                    "name" => $user_data->data->display_name,
                    "email" => $user_data->data->user_email,
                    "quickPublish" => true,
                    "attributes" => [],
                    "issueDate" => date("Y-m-d")
                ]
            ]);
        }
    }
}

$Sertifier = new Sertifier();