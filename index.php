<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AlarmController.php';
require_once __DIR__ . '/controllers/AutomationController.php';
require_once __DIR__ . '/controllers/DeviceController.php';
require_once __DIR__ . '/controllers/LogsController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/services/SendNotifications.php';
require_once __DIR__ . '/services/TwilioCall.php';

$db = new Connection();
$conn = $db->getConnection();


$alarmController = new AlarmController($conn);
$automationController = new AutomationController($conn);
$deviceController = new DeviceController($conn);
$logController = new LogController($conn);
$userController = new UserController($conn);


$bot = new TelegramBot('7313027953:AAFe6WB9p7_NtR0i2OfOH2ceuFCTbSpXnlI');
$twilioCall = new TwilioCall('AC45e7f1db0ab1ebd10432a30e246daa18', 'e413b1dff0e04ad12632b6144dd11435', '+14153902319');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
            // Alarm actions
        case 'checkAlarmStatus':
            $alarmId = isset($_POST['alarmId']) ? $_POST['alarmId'] : 1;
            $alarmController->checkAlarmStatus($alarmId);
            break;

        case 'toggleAlarm':
            $rfid = $_POST['rfid'];
            $alarmController->toggleAlarm($rfid);
            break;

        case 'turnOnAlarm':
            $password = $_POST['password'];
            $alarmController->turnOnAlarm($password);
            break;

        case 'turnOffAlarm':
            $password = $_POST['password'];
            $alarmController->turnOffAlarm($password);
            break;

        case 'checkStatus':
            $alarmController->checkStatus();
            break;

        case 'intruderDetected':
            $message = "Intruder detected";
            $bot->sendMessagesToAllUsers($message);
            $toPhoneNumber = '+573244209118';
            $twilioCall->makeCall($toPhoneNumber);
            $alarmController->intruderDetected();
            break;

            // Automation actions
        case 'activateAutomation':
            $automationController->activateAutomation();
            break;

        case 'deactivateAutomation':
            $automationController->deactivateAutomation();
            break;

        case 'updateAutomationConfiguration':
            $turnOnHour = $_POST['turnOnHour'];
            $turnOffHour = $_POST['turnOffHour'];
            $automationController->updateAutomationConfiguration($turnOnHour, $turnOffHour);
            break;

        case 'getAutomationStatus':
            $automationController->getAutomationStatus();
            break;

        case 'checkAutomation':
            $automationController->checkAutomation();
            break;

            // Device actions
        case 'checkPassword':
            $password = $_POST['password'];
            $deviceController->checkPassword($password);
            break;

        case 'updatePassword':
            $currentPassword = $_POST['currentPassword'];
            $newPassword = $_POST['newPassword'];
            $deviceController->updatePassword($currentPassword, $newPassword);
            break;

            // Log actions
        case 'getAlarmLogs':
            $startTime = isset($_POST['startTime']) ? $_POST['startTime'] : null;
            $endTime = isset($_POST['endTime']) ? $_POST['endTime'] : null;
            $search = isset($_POST['search']) ? $_POST['search'] : null;
            $logController->getAlarmLogs($startTime, $endTime, $search);
            break;

        case 'getDetectionLogs':
            $startTime = isset($_POST['startTime']) ? $_POST['startTime'] : null;
            $endTime = isset($_POST['endTime']) ? $_POST['endTime'] : null;
            $logController->getDetectionLogs($startTime, $endTime);
            break;

            // User actions
        case 'login':
            $username = $_POST['username'];
            $password = $_POST['password'];
            $userController->login($username, $password);
            break;

        case 'checkSession':
            $userController->checkSession();
            break;

        case 'createUser':
            $newUsername = $_POST['newUsername'];
            $newPassword = $_POST['newPassword'];
            $userController->createUser($newUsername, $newPassword);
            break;

        case 'deleteUser':
            $id = $_POST['id'];
            $userController->deleteUser($id);
            break;

        case 'logout':
            $userController->logout();
            break;

            // RFID actions
        case 'assignRfidToUser':
            $rfid_code = $_POST['rfid_code'];
            $user_id = $_POST['user_id'];
            $device_id = $_POST['device_id'];
            $userController->assignRfidToUser($rfid_code, $user_id, $device_id);
            break;

        case 'desassignRfid':
            $id = $_POST['id'];
            $userController->desassignRfid($id);
            break;

        case 'listUserRfid':
            $userController->listUserRfid();
            break;

        case 'setRfidMode':
            $newMode = $_POST['newMode'];
            $userController->setRfidMode($newMode);
            break;

        case 'getRfidMode':
            $userController->getRfidMode();
            break;

        case 'saveRfid':
            $rfid_code = $_POST['rfid_code'];
            $userController->saveRfidTemp($rfid_code);
            break;

        case 'restartRfid':
            $userController->restartRfid();
            break;

        case 'getRfid':
            $userController->getRfidTemp();
            break;

        default:
            echo json_encode(['error' => 'Unrecognized action']);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Manejo de solicitudes GET para vistas
    $view = isset($_GET['view']) ? $_GET['view'] : 'login';

    if (!isset($_SESSION['id']) && $view !== 'login') {
        header('Location: index.php?view=login');
        exit();
    }

    switch ($view) {
        case 'login':
            if (isset($_SESSION['id'])) {
                header('Location: index.php?view=alarm');
                exit();
            }
            require __DIR__ . '/views/login.html';
            break;
        case 'alarm':
            require __DIR__ . '/views/alarm.html';
            break;
        case 'alarmLogs':
            require __DIR__ . '/views/alarmLogs.html';
            break;
        case 'detectionLogs':
            require __DIR__ . '/views/detectionLogs.html';
            break;
        case 'users':
            require __DIR__ . '/views/users.html';
            break;
        default:
            require __DIR__ . '/views/404.html';
            break;
    }
} else {
    echo json_encode(['error' => 'Method not allowed']);
}