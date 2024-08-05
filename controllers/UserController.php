<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';

class UserController
{
    private $db;
    private $userModel;

    public function __construct($db)
    {
        session_start();
        $this->userModel = new User($db);
        $this->db = $db;
    }

    public function setRfidMode($mode)
    {
        if ($mode === 'read' || $mode === 'register') {
            $result = json_decode($this->userModel->setRfidMode($mode), true);

            if (isset($result['success'])) {
                echo json_encode([
                    'success' => $result['success'],
                    'mode' => $result['mode']
                ]);
            } else {
                echo json_encode(['error' => $result['error'] ?? 'Failed to update RFID mode']);
            }
        } else {
            echo json_encode(['error' => 'Invalid mode']);
        }
    }

    public function getRfidMode()
    {
        $result = json_decode($this->userModel->getRfidMode(), true);
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Failed to fetch RFID mode']);
        }
    }

    public function saveRfidTemp($rfid_code)
    {
        $result = json_decode($this->userModel->saveRfidTemp($rfid_code), true);
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Failed to save RFID']);
        }
    }

    public function restartRfid()
    {
        $result = json_decode($this->userModel->restartRfidTemp(), true);
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Failed to restart RFID']);
        }
    }

    function getRfidTemp()
    {
        $result  = json_decode($this->userModel->getRfidTemp(), true);

        if ($result && isset($result['rfid'])) {
            echo json_encode($result);
        } else {
            echo json_encode(['rfid' => null]);
        }
    }


    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            echo json_encode(array("status" => "error", "message" => "Please fill in all fields"));
            return;
        }

        $data = json_decode($this->userModel->getUserByUsernameAndPassword($username, $password), true);
        if ($data) {
            $_SESSION['active'] = true;
            $_SESSION['username'] = $data['username'];
            $_SESSION['id'] = $data['id'];
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Invalid username or password"));
            session_destroy();
        }
    }

    public function checkSession()
    {
        if (isset($_SESSION['active'])) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "error", "message" => "No active session"));
        }
    }

    public function createUser($newUsername, $newPassword)
    {
        if (empty($newUsername) || empty($newPassword)) {
            echo json_encode(array("status" => "error", "message" => "Please fill in all fields for new user"));
            return;
        }

        $result = json_decode($this->userModel->getUserByUsername($newUsername), true);
        if ($result) {
            echo json_encode(array("status" => "error", "message" => "User already exists"));
            return;
        }

        $createResult = json_decode($this->userModel->createUser($newUsername, $newPassword), true);
        if ($createResult['success']) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to create user"));
        }
    }

    public function deleteUser($id)
    {
        $result = json_decode($this->userModel->deleteUser($id), true);
    
        if ($result['success']) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "error", "message" => $result['error']));
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        echo json_encode(array("status" => "success"));
    }

    public function assignRfidToUser($rfid_code, $user_id, $device_id)
    {
        if (empty($rfid_code) || empty($user_id) || empty($device_id)) {
            echo json_encode(array("status" => "error", "message" => "Please provide all fields for RFID assignment"));
            return;
        }

        $result = json_decode($this->userModel->assignRfidToUser($rfid_code, $user_id, $device_id), true);

        if ($result['success']) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "error", "message" => $result['error']));
        }
    }

    public function desassignRfid($id){
        $result = json_decode($this->userModel->desassignRfid($id), true);
        if ($result['success']) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "error", "message" => $result['error']));
        }
    }

    public function listUserRfid()
    {
        $result = json_decode($this->userModel->getUserRfid(), true);
        if ($result) {
            echo json_encode(array("status" => "success", "data" => $result));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to fetch RFID data"));
        }
    }
}
