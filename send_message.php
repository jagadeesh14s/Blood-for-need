<?php
session_start();
require_once 'Database.php';
require_once 'twilio-php/src/Twilio/autoload.php';

use Twilio\Rest\Client;

// Twilio credentials
$accountSid = 'AC04755ef929b01042bd2ee7c9be5f8e93';
$authToken = '2e5a842b042cf2ae4e75d6ea003b60a2';
$twilioPhoneNumber = '+15042221608';

$client = new Client($accountSid, $authToken);

// Retrieve the selected blood groups from POST data
$selected_groups = isset($_POST['selected_groups']) ? explode(',', $_POST['selected_groups']) : [];
$requester_phone = $_SESSION['user'] ?? '';

$bloodGroupsPlaceholder = implode(',', array_fill(0, count($selected_groups), '?'));

if (!empty($selected_groups)) {
    $db = new Database();

    $stmt = $db->connection->prepare("
        SELECT phone_number 
        FROM registration 
        WHERE blood_group IN ($bloodGroupsPlaceholder) 
        AND phone_number != ? 
        AND diseases = 'None'
    ");

    $types = str_repeat('s', count($selected_groups)) . 's';
    $params = array_merge($selected_groups, [$requester_phone]);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $usersFound = false;

        while ($row = $result->fetch_assoc()) {
            try {
                // Constructing the message with Yes/No instructions
                $messageBody = "A user with phone number {$requester_phone} is requesting your blood donation. 
Reply with:
YES - If you are willing to donate
NO - If you are unable to donate";

                $message = $client->messages->create(
                    $row['phone_number'],
                    [
                        'from' => $twilioPhoneNumber,
                        'body' => $messageBody
                    ]
                );

                echo "Message sent to: " . htmlspecialchars($row['phone_number']) . " SID: " . $message->sid . "<br>";
                $usersFound = true;
            } catch (Exception $e) {
                echo "Error sending message to " . htmlspecialchars($row['phone_number']) . ": " . $e->getMessage() . "<br>";
            }
        }

        if (!$usersFound) {
            echo "There is no user with the blood group you are requesting for.";
        }
    } else {
        echo "Error fetching phone numbers: " . $stmt->error;
    }

    $stmt->close();
    $db->connection->close();
} else {
    echo "No blood groups selected.";
}
?>
