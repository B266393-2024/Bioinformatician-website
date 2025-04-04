<?php
// The PHP content in this part is mainly used to start or continue the current session. The session contains user information. 
// The try part implements the database connection.
// In the if part, the function of allocating temporary visitor accounts is implemented.
session_start();  
try {
    $dsn = 'mysql:host=127.0.0.1;dbname=s2704757_my_first_db';
    $dbUser = 's2704757';
    $dbPass = 'Ziyiyang@2002!';
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    if (!isset($_SESSION['user_id'])) {
        $guestUsername = 'guest_' . uniqid();
        $guestPasswordHash = password_hash($guestUsername, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash)
                               VALUES (:username, :pass_hash)");
        $stmt->execute([
            ':username' => $guestUsername,
            ':pass_hash' => $guestPasswordHash
        ]);

        $guestUserId = $pdo->lastInsertId(); 
        $_SESSION['user_id'] = $guestUserId;
    }

} catch (PDOException $e) {
    echo 'Database connection failed: ' . $e->getMessage();
    exit;
}
?>


 
<!-- The html content of this part mainly defines the layout of the entire search interface. -->
<!-- In the Header area, the navigation function is set and a logic is added. If the user is logged in, the user name and Log out button are displayed. If not logged in, the Log in button is displayed. At the same time, the history button is displayed for users to view search records. --> 
<!-- The search bar is displayed in the middle content area. The sample database is displayed by default. After the user submits, it will be posted to search.php for search. --> 
<!-- The footer part at the bottom displays footer links, Help, About, and Statement of Credits.-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bioinformatician</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <!-- Top Header -->
  <header class="header">
    <h1>Bioinformatician</h1>
    <div class="auth-links">
      <a href="history.php">History</a>
      <?php if (isset($_SESSION['username'])): ?>
        <span style="margin-left: 15px;">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" style="margin-left: 10px;">Log out</a>
      <?php else: ?>
        <a href="login.php" style="margin-left: 15px;">Log in</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Main Content Section (Centered Form) -->
<div class="container" style="display: flex; flex-direction: column; align-items: center;">
  <div style="text-align: center; margin-bottom: 40px;">
    <h1 style="font-size: 36px; font-weight: bold; color: #333; text-shadow: 1px 1px 4px rgba(0,0,0,0.1);">
      Protein Sequence Analysis
    </h1>
    <p style="color: #222; text-shadow: 1px 1px 3px rgba(0,0,0,0.1);">
      A platform to obtain protein sequences, perform conservation analysis, and visualize data.
    </p>
    <p style="color: #222; text-shadow: 1px 1px 3px rgba(0,0,0,0.1);">
      Eenter Taxonomic Group and Protein Family or use example set aves glucose-6-phosphatase to explore.
    </p>
  </div>

  <div class="form-container">
    <form action="search.php" method="post">
      <div class="form-group">
        <label for="taxonomic-group">Taxonomic Group</label>
        <input type="text" id="taxonomic-group" name="taxonomic-group" placeholder="aves">
      </div>
      <div class="form-group">
        <label for="protein-family">Protein Family</label>
        <input type="text" id="protein-family" name="protein-family" placeholder="glucose-6-phosphatase">
      </div>
      <button type="submit" class="start-btn">Start</button>
    </form>
  </div>
</div>


  <!-- Footer -->
  <footer class="footer">
    <a href="help.php">Help</a>
    <a href="about.php">About</a>
    <a href="statement.php">Statement of Credits</a>
  </footer>

</body>
</html>