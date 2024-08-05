<?php

class Alarm
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAlarmStatus($alarmId)
    {
        $query = "SELECT status, activationPassword FROM devices WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $alarmId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            if ($result) {
                return json_encode([
                    'status' => $result['status'] == 1 ? 'Alarm activated' : 'Alarm deactivated',
                    'activationPassword' => $result['activationPassword']
                ]);
            } else {
                return json_encode(['error' => 'No data found']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['error' => 'Error preparing statement']);
        }
    }

    public function getDeviceByRfid($rfid)
    {
        $query = "SELECT d.id AS device_id, d.name AS device_name, r.rfid_code, d.status, u.id AS user_id, u.username FROM rfid r INNER JOIN devices d ON r.device_id = d.id LEFT JOIN users u ON r.user_id = u.id WHERE r.rfid_code = ?";
        $stmt = $this->db->prepare($query);
  
            $stmt->bind_param('s', $rfid);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return json_encode($result);
    }


    public function updateAlarmStatus($alarmId, $status)
    {
        $query = "UPDATE devices SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ii', $status, $alarmId);
            if ($stmt->execute()) {
                return json_encode(['success' => true]);
            } else {
                return json_encode(['error' => 'Error executing statement']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['error' => 'Error preparing statement']);
        }
    }

    public function insertDetectionLog($alarmId, $action)
    {
        $query = "INSERT INTO detection_logs (alarm_id, action, timestamp) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('is', $alarmId, $action);
            if ($stmt->execute()) {
                return json_encode(['success' => true]);
            } else {
                return json_encode(['error' => 'Error executing statement']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['error' => 'Error preparing statement']);
        }
    }

    public function insertAlarmLog($alarmId, $action)
    {
        $query = "INSERT INTO alarm_logs (alarm_id, action) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('is', $alarmId, $action);
            if ($stmt->execute()) {
                return json_encode(['success' => true]);
            } else {
                return json_encode(['error' => 'Error executing statement']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['error' => 'Error preparing statement']);
        }
    }

    public function getAutomationStatus($alarmId)
    {
        $query = "SELECT status FROM automation WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $alarmId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return json_encode($result);
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['error' => 'Error preparing statement']);
        }
    }

    public function updateAutomationStatus($alarmId, $status)
    {
        $query = "UPDATE automation SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ii', $status, $alarmId);
            if ($stmt->execute()) {
                return json_encode(['success' => true]);
            } else {
                return json_encode(['error' => 'Error executing statement']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['error' => 'Error preparing statement']);
        }
    }
}
