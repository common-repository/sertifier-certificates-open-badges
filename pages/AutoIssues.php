<?php 

class Sertifier_Credentials_AutoIssues {
    
    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_menu' ));
        require_once( plugin_dir_path( SERTIFIER_FILE ) . "/classes/api.php" );
        add_action( 'wp_ajax_delete_auto_issue', array( $this, 'delete_auto_issue' ) );
    }

    public function add_menu(){
        add_submenu_page( 
            'sertifier_home',
            'Automated sending',
            'Automated sending',
            'list_users',
            'sertifier_auto_issues',
            array( $this, 'admin_page' )
        );

    }

    public function sertifier_delivery_check($delivery) {
        if (
            $delivery->type == 11 &&
            $delivery->detailId != "00000000-0000-0000-0000-000000000000" &&
            ($delivery->designId != "00000000-0000-0000-0000-000000000000" ||
                $delivery->badgeId != "00000000-0000-0000-0000-000000000000") &&
            $delivery->emailTemplateId != "00000000-0000-0000-0000-000000000000" &&
            !empty($delivery->emailFromName) &&
            !empty($delivery->mailSubject)
        ) {
            return true;
        }

        return false;
    }

    public function admin_page(){
        if(!current_user_can('manage_options'))
        {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        global $wpdb;

        $records = $wpdb->get_results(
            $wpdb->prepare("
                SELECT A.*,B.post_title as course_title FROM {$wpdb->prefix}sertifier_issues A
                JOIN {$wpdb->prefix}posts B ON A.course_id = B.id
                WHERE type = %d
            ", 0)
        );

        $api = new Sertifier_Api(get_option("sertifier_api_key"));

        $deliveries = $api->get_deliveries();
        $deliveryfilter = array();
        foreach ($deliveries->data->deliveries as $delivery) {
            if ($this->sertifier_delivery_check($delivery)) {
                $deliveryfilter[$delivery->id] = $delivery->title;
            }
        }

        foreach ($records as $record) {
            $record->delivery_title = $deliveryfilter[$record->delivery_id];
        }

        include(sprintf("%s/sertifier-certificates-open-badges/templates/auto-issues.php", WP_PLUGIN_DIR));
    }

    public function delete_auto_issue(){
        if(!$_POST || empty(@$_POST["id"]) || !current_user_can('manage_options'))
            return;

        global $wpdb;

        $wpdb->delete($wpdb->prefix . "sertifier_issues", ["id" => sanitize_text_field($_POST["id"])]);
        echo json_encode([]);
        wp_die(); 
    }
}