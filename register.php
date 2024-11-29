<?php
session_start();
include 'db_connection.php';

$message = ""; // Variable to hold messages
$registration_success = false; // Flag for successful registration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashed_password);

    if ($stmt->execute()) {
        $message = "User  registered successfully!";
        $registration_success = true; // Set flag to true on successful registration
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
		
		form p{
			text-align: center;
		}
		
		form h2 {
			margin-top: 50px;
			text-align: center;
		}
		
        input {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
			font-size: 16px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
			margin-top : 20px;
			font-size: 16px;
			margin-bottom: 30px;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
    <script>
        // Function to show alert if registration is successful
        function showAlert() {
            alert("<?php echo $message; ?>");
            // Redirect to login page after alert
            window.location.href = "login.php"; // Change 'login.php' to your actual login page
        }
    </script>
</head>
<body>
    <form method="POST" action="">
		<p>Already have an account ? <a href="login.php">Login</a></p>
		<h2>Registration Form</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
		
    </form>

    <?php if ($registration_success): ?>
        <script>
            // Call the showAlert function if registration was successful
            window.onload = showAlert;
        </script>
    <?php endif; ?>
</body>
</html>