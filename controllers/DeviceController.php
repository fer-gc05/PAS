<?php
require_once __DIR__. '/../models/Device.php';
require_once __DIR__. '/../config/database.php';

class DeviceController {
    private $db;
    private $deviceModel;

    public function __construct($db) {
        $this->deviceModel = new Device($db);
        $this->db = $db;
    }

    public function checkPassword($password) {
        $device = json_decode($this->deviceModel->getDeviceById(1), true);
        if ($device) {
            if ($password === $device["activationPassword"]) {
                echo json_encode(['status' => 'success', 'message' => 'Password correct']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Password incorrect']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No device found']);
        }
    }

    public function updatePassword($currentPassword, $newPassword) {
        $result = json_decode($this->deviceModel->getActivationPassword(1), true);
        if ($result) {
            $storedPassword = $result["activationPassword"];
            if ($currentPassword === $storedPassword) {
                if ($this->deviceModel->updateActivationPassword(1, $newPassword)) {
                    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'The current password is incorrect']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No device found']);
        }
    }
}