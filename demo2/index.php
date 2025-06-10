<?php
require_once 'config/database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'register') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if (empty($username) || empty($email) || empty($password)) {
                $message = "All fields are required!";
            } elseif ($password !== $confirm_password) {
                $message = "Passwords don't match!";
            } elseif ($user->usernameExists($username)) {
                $message = "Username already exists!";
            } elseif ($user->emailExists($email)) {
                $message = "Email already exists!";
            } else {
                if ($user->register($username, $email, $password)) {
                    $message = "Registration successful! You can now login.";
                } else {
                    $message = "Registration failed!";
                }
            }
        } elseif ($_POST['action'] == 'login') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $message = "Username and password are required!";
            } else {
                if ($user->login($username, $password)) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Invalid username or password!";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 800px;
            display: flex;
            min-height: 500px;
        }

        .form-container {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-container.hidden {
            display: none;
        }

        .side-panel {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        h2 {
            margin-bottom: 30px;
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .switch-btn {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 10px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .switch-btn:hover {
            background: white;
            color: #667eea;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 400px;
            }
            
            .side-panel {
                order: -1;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Login Form -->
        <div class="form-container" id="loginForm">
            <h2>Welcome Back</h2>
            <?php if ($message && isset($_POST['action']) && $_POST['action'] == 'login'): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label for="login_username">Username</label>
                    <input type="text" id="login_username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="login_password">Password</label>
                    <input type="password" id="login_password" name="password" required>
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>
        </div>

        <!-- Registration Form -->
        <div class="form-container hidden" id="registerForm">
            <h2>Create Account</h2>
            <?php if ($message && isset($_POST['action']) && $_POST['action'] == 'register'): ?>
                <div class="message <?php echo (strpos($message, 'successful') !== false) ? 'success' : ''; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="register">
                
                <div class="form-group">
                    <label for="reg_username">Username</label>
                    <input type="text" id="reg_username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="reg_email">Email</label>
                    <input type="email" id="reg_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="reg_password">Password</label>
                    <input type="password" id="reg_password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="reg_confirm_password">Confirm Password</label>
                    <input type="password" id="reg_confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn">Register</button>
            </form>
        </div>

        <!-- Side Panel -->
        <div class="side-panel">
            <div id="loginPanel">
                <h3>New Here?</h3>
                <p>Sign up and discover great features!</p>
                <button class="switch-btn" onclick="showRegister()">Sign Up</button>
            </div>
            
            <div id="registerPanel" style="display: none;">
                <h3>Already Have Account?</h3>
                <p>Sign in to access your account!</p>
                <button class="switch-btn" onclick="showLogin()">Sign In</button>
            </div>
        </div>
    </div>

    <script>
        function showRegister() {
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registerForm').classList.remove('hidden');
            document.getElementById('loginPanel').style.display = 'none';
            document.getElementById('registerPanel').style.display = 'block';
        }

        function showLogin() {
            document.getElementById('registerForm').classList.add('hidden');
            document.getElementById('loginForm').classList.remove('hidden');
            document.getElementById('registerPanel').style.display = 'none';
            document.getElementById('loginPanel').style.display = 'block';
        }

        // Show register form if there's a registration message
        <?php if ($message && isset($_POST['action']) && $_POST['action'] == 'register'): ?>
            showRegister();
        <?php endif; ?>
    </script>
</body>
</html>