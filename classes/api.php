<?php

class Sertifier_Api {
	private $api_key;

	private $api_endpoint = "https://b2b.sertifier.com";

    private $headers;
    
	/**
	 * Set API Key
	 * @param String $key
	 * @return null
	 */
	public function setAPIKey($key) {
        $this->api_key = $key;
    }
    
    /**
     * Get API Key
     * @return String
     */
    public function getAPIKey() {
        return $this->api_key;
    }

    public function __construct($api_key){
        $this->setAPIKey($api_key);
        $this->headers = [
            "secretKey" => $this->api_key,
            "Content-Type" => "application/json",
            "api-version" => "2.2"
        ];
    }

    public function get_deliveries(){
        $args = [
            "body" => json_encode([]),
            "headers" => $this->headers
        ];
        $response = wp_remote_post( "{$this->api_endpoint}/Delivery/GetAllDeliveries", $args );
        return json_decode($response["body"]);
    }

    public function add_recipients($deliveryId,$recipients){
        $args = [
            "body" => json_encode([
                "deliveryId" => $deliveryId,
                "recipients" => $recipients
            ]),
            "headers" => $this->headers
        ];
        $response = wp_remote_post( "{$this->api_endpoint}/Delivery/AddRecipients", $args );
        return json_decode($response["body"]);
    }
    
    public function get_recipients($deliveryId){
        $args = [
            "body" => json_encode([
                "id" => $deliveryId,
            ]),
            "headers" => $this->headers
        ];
        $response = wp_remote_post( "{$this->api_endpoint}/Delivery/ListRecipients", $args );
        return json_decode($response["body"]);
    }

    public function delete_recipients($certificateNos){
        $args = [
            "body" => json_encode([
                "certificateNos" => $certificateNos,
            ]),
            "headers" => $this->headers
        ];
        $response = wp_remote_post( "{$this->api_endpoint}/Recipient/DeleteCertificates", $args );
        return json_decode($response["body"]);
    }
}