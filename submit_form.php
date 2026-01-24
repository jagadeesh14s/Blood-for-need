<?php
session_start();
require_once 'Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_account'])) {
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $errors = [];

    // Validate input
    if (empty($phone_number) || empty($password)) {
        $errors[] = "Phone number and password are required.";
    }

    // Check if phone number already exists
    $db = new Database();
    $stmt = $db->connection->prepare("SELECT phone_number FROM registration WHERE phone_number = ?");
    $stmt->bind_param("s", $phone_number);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "This phone number is already registered.";
    }

    // If no errors, proceed to insert the new user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->connection->prepare("INSERT INTO registration (phone_number, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $phone_number, $hashed_password);
        
        if ($stmt->execute()) {
            // Redirect to Details.php with the user's phone number
            $_SESSION['user_id'] = $phone_number; // Assuming phone_number is unique
            header("Location: Details.php?id=" . urlencode($phone_number));
            exit;
        } else {
            $errors[] = "There was an error creating your account. Please try again.";
        }
    }

    // Handle errors
    if (!empty($errors)) {
        header("Location: Create_Account.php?error=" . urlencode(implode(", ", $errors)));
        exit;
    }
}
?>
