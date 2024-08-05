<?php
require_once 'SendNotifications.php';

$bot = new TelegramBot('7313027953:AAFe6WB9p7_NtR0i2OfOH2ceuFCTbSpXnlI');
$update = file_get_contents('php://input');
$update = json_decode($update, true);

$bot->handleUpdate($update);