<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        
        if(!$this->input->is_cli_request()) {
            die('Request is not permited.');
        }
        
        $this->load->library('Ci_Rabbitmq');
    }
    
    public function index() {
        echo '=========================================' . PHP_EOL;
        echo 'CRON: ' . __METHOD__ . PHP_EOL;
        echo '=========================================' . PHP_EOL;
        
        $channel_name = "my_message";
        $channel = $this->ci_rabbitmq->getChannel($channel_name);
        
        $channel->queue_declare($channel_name, false, false, false, false);
        $channel->basic_consume($channel_name, '', false, true, false, false, function ($msg) {
            echo '[x] Received message: ' . $msg->body . PHP_EOL;
        });
        
        while($channel->is_open()) {
            $channel->wait();
        }
        
        $this->ci_rabbitmq->close();
    }
}
