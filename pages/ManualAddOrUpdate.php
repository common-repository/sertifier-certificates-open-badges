<?php

class Sertifier_Credentails_ManualAddOrUpdate {

    private $api;

    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_menu' ));

        add_action('admin_enqueue_scripts', array( $this, 'plugin_css_jsscripts' ));

        require_once( plugin_dir_path( SERTIFIER_FILE ) . "/classes/api.php" );
        $this->api = new Sertifier_Api(get_option("sertifier_api_key"));
    }

    public function plugin_css_jsscripts($hook) {
        if ( !str_contains($hook, "sertifier_add_or_update") ) {
            return;
        }
    }

    public function add_menu(){
        add_submenu_page( 
            'sertifier_home',
            'Send now',
            'Send now',
            'list_users',
            'sertifier_manual_add_or_update',
            array( $this, 'add_or_update_page' )
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

    public function add_or_update_page(){
        if(!current_user_can('manage_options'))
        {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        
        global $wpdb;

        $type = "manual";

        $action = "add";
        if(isset($_GET["id"])){
            $action = "edit";
        }


        $deliveries = $this->api->get_deliveries();
        $deliveryfilter = array();
        foreach ($deliveries->data->deliveries as $delivery) {
            if ($this->sertifier_delivery_check($delivery)) {
                $deliveryfilter[$delivery->id] = $delivery->title;
            }
        }

        $courses = get_posts([
            'post_status' => array('publish', 'private'),
            'post_type' => 'courses',
            'numberposts' => -1
        ]);

        if($_POST){
            map_deep($_POST, 'sanitize_text_field');
            
            $required = ["delivery_id","usersNew"];
            
            if (count(array_intersect_key(array_flip($required), $_POST)) != count($required)) {
                wp_cache_add("sertifier_error_message","Check required fields.",'',10);
                include(sprintf("%s/sertifier-certificates-open-badges/templates/add-or-update.php", WP_PLUGIN_DIR));
                return;
            }

            $db_data = array(
                'course_id' => sanitize_text_field(@$_POST['course_id']),
                'lesson_id' => sanitize_text_field(@$_POST['lesson_id']) != 0 ? sanitize_text_field($_POST['lesson_id']) : NULL,
                'delivery_id' => sanitize_text_field(@$_POST['delivery_id']),
                'type' => 1
            );
            if($action == "add"){
                $wpdb->insert( $wpdb->prefix."sertifier_issues", $db_data );
                $message = 1;
            }else {
                $wpdb->update( $wpdb->prefix."sertifier_issues", $db_data, ["id" => sanitize_text_field($_GET["id"])] );
                $message = 2;
            }

            $record = $wpdb->get_row(
                $wpdb->prepare("
                    SELECT * FROM {$wpdb->prefix}sertifier_issues
                    WHERE id = %s
                ", sanitize_text_field($_GET['id']))
            );

            $alreadyRecipients = $this->api->get_recipients($_POST["delivery_id"])->data->recipients;
            $alreadyrecipientsemail = array_column($alreadyRecipients, "email");
            $users = get_users([
                "fields" => ["ID","display_name", "user_email"],
                "include" => explode(",", sanitize_text_field($_POST["usersNew"]))
            ]);
            $userEmails = array_column($users,'user_email');

            // Deleted recipients
            $deletedEmails = array_diff($alreadyrecipientsemail,$userEmails);
            $toDeleteCertificateNos = [];
            foreach ($deletedEmails as $email) {
                $toDeleteCertificateNos[] = $alreadyRecipients[array_search($email, array_column($alreadyRecipients, 'email'))]->certificateNo;
            }
            $this->api->delete_recipients($toDeleteCertificateNos);

            // New added recipients
            $toAddedRecipientEmails = array_diff($userEmails,$alreadyrecipientsemail);
            $recipients = [];
            foreach ($toAddedRecipientEmails as $email) {
                $user = $users[array_search($email, array_column($users, 'user_email'))];
                $recipients[] = [
                    "name" => $user->display_name,
                    "email" => $user->user_email,
                    "issueDate" => date("Y-m-d"),
                    "quickPublish" => true
                ];
            }
            $response = $this->api->add_recipients($_POST["delivery_id"], $recipients);

            wp_redirect(add_query_arg(['page' => 'sertifier_manual_issues','message' => $message], admin_url('admin.php')));
            exit;
            
        }else {
            $usedDeliveryIds = [];
            if($action == "edit"){
                $record = $wpdb->get_row(
                    $wpdb->prepare("
                        SELECT * FROM {$wpdb->prefix}sertifier_issues
                        WHERE id = %s
                    ", sanitize_text_field($_GET['id']))
                );
            }else {
                $usedDeliveryIds = array_column(
                    $wpdb->get_results(
                        $wpdb->prepare("
                            SELECT delivery_id FROM {$wpdb->prefix}sertifier_issues
                            WHERE type = %d
                        ", 1),
                    "ARRAY_A"),
                "delivery_id"
                );
            }

            include(sprintf("%s/sertifier-certificates-open-badges/templates/add-or-update.php", WP_PLUGIN_DIR));
        }
    }
}