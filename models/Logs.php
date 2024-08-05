<?php

class Log
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAlarmLogs($startTime = null, $endTime = null, $search = null) {
        $query = "SELECT * FROM alarm_logs WHERE 1";
    
        if ($startTime && $endTime) {
            $query .= " AND DATE(timestamp) BETWEEN ? AND ?";
        } elseif ($startTime) {
            $query .= " AND DATE(timestamp) >= ?";
        } elseif ($endTime) {
            $query .= " AND DATE(timestamp) <= ?";
        }
    
        if ($search) {
            $query .= " AND action LIKE ?";
            $search = "%$search%";
        }
    
        $query .= " ORDER BY timestamp DESC";
        
        $stmt = $this->db->prepare($query);
    
        if ($stmt) {
            $params = [];
            $types = '';
    
            if ($startTime && $endTime) {
                $types .= 'ss';
                $params[] = $startTime;
                $params[] = $endTime;
            } elseif ($startTime) {
                $types .= 's';
                $params[] = $startTime;
            } elseif ($endTime) {
                $types .= 's';
                $params[] = $endTime;
            }
    
            if ($search) {
                $types .= 's';
                $params[] = $search;
            }
    
            if ($types) {
                $stmt->bind_param($types, ...$params);
            }
    
            $stmt->execute();
            $result = $stmt->get_result();
            $logs = [];
    
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
    
            return json_encode($logs);
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }
    


    public function getDetectionLogs($startTime = null, $endTime = null)
    {
        $query = "SELECT * FROM detection_logs WHERE 1";

        if ($startTime && $endTime) {
            $query .= " AND DATE(timestamp) BETWEEN ? AND ?";
        } elseif ($startTime) {
            $query .= " AND DATE(timestamp) >= ?";
        } elseif ($endTime) {
            $query .= " AND DATE(timestamp) <= ?";
        }

        $query .= " ORDER BY timestamp DESC";

        $stmt = $this->db->prepare($query);

        if ($stmt) {
            if ($startTime && $endTime) {
                $stmt->bind_param('ss', $startTime, $endTime);
            } elseif ($startTime) {
                $stmt->bind_param('s', $startTime);
            } elseif ($endTime) {
                $stmt->bind_param('s', $endTime);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $logs = [];

            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }

            return json_encode($logs);
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }
}
