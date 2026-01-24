<?php
session_start();
require_once 'Database.php'; // Include your database connection file

$errors = [];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $phone_number = $_POST['phone_number'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($phone_number) || empty($password)) {
        $errors[] = "All fields are required.";
    } elseif (!preg_match('/^\d{10}$/', $phone_number)) { // Example validation for phone number
        $errors[] = "Please enter a valid 10-digit phone number.";
    }

    // If there are no validation errors, proceed to save the account
    if (empty($errors)) {
        $db = new Database();

        // Prepend the country code +91 to the phone number
        $phone_number = '+91' . $phone_number;

        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate a unique chat ID (you can adjust this logic as needed)
        $chat_id = uniqid('chat_', true);

        // Prepare statement to insert the new user with chat_id
        $stmt = $db->connection->prepare("INSERT INTO registration (phone_number, password, chat_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $phone_number, $hashed_password, $chat_id);

        if ($stmt->execute()) {
            $_SESSION['phone_number'] = $phone_number; // Store phone number in session
            header("Location: details.php"); // Redirect to details.php after successful account creation
            exit;
        } else {
            $errors[] = "Account creation failed. Please try again.";
        }

        $stmt->close();
        $db->connection->close(); // Close the database connection
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blood In Need - Create Account</title>
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #f7f9fc;
      background-image: url('bfn.jpg'); /* Added background image */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    .container {
      width: 400px; /* Adjusted width for desktop view */
      padding: 40px; /* Increased padding for better spacing */
      background-color: rgba(255, 255, 255, 0.2); /* Adjusted for transparency */
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Slightly more prominent shadow */
      border-radius: 15px;
      text-align: center;
      animation: slideIn 0.8s ease-out forwards;
    }

    h2 {
      color: #b22222;
      margin-bottom: 20px;
      font-weight: 600;
    }

    input[type="text"], input[type="password"], input[type="tel"] {
      width: 100%; /* Full width for inputs */
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ddd;
      border-radius: 10px;
      outline: none;
      transition: border 0.3s ease;
      background-color: rgba(255, 255, 255, 0.9); /* Slightly more opaque input fields for readability */
    }

    input[type="text"]:focus, input[type="password"]:focus, input[type="tel"]:focus {
      border: 1px solid #b22222;
    }

    button {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      background-color: #b22222;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
      background-color: #a12020;
      transform: translateY(-2px);
    }

    @keyframes slideIn {
      0% {
        opacity: 0;
        transform: translateY(50px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Create Account</h2>
    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <?= implode("<br>", $errors); ?>
        </div>
    <?php endif; ?>
    <form action="" method="POST">
      <input type="tel" name="phone_number" placeholder="Phone Number (10 digits)" required pattern="\d{10}"><br>
      <input type="password" name="password" placeholder="Password" required id="password">
      <button type="submit">Confirm</button>
    </form>
  </div>
</body>
</html>
