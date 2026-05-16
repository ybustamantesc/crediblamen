<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_pld extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pld_model');
    }

    // CLI entry point: php index.php cron_pld run
    public function run()
    {
        if (!is_cli()) {
            echo "This script must be run from CLI\n";
            return;
        }

        // Scaffold: load rules and scan recent transactions (placeholder)
        log_message('info', 'Cron_pld: starting scan');

        $rules = $this->Pld_model->get_rules();
        // TODO: implement scanning of transactions

        log_message('info', 'Cron_pld: finished scan');
    }
}
