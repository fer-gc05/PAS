<?php

class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUserByUsernameAndPassword($username, $password)
    {
        $query = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return json_encode($result);
    }

    public function getUserByUsername($username)
    {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return json_encode($result);
    }

    public function createUser($username, $password)
    {
        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $username, $password);
        return json_encode(array('success' => $stmt->execute()));
    }


    public function deleteUser($id)
    {
        if (empty($id)) {
            return json_encode(array('success' => false, 'error' => 'ID is required'));
        }

        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();

        if ($success) {
            return json_encode(array('success' => true));
        } else {
            $error = $stmt->error;
            error_log("Execute failed: $error");
            return json_encode(array('success' => false, 'error' => "Execute failed: $error"));
        }
    }

    public function getUserRfid()
    {
        $query = "SELECT u.id AS user_id, u.username, CASE WHEN r.id IS NOT NULL THEN 'Sí' ELSE 'No' END AS rfid_asociado FROM users u LEFT JOIN rfid r ON u.id = r.user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);;
        return json_encode($result);
    }

    public function getUserByRfid($rfid)
    {
        $query = "SELECT u.id AS user_id, u.username, CASE WHEN r.id IS NOT NULL THEN 'Sí' ELSE 'No' END AS rfid_asociado FROM users u LEFT JOIN rfid r ON u.id = r.user_id WHERE r.rfid_code = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $rfid);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return json_encode($result);
    }

    public function assignRfidToUser($rfid_code, $user_id, $device_id)
    {

        $query = "SELECT * FROM rfid WHERE rfid_code = ? AND user_id IS NOT NULL";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $rfid_code);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            return json_encode(array('success' => false, 'error' => 'RFID already assigned'));
        }

        $query = "SELECT * FROM rfid WHERE rfid_code = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $rfid_code);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $query = "UPDATE rfid SET user_id = ?, device_id = ? WHERE rfid_code = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('iis', $user_id, $device_id, $rfid_code);

            if ($stmt->execute()) {
                error_log("RFID asignado correctamente: $rfid_code, $user_id, $device_id");
                return json_encode(array('success' => true));
            } else {
                error_log("Error al asignar RFID: " . $stmt->error);
                return json_encode(array('success' => false, 'error' => $stmt->error));
            }
        } else {
            $query = "INSERT INTO rfid (rfid_code, user_id, device_id) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('sii', $rfid_code, $user_id, $device_id);

            if ($stmt->execute()) {
                error_log("RFID asignado correctamente: $rfid_code, $user_id, $device_id");
                return json_encode(array('success' => true));
            } else {
                error_log("Error al asignar RFID: " . $stmt->error);
                return json_encode(array('success' => false, 'error' => $stmt->error));
            }
        }
    }

    public function desassignRfid($user_id)
    {
        $query = "UPDATE rfid SET user_id = NULL WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $user_id);
        $result = $stmt->execute();
    
        if ($result) {
            return json_encode(array('success' => true));
        } else {
            return json_encode(array('success' => false, 'error' => $stmt->error));
        }
    }
    


    public function setRfidMode($mode)
    {
        $rfidStatus = ($mode === 'read') ? 1 : (($mode === 'register') ? 0 : null);

        if ($rfidStatus === null) {
            return json_encode(['error' => 'Invalid RFID mode']);
        }

        $query = "UPDATE devices SET rfid_status = ? WHERE id = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $rfidStatus);
        $success = $stmt->execute();

        if ($success) {
            return json_encode(['success' => true, 'mode' => $mode]);
        } else {
            return json_encode(['error' => 'Failed to update RFID mode']);
        }
    }


    public function getRfidMode()
    {
        $query = "SELECT rfid_status FROM devices WHERE id = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $result = ($result['rfid_status'] === 1) ? 'read' : (($result['rfid_status'] === 0) ? 'register' : null);

        return json_encode(['mode' => $result]);
    }

    public function saveRfidTemp($rfid)
    {
        if (empty($rfid)) {
            return json_encode(['error' => 'Invalid RFID']);
        }

        $query = "UPDATE devices SET rfid_temp = ? WHERE id = 1";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            return json_encode(['error' => 'Failed to prepare the SQL statement']);
        }

        $stmt->bind_param('s', $rfid);

        $result = $stmt->execute();

        if ($result) {
            return json_encode(['success' => true, 'message' => 'RFID temp value saved']);
        } else {
            return json_encode(['error' => 'Failed to save RFID temp value']);
        }
    }

    public function restartRfidTemp()
    {
        $query = "UPDATE devices SET rfid_temp = NULL WHERE id = 1";
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute();
        return json_encode(['success' => $result]);
    }

    public function getRfidTemp()
    {
        $query = "SELECT rfid_temp FROM devices WHERE id = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && isset($result['rfid_temp'])) {
            return json_encode(['rfid' => $result['rfid_temp']]);
        } else {
            return json_encode(['rfid' => null]);
        }
    }
}
