<?php
require_once __DIR__ . '/../models/Automation.php';
require_once __DIR__ . '/../config/database.php';

class AutomationController {
    private $db;
    private $automation;

    public function __construct($db) {
        $this->db = $db;
        $this->automation = new Automation($db);
    }

    public function activateAutomation() {
        $response = $this->automation->updateAutomationStatus(1);
        header('Content-Type: application/json');
        echo $response;
    }

    public function deactivateAutomation() {
        $response = $this->automation->updateAutomationStatus(0);
        header('Content-Type: application/json');
        echo $response;
    }

    public function updateAutomationConfiguration($turnOnHour, $turnOffHour) {
        $turnOnHour = date("H:i", strtotime($turnOnHour));
        $turnOffHour = date("H:i", strtotime($turnOffHour));
        $response = $this->automation->updateAutomationConfiguration($turnOnHour, $turnOffHour);
        header('Content-Type: application/json');
        echo $response;
    }

    public function getAutomationStatus() {
        $response = $this->automation->getAutomationStatus();
        header('Content-Type: application/json');
        echo $response;
    }

    public function checkAutomation() {
        date_default_timezone_set('America/Bogota');
        $data = json_decode($this->automation->getAutomationAndDeviceStatus(), true);
        if (!$data) {
            error_log("Automation configuration or device status not found");
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Automation configuration or device status not found']);
            return;
        }

        $response = $this->processAutomation($data);
        header('Content-Type: application/json');
        echo $response;
    }

    private function processAutomation($data) {
        $status = $data['automation_status'];
        $turnOnHour = date('H:i', strtotime($data['turnOnHour']));
        $turnOffHour = date('H:i', strtotime($data['turnOffHour']));
        $currentTime = date('H:i');
        $currentDeviceStatus = $data['device_status'];

        if ($status == 1) {
            if ($currentTime == $turnOnHour) {
                return $this->updateDeviceStatus($currentDeviceStatus, 1, "Alarma activada por automatización");
            } else if ($currentTime == $turnOffHour) {
                $this->automation->updateAutomationStatus(0);
                return $this->updateDeviceStatus($currentDeviceStatus, 0, "Alarma desactivada por automatización");
            }
        }
        return json_encode(['message' => 'Automation process completed']);
    }

    private function updateDeviceStatus($currentDeviceStatus, $newStatus, $logMessage) {
        if ($currentDeviceStatus != $newStatus) {
            $response = json_decode($this->automation->updateDeviceStatus($newStatus), true);
            if ($response['message'] == 'Device status updated') {
                return $this->automation->logAlarmAction(1, $logMessage);
            } else {
                error_log("Error updating device status");
                return json_encode(['message' => 'Error updating device status']);
            }
        }
        return json_encode(['message' => 'Device status unchanged']);
    }
}