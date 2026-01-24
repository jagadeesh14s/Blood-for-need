<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize errors array
$errors = [];

// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your database username
$password_db = ""; // Replace with your database password
$dbname = "form"; // Replace with your database name

// Create a database connection
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the phone number and password from the POST request
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement to fetch the user record
    $stmt = $conn->prepare("SELECT password FROM registration WHERE phone_number = ?");
    $stmt->bind_param("s", $phone_number);
    $stmt->execute();
    $stmt->store_result();

    // Check if a matching record was found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the entered password against the stored hashed password
        if (password_verify($password, $hashed_password)) { // Secure comparison with hashed passwords
            $_SESSION['user'] = $phone_number; // Save phone number in session
            header('Location: request.php'); // Redirect to request.php
            exit; // Stop further script execution after redirect
        } else {
            $errors[] = "Invalid phone number or password.";
        }
    } else {
        $errors[] = "Invalid phone number or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blood In Need - Login</title>
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #f7f9fc;
      background-image: url('bfn.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    .login-container {
      width: 420px;
      max-width: 90%;
      padding: 40px;
      background-color: rgba(255, 255, 255, 0.2); /* Increased transparency */
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      border-radius: 15px;
      text-align: center;
      animation: slideIn 0.8s ease-out forwards;
    }

    h2 {
      color: #b22222;
      margin-bottom: 20px;
      font-weight: 600;
    }

    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 14px;
      margin: 12px 0;
      border: 1px solid #ddd;
      border-radius: 10px;
      outline: none;
      transition: border 0.3s ease;
    }

    input[type="text"]:focus, input[type="password"]:focus {
      border: 1px solid #b22222;
    }

    button {
      width: 100%;
      padding: 14px;
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

    .password-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 10px 0;
      font-size: 0.9rem;
      line-height: 1.5;
    }

    .forgot-password {
      color: #b22222;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .forgot-password:hover {
      text-decoration: underline;
      color: #a12020;
    }

    .account-message {
      margin: 15px 0;
      color: #555;
      font-size: 0.9rem;
      text-align: left;
    }

    .create-account-link {
      color: #b22222;
      font-weight: bold;
      text-decoration: none;
      cursor: pointer;
    }

    .create-account-link:hover {
      text-decoration: underline;
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
  <div class="login-container">
    <h2>Login</h2>
    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <?= implode("<br>", $errors); ?>
        </div>
    <?php endif; ?>
    <form action="" method="POST">
      <input type="text" name="phone_number" placeholder="Phone Number" required><br>
      <input type="password" name="password" placeholder="Password" required id="password">
      <div class="password-options">
        <label>
          <input type="checkbox" onclick="togglePassword()"> Show Password
        </label>
        <a href="forgot-password.html" class="forgot-password">Forgot Password?</a>
      </div>
      <button type="submit">Login</button>
      <p class="account-message">
        Don't have an account? 
        <a href="Create_Account.php" class="create-account-link">Create Account</a>
      </p>
    </form>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>
