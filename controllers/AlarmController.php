<?php
require_once __DIR__ . '/../models/Alarm.php';
require_once __DIR__ . '/../config/database.php';

class AlarmController
{
    private $db;
    private $alarmModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->alarmModel = new Alarm($db);
    }

    public function checkAlarmStatus($alarmId)
    {
        $result = $this->alarmModel->getAlarmStatus($alarmId);
        echo $result;
    }

    public function toggleAlarm($rfid)
    {
        $result = json_decode($this->alarmModel->getDeviceByRfid($rfid), true);

        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error']]);
            return;
        }

        if (empty($result)) {
            echo json_encode(['error' => 'No device found']);
            return;
        }

        $alarmId = $result['device_id'];
        $status = json_decode($this->alarmModel->getAlarmStatus($alarmId), true);

        if (isset($status['error'])) {
            echo json_encode(['error' => $status['error']]);
            return;
        }

        $currentStatus = $status['status'];
        $newStatus = $currentStatus == 'Alarm activated' ? 0 : 1;

        $updateResult = $this->alarmModel->updateAlarmStatus($alarmId, $newStatus);

        if (isset($updateResult['error'])) {
            echo json_encode(['error' => $updateResult['error']]);
            return;
        } else {
            $message = $newStatus == 1 ? 'Alarm activated' : 'Alarm deactivated';
            echo json_encode(['success' => $message]);

            $action = $newStatus == 1 ? 'Alarma activada por ' . $result['username'] : 'Alarma desactivada por ' . $result['username'];
            $this->alarmModel->insertAlarmLog($alarmId, $action);

            if ($newStatus == 0) {
                $this->deactivateAutomation($alarmId);
            }

        }
    }


    public function turnOnAlarm($password)
    {
        $result = json_decode($this->alarmModel->getAlarmStatus(1), true);
        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error']]);
            return;
        }

        $storedPassword = $result['activationPassword'];
        if ($result['status'] == 'Alarm deactivated') {
            if ($password == $storedPassword) {
                $updateResult = $this->alarmModel->updateAlarmStatus(1, 1);
                if (json_decode($updateResult, true)['success']) {
                    echo json_encode(['message' => 'Alarm activated']);
                    $this->alarmModel->insertAlarmLog(1, 'Alarma activada por interfaz web');
                } else {
                    echo json_encode(['error' => 'Error activating alarm']);
                }
            } else {
                echo json_encode(['error' => 'Incorrect password']);
            }
        } else {
            echo json_encode(['message' => 'Alarm already activated']);
        }
    }

    public function turnOffAlarm($password)
    {
        $result = json_decode($this->alarmModel->getAlarmStatus(1), true);
        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error']]);
            return;
        }

        $storedPassword = $result['activationPassword'];
        if ($result['status'] == 'Alarm activated') {
            if ($password == $storedPassword) {
                $updateResult = $this->alarmModel->updateAlarmStatus(1, 0);
                if (json_decode($updateResult, true)['success']) {
                    echo json_encode(['message' => 'Alarm deactivated']);
                    $this->alarmModel->insertAlarmLog(1, 'Alarma desactivada por interfaz web');
                    $this->deactivateAutomation(1);
                } else {
                    echo json_encode(['error' => 'Error deactivating alarm']);
                }
            } else {
                echo json_encode(['error' => 'Incorrect password']);
            }
        } else {
            echo json_encode(['message' => 'Alarm already deactivated']);
        }
    }


    public function checkStatus()
    {
        $result = $this->alarmModel->getAlarmStatus(1);
        echo $result;
    }

    public function intruderDetected()
    {
        $result = $this->alarmModel->insertDetectionLog(1, 'Intruso detectado');
        echo $result;
    }

    private function deactivateAutomation($alarmId)
    {
        $result = json_decode($this->alarmModel->getAutomationStatus($alarmId), true);
        if (isset($result['error'])) {
            echo json_encode(['error' => $result['error']]);
            return;
        }

        if ($result['status'] == 1) {
            $updateResult = $this->alarmModel->updateAutomationStatus($alarmId, 0);
            if (json_decode($updateResult, true)['success']) {
                echo json_encode(['message' => 'Automation deactivated']);
            } else {
                echo json_encode(['error' => 'Error deactivating automation']);
            }
        }
    }
}
