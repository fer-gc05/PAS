<?php

class Automation {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAutomationStatus() {
        $query = "SELECT status, turnOnHour, turnOffHour FROM automation WHERE id = 1";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $data = $result->fetch_assoc();
                $data['turnOnHour'] = date('H:i', strtotime($data['turnOnHour']));
                $data['turnOffHour'] = date('H:i', strtotime($data['turnOffHour']));
                return json_encode($data);
            } else {
                return json_encode(['message' => 'No data found']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }

    public function updateAutomationStatus($status) {
        $query = "UPDATE automation SET status = ? WHERE id = 1";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $status);
            if ($stmt->execute()) {
                return json_encode(['message' => 'Automation status updated']);
            } else {
                return json_encode(['message' => 'Error updating automation status']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }

    public function updateAutomationConfiguration($turnOnHour, $turnOffHour) {
        $query = "UPDATE automation SET status = 1, turnOnHour = ?, turnOffHour = ? WHERE id = 1";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ss', $turnOnHour, $turnOffHour);
            if ($stmt->execute()) {
                return json_encode(['message' => 'Configuration updated successfully']);
            } else {
                return json_encode(['message' => 'Error updating configuration']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }

    public function getAutomationAndDeviceStatus() {
        $query = "
            SELECT a.status AS automation_status, a.turnOnHour, a.turnOffHour, d.status AS device_status
            FROM automation a
            JOIN devices d ON d.id = 1
            WHERE a.id = 1";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                return json_encode($result->fetch_assoc());
            } else {
                return json_encode(['message' => 'No data found']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }

    public function updateDeviceStatus($newState) {
        $query = "UPDATE devices SET status = ? WHERE id = 1";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $newState);
            if ($stmt->execute()) {
                return json_encode(['message' => 'Device status updated']);
            } else {
                return json_encode(['message' => 'Error updating device status']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }

    public function logAlarmAction($alarmId, $action) {
        $query = "INSERT INTO alarm_logs (alarm_id, action) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param("is", $alarmId, $action);
            if ($stmt->execute()) {
                return json_encode(['message' => 'Alarm action logged']);
            } else {
                return json_encode(['message' => 'Error logging alarm action']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }
}
