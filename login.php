<?php
session_start();
include 'db_connection.php';

$error_message = ''; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the email and password keys exist in the POST array
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Store user ID and email in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email']; // Store email in session

                echo "<script>
                        alert('Login successful!');
                        window.location.href = 'index.html';
                      </script>";
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "User  not found.";
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
		
		.login-container p{
			text-align: center;
			margin-bottom: 50px;
		}
		
        .login-container h2 {
            margin-bottom: 20px;
			text-align: center;
        }
		
        .login-container input {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
			font-size: 16px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background: #5cb85c;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
			font-size: 16px;
			margin-bottom: 30px;
			margin-top: 20px;
        }
        .login-container button:hover {
            background: #4cae4c;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="login-container">
		<p>Don't have an account? <a href="register.php">Register</a></p>
        <h2>Login Form</h2>
        <form method="POST" action="">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
			<div style="margin-left: 380px"><a href="#">Forgot Password?</a></div>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>