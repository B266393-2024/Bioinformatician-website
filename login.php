<?php
/// This part of the code first starts the session and establishes a PDO connection with the MySQL database. Then, after receiving the POST request, it obtains and trims the username and password from the form and determines whether the input is empty. If it is empty, it sets an error feedback. Otherwise, it queries the database to see if the username exists. If not, it automatically registers a new user (hashes the password and inserts it into the database) and stores the user information in the SESSION. If it exists, it verifies the correctness of the password, updates the SESSION information based on the verification result, and sets the corresponding feedback message.
session_start();
$dsn = 'mysql:host=127.0.0.1;dbname=s2704757_my_first_db';
$dbUser = 's2704757';
$dbPass = 'Ziyiyang@2002!';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $feedback = "Username or Password cannot be empty";
    } else {
        $stmt = $pdo->prepare("SELECT user_id, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $insert->execute([$username, $hash]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            $feedback = "Registration successful. Welcome, $username!";
        } else {
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $username;
                $feedback = "Login success! Hello, $username";
            } else {
                $feedback = "Incorrect password for user: $username";
            }
        }
    }
}
?>

<!-- The HTML structure defines the layout of the entire page, including a top navigation bar (header) showing the website name and a link to return to the home page, a center-aligned container showing a login/registration form or feedback information, and a footer containing help, about, and thank you links. The page also sets background images, fonts, and responsive layouts through inline styles and external CSS files. -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login or Auto-Register</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-image: url('back.jpg'); background-size: cover; background-position: center; background-attachment: fixed; display: flex; flex-direction: column; min-height: 100vh;">

    <div class="header" style="background-color: #3b3c50; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="margin-left: 20px; font-size: 24px;">Bioinformatician</h1>
        <div class="auth-links" style="margin-right: 20px;">
            <a href="index.php" style="color: white; margin-left: 15px; text-decoration: none;">Back to Home</a>
        </div>
    </div>

    <div class="container" style="flex: 1; display: flex; justify-content: center; align-items: center; margin: 20px; margin-top: 140px;">
        <div class="form-container" style="background-color: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 400px; text-align: center;">
            
            <?php if (isset($feedback)): ?>
                <h2 style="margin-bottom: 20px;">Notice</h2>
                <p style="font-size: 1.1em; margin-bottom: 20px;"><?php echo $feedback; ?></p>
                <?php if ($feedback === "Login success! Hello, $username" || str_starts_with($feedback, 'Registration successful')): ?>
                    <a href="index.php" style="display: inline-block; padding: 10px 20px; background-color: #3b3c50; color: white; border-radius: 5px; text-decoration: none;">Go to Home</a>
                <?php else: ?>
                    <a href="login.php" style="display: inline-block; padding: 10px 20px; background-color: #3b3c50; color: white; border-radius: 5px; text-decoration: none;">Try Again</a>
                <?php endif; ?>
            <?php else: ?>
                <h2>Login or Auto-Register</h2>
                <form action="login.php" method="POST">
                    <label style="display: block; margin-bottom: 10px; text-align: center;">Username</label>
                    <input type="text" name="username" style="width: 100%; padding: 12px; font-size: 1em; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 25px;" required>

                    <label style="display: block; margin-bottom: 10px; text-align: center;">Password</label>
                    <input type="password" name="password" id="password" placeholder="At least 8 chars, 1 upper, 1 lower, 1 number" style="width: 100%; padding: 12px; font-size: 1em; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 10px;" required>
                    <div id="password-message" style="font-size: 0.9em; margin-top: 8px; color: red; display: none;">The password must be at least eight characters long and contain uppercase and lowercase letters and numbers</div>

                    <button type="submit" class="start-btn" style="background-color: #3b3c50; color: white; padding: 12px; font-size: 1.2em; border: none; border-radius: 5px; cursor: pointer; width: 100%; margin-top: 20px;">Submit</button>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <div class="footer" style="text-align: center; background-color: #f1f1f1; padding: 10px 0;">
        <a href="help.php" style="margin: 0 25px; color: #333; text-decoration: none;">Help</a>
        <a href="about.php" style="margin: 0 25px; color: #333; text-decoration: none;">About</a>
        <a href="statement.php" style="margin: 0 25px; color: #333; text-decoration: none;">Statement of Credits</a>
    </div>


<!--  The JavaScript code is mainly used for real-time password strength detection. It monitors the input events of the password input box and uses regular expressions to determine whether the password meets the conditions of at least 8 characters and contains uppercase and lowercase letters and numbers. Then, the corresponding prompt information is displayed below the input box (red prompts that it does not meet the requirements, and green prompts that the password is strong enough) so that users can immediately understand the security of the password. -->
    <script>
        const passwordInput = document.getElementById('password');
        const message = document.getElementById('password-message');

        if (passwordInput) {
            passwordInput.addEventListener('input', () => {
                const value = passwordInput.value;
                const valid = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(value);

                if (value.length === 0) {
                    message.style.display = 'none';
                } else if (!valid) {
                    message.style.display = 'block';
                    message.style.color = 'red';
                    message.textContent = "Password must be at least 8 characters, include uppercase, lowercase, and a number.";
                } else {
                    message.style.display = 'block';
                    message.style.color = 'green';
                    message.textContent = "Strong password!";
                }
            });
        }
    </script>

</body>
</html>





