<?php
require_once __DIR__. '/../models/Logs.php';
require_once __DIR__. '/../config/database.php';

class LogController {
    private $db;
    private $logModel;

    public function __construct($db) {
        $this->logModel = new Log($db);
        $this->db = $db;
    }

    public function getAlarmLogs($startTime = null, $endTime = null, $search = null) {
        $logs = json_decode($this->logModel->getAlarmLogs($startTime, $endTime, $search), true);
        if (is_array($logs)) { 
            echo json_encode($logs);
        } else {
            echo json_encode(['message' => 'Error fetching alarm logs']);
        }
    }
    
    public function getDetectionLogs($startTime = null, $endTime = null) {
        $logs = json_decode($this->logModel->getDetectionLogs($startTime, $endTime), true);
        if (is_array($logs)) { 
            echo json_encode($logs);
        } else {
            echo json_encode(['message' => 'Error fetching detection logs']);
        }
    }
}