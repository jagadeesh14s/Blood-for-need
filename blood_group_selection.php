<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Blood Group</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f7f9fc;
            background-image: linear-gradient(to right, #f7f9fc, #e2eafc);
        }
        .container {
            width: 320px;
            padding: 30px;
            background-color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            text-align: center;
        }
        h2 {
            color: #b22222;
            margin-bottom: 20px;
            font-weight: 600;
        }
        select {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            transition: border 0.3s ease;
        }
        select:focus {
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
        .error-messages {
            color: red;
            list-style-type: none;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select Your Blood Group</h2>

        <?php if (isset($_GET['error'])): ?>
            <ul class="error-messages">
                <li><?php echo htmlspecialchars($_GET['error']); ?></li>
            </ul>
        <?php endif; ?>

        <form action="submit_form.php" method="POST">
            <select name="blood_group" required>
                <option value="" disabled selected>Select your blood group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
            </select>
            <button type="submit" name="submit_blood_group">Submit</button>
        </form>
    </div>
</body>
</html>
