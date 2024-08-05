<?php

class TelegramBot
{
    private $botToken;
    private $dataFile = 'users.json';

    public function __construct($botToken)
    {
        $this->botToken = $botToken;
    }

    public function handleUpdate($update)
    {
        if (isset($update['message'])) {
            $chatId = $update['message']['from']['id'];
            $username = $update['message']['from']['username'];
            $text = $update['message']['text'];

            if ($text == '/start') {
                $this->handleStartCommand($chatId, $username);
            }
        }
    }

    private function handleStartCommand($chatId, $username)
    {
        $welcomeMessage = "Hola, $username! Te has registrado para recibir notificaciones.";
        $this->sendMessage($chatId, $welcomeMessage);

        $users = $this->loadUsers();
        if (!array_key_exists($chatId, $users)) {
            $users[$chatId] = [
                'username' => $username,
                'chat_id' => $chatId
            ];

            $this->saveUsers($users);
        }
    }

    private function loadUsers()
    {
        $users = [];
        if (file_exists($this->dataFile)) {
            $jsonData = file_get_contents($this->dataFile);
            $users = json_decode($jsonData, true);
        }
        return $users;
    }

    private function saveUsers($users)
    {
        file_put_contents($this->dataFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    private function sendMessage($chatId, $message)
    {
        file_get_contents("https://api.telegram.org/bot$this->botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message));
    }

    public function sendMessagesToAllUsers($message)
    {
        $users = $this->loadUsers();
        foreach ($users as $user) {
            $chatId = $user['chat_id'];
            $this->sendMessage($chatId, $message);
        }
    }
}