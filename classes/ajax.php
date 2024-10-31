<?php

class Sertifier_Ajax {

    private $api;

    public function __construct(){
        add_action( 'wp_ajax_get_lessons', array( $this, 'get_lessons' ) );
        add_action( 'wp_ajax_get_users', array( $this, 'get_users' ) );
        
        require_once( plugin_dir_path( SERTIFIER_FILE ) . "/classes/api.php" );
        $this->api = new Sertifier_Api(get_option("sertifier_api_key"));
    }

    public function get_lessons(){
        global $wpdb;
        $course_id = sanitize_text_field($_POST["course_id"]);

        $lessons = $wpdb->get_results(
            $wpdb->prepare("
                SELECT A.id, A.post_title FROM {$wpdb->prefix}posts A
                JOIN {$wpdb->prefix}posts B ON A.post_parent = B.id
                WHERE A.post_type='lesson' AND B.post_parent = %s
            ", $course_id)
        );

        echo json_encode($lessons);
        wp_die(); 
    }

    public function get_users(){
        map_deep($_POST, 'sanitize_text_field');

        global $wpdb;

        $query = [
            "fields" => ["ID","display_name", "user_email"]
        ];

        if(isset($_POST["course_id"]) && !empty($_POST["course_id"])){
            $posts = get_posts([
                'post_status' => 'completed',
                'post_type' => 'tutor_enrolled',
                'post_parent' => sanitize_text_field($_POST["course_id"]),
                'numberposts' => -1
            ]);
            $author_ids = [];
            foreach ($posts as $post) {
                $author_ids[] = $post->post_author;
            }
            $author_ids = array_unique($author_ids);
            $query["include"] = $author_ids;
        }

        if(isset($_POST["query"]) && !empty($_POST["query"])){
            $query["search"] = "*" . sanitize_text_field($_POST["query"]) . "*";
            $query["search_columns"] = ["display_name","user_email"];
        }

        $users = get_users($query);

        $selectedUserIds = [];

        if(isset($_POST["delivery_id"]) && !empty(@$_POST["delivery_id"])){
            $alreadyRecipients = $this->api->get_recipients($_POST["delivery_id"])->data->recipients;
            $alreadyRecipientsEmails = array_column($alreadyRecipients, "email");
            foreach ($users as $user) {
                if(array_search($user->user_email, $alreadyRecipientsEmails) !== false){
                    $selectedUserIds[] = $user->ID;
                }
            }
        }

        echo json_encode([
            "users" => $users,
            "selectedUserIds" => $selectedUserIds,
        ]);
        wp_die(); 
    }
}