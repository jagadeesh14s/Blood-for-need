<?php
require_once 'Database.php';
require_once 'twilio-php/src/Twilio/autoload.php';

use Twilio\Rest\Client;

// Twilio credentials
$accountSid = 'AC04755ef929b01042bd2ee7c9be5f8e93';
$authToken = '2e5a842b042cf2ae4e75d6ea003b60a2';
$twilioPhoneNumber = '+15042221608';

// Read the incoming Twilio message
$from = $_POST['From'] ?? '';
$body = trim(strtolower($_POST['Body'] ?? ''));

if (!empty($from) && !empty($body)) {
    $db = new Database();

    // Check if the sender exists in the registration table
    $stmt = $db->connection->prepare("SELECT phone_number, chat_id FROM registration WHERE phone_number = ?");
    $stmt->bind_param("s", $from);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $chat_id = $row['chat_id'];

        // Process Yes/No response
        if ($body === 'yes') {
            $responseMessage = "Thank you! The requester will be notified of your willingness to donate.";
            $donorResponse = "YES";
        } elseif ($body === 'no') {
            $responseMessage = "Thank you for your response. We appreciate your time.";
            $donorResponse = "NO";
        } else {
            $responseMessage = "Invalid response. Please reply with YES or NO.";
            $donorResponse = "INVALID";
        }

        // Store response in the database (if valid)
        if ($donorResponse !== "INVALID") {
            $stmt = $db->connection->prepare("INSERT INTO responses (phone_number, response, chat_id) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $from, $donorResponse, $chat_id);
            $stmt->execute();
        }

        // Send acknowledgment message to the donor
        $client = new Client($accountSid, $authToken);
        $client->messages->create(
            $from,
            [
                'from' => $twilioPhoneNumber,
                'body' => $responseMessage
            ]
        );

        // Notify the requester via Telegram (if valid response)
        if ($donorResponse !== "INVALID") {
            $telegramToken = "YOUR_TELEGRAM_BOT_TOKEN"; // Replace with your bot token
            $messageText = "Response from {$from}: " . strtoupper($donorResponse);
            file_get_contents("https://api.telegram.org/bot{$telegramToken}/sendMessage?chat_id={$chat_id}&text={$messageText}");
        }

        echo "Response received and processed.";
    } else {
        echo "Sender not found.";
    }

    $stmt->close();
    $db->connection->close();
} else {
    echo "Invalid request.";
}
?>
