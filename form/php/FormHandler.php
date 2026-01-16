<?php

class FormHandler {
    private $db = null;
    
    public function __construct($dbConnection = null) {
        $this->db = $dbConnection;
    }
    
    public function validate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Имя обязательно для заполнения';
        }
        
        if (!isset($data['agreement']) || !$data['agreement']) {
            $errors[] = 'Необходимо согласие';
        }
        
        return $errors;
    }
    
    public function saveToDatabase($data) {
        if (!$this->db) {
            return $this->saveToFile($data);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO form_submissions (name, mood_color, comment, radio_option, agreement, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['name'],
                isset($data['mood_color']) ? $data['mood_color'] : '#0000ff',
                isset($data['comment']) ? $data['comment'] : '',
                isset($data['radio']) ? $data['radio'] : '',
                isset($data['agreement']) ? 1 : 0
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    
    private function saveToFile($data) {
        $filename = __DIR__ . '/form_submissions.json';
        $submissions = [];
        
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $submissions = $content ? json_decode($content, true) : [];
            if (!is_array($submissions)) {
                $submissions = [];
            }
        }
        
        $submissions[] = [
            'name' => $data['name'],
            'mood_color' => isset($data['mood_color']) ? $data['mood_color'] : '#0000ff',
            'comment' => isset($data['comment']) ? $data['comment'] : '',
            'radio' => isset($data['radio']) ? $data['radio'] : '',
            'agreement' => isset($data['agreement']),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return file_put_contents($filename, json_encode($submissions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }
    
    public function sendToAPI($data, $apiUrl) {
        $ch = curl_init($apiUrl);
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 300;
    }

    public function getAllSubmissions() {
        if (!$this->db) {
            $filename = __DIR__ . '/form_submissions.json';
            if (file_exists($filename)) {
                return json_decode(file_get_contents($filename), true) ?: [];
            }
            return [];
        }
        
        try {
            $stmt = $this->db->query("SELECT * FROM form_submissions ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }
}
