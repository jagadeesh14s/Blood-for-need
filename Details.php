<?php
session_start();
require_once 'Database.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['phone_number'])) {
    header("Location: Create_Account.php"); // Redirect to create account if not authenticated
    exit;
}

// Retrieve phone number from session
$phone_number = $_SESSION['phone_number'];

// Initialize user details array
$errors = [];

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $full_name = $_POST['full_name'] ?? '';
    $age = $_POST['age'] ?? 0;
    $gender = $_POST['gender'] ?? '';
    $area = $_POST['area'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $blood_group = $_POST['blood_group'] ?? '';
    $diseases = $_POST['diseases'] ?? ''; // Capture diseases input

    // Debug: Display the retrieved area value to verify it's correct
    echo "Debug: Area entered is: " . htmlspecialchars($area) . "<br>";

    // Validate the input
    if (empty($full_name) || empty($age) || empty($gender) || empty($area) || empty($city) || empty($state) || empty($blood_group) || empty($diseases)) {
        $errors[] = "All fields are required.";
    }

    // If there are no validation errors, proceed to update the data
    if (empty($errors)) {
        $db = new Database();

        // Prepare SQL statement to update user details
        $stmt = $db->connection->prepare("UPDATE registration SET full_name=?, age=?, gender=?, area=?, city=?, state=?, blood_group=?, diseases=? WHERE phone_number=?");

        if ($stmt) {
            // Debugging: Output the parameters being bound
            echo "Debug: Binding parameters: ";
            var_dump([$full_name, $age, $gender, $area, $city, $state, $blood_group, $diseases, $phone_number]);

            // Use 's' for strings and 'i' for integer binding
            $stmt->bind_param("sisisssss", $full_name, $age, $gender, $area, $city, $state, $blood_group, $diseases, $phone_number);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to request.php after successful insertion
                header("Location: request.php");
                exit;
            } else {
                $errors[] = "Error updating data: " . $stmt->error;
                // Debugging: Output SQL error if any
                echo "SQL Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $errors[] = "Failed to prepare the SQL statement: " . $db->connection->error;
        }

        $db->connection->close(); // Close the database connection
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood In Need - Details</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('bfn.jpg'); /* Background image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .create-account-container {
            width: 600px; /* Increased width for desktop */
            padding: 40px; /* Increased padding for better spacing */
            background-color: rgba(255, 255, 255, 0.2); /* More transparent for background */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            text-align: center;
        }
        h2 {
            color: #b22222;
            margin-bottom: 20px;
            font-weight: 600;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 15px; /* Increased padding */
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            transition: border 0.3s ease;
            background-color: rgba(255, 255, 255, 0.8); /* More opaque for readability */
        }
        button {
            width: 100%;
            padding: 15px; /* Increased padding */
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
    </style>
</head>
<body>
    <div class="create-account-container">
        <h2>Details</h2>
        <?php if (!empty($errors)): ?>
            <div style="color:red;">
                <?= implode("<br>", $errors); ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="number" name="age" placeholder="Age" required>
            <select name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" name="area" placeholder="Area" required>
            <input type="text" name="city" placeholder="City" required>
            <input type="text" name="state" placeholder="State" required>
            <select name="blood_group" required>
                <option value="" disabled selected>Select Blood Group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
            </select>
            <select name="diseases" required>
                <option value="" disabled selected>Select Diseases</option>
                <option value="HIV/AIDS">HIV/AIDS</option>
                <option value="Hepatitis B and C">Hepatitis B and C</option>
                <option value="Cancer">Cancer (especially blood-related cancers)</option>
                <option value="Chronic Kidney Disease">Chronic Kidney Disease</option>
                <option value="Autoimmune Diseases">Autoimmune Diseases</option>
                <option value="Diabetes">Diabetes (Uncontrolled)</option>
                <option value="Chronic Heart Disease">Chronic Heart Disease</option>
                <option value="Chronic Lung Disease">Chronic Lung Disease (e.g., COPD, Severe Asthma)</option>
                <option value="Blood Disorders">Blood Disorders (e.g., Sickle Cell Disease, Hemophilia)</option>
                <option value="Chronic Liver Disease">Chronic Liver Disease (e.g., Cirrhosis)</option>
                <option value="Creutzfeldt-Jakob Disease">Creutzfeldt-Jakob Disease (CJD)</option>
                <option value="Severe Obesity">Severe Obesity (with complications)</option>
                <option value="Organ Transplant Recipients">Organ Transplant Recipients</option>
                <option value="Epilepsy">Epilepsy (Uncontrolled)</option>
                <option value="Brucellosis">Brucellosis</option>
                <option value="Chronic Infections">Chronic Infections (e.g., Tuberculosis)</option>
                <option value="None">None</option>
            </select>
            <button type="submit">Confirm</button>
        </form>
    </div>
</body>
</html>
