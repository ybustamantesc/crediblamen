<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pld_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get_clients_for_kyc()
    {
        // Placeholder: return simple list
        return $this->db->select('id, nombre, documento')->get('tb_clientes')->result();
    }

    public function save_kyc($payload)
    {
        $this->db->trans_start();
        $insert = array(
            'client_id' => isset($payload['client_id']) ? $payload['client_id'] : NULL,
            'document_type' => isset($payload['document_type']) ? $payload['document_type'] : NULL,
            'document_number' => isset($payload['document_number']) ? $payload['document_number'] : NULL,
            'first_name' => isset($payload['first_name']) ? $payload['first_name'] : NULL,
            'last_name' => isset($payload['last_name']) ? $payload['last_name'] : NULL,
            'birth_date' => isset($payload['birth_date']) ? $payload['birth_date'] : NULL,
            'address' => isset($payload['address']) ? $payload['address'] : NULL,
            'phone' => isset($payload['phone']) ? $payload['phone'] : NULL,
            'email' => isset($payload['email']) ? $payload['email'] : NULL,
            'notes' => isset($payload['notes']) ? $payload['notes'] : NULL,
            'documents' => isset($payload['documents']) ? $payload['documents'] : NULL,
            'created_by' => isset($payload['created_by']) ? $payload['created_by'] : NULL,
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('pld_kyc', $insert);
        $insertId = $this->db->insert_id();

        // audit
        $audit = array(
            'entity' => 'pld_kyc',
            'entity_id' => $insertId,
            'action' => 'create',
            'user_id' => isset($payload['created_by']) ? $payload['created_by'] : NULL,
            'notes' => 'KYC creado',
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('pld_audits', $audit);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $insertId;
    }

    public function get_alerts($filters = [])
    {
        // Placeholder: empty
        return [];
    }

    public function get_rules()
    {
        return $this->db->get('pld_rules')->result();
    }

}
