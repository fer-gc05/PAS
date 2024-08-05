<?php

class Device {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getDeviceById($id) {
        $query = "SELECT * FROM devices WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                return json_encode($result->fetch_assoc());
            } else {
                return json_encode(['message' => 'No device found']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }

    public function getActivationPassword($id) {
        $query = "SELECT activationPassword FROM devices WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                return json_encode($result->fetch_assoc());
            } else {
                return json_encode(['message' => 'No device found']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }

    public function updateActivationPassword($id, $newPassword) {
        $query = "UPDATE devices SET activationPassword = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->bind_param('si', $newPassword, $id);
            if ($stmt->execute()) {
                return json_encode(['message' => 'Password updated successfully']);
            } else {
                return json_encode(['message' => 'Error updating password']);
            }
        } else {
            error_log("Error preparing statement: " . $this->db->error);
            return json_encode(['message' => 'Error preparing statement']);
        }
    }
}