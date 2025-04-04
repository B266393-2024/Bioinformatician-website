<?php
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href='login.php'>log in</a> to view your history.</p>";
    exit();
}

// Database connection
$dsn = 'mysql:host=127.0.0.1;dbname=s2704757_my_first_db';
$username = 's2704757';
$password = 'Ziyiyang@2002!';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user history
    $stmt = $pdo->prepare("SELECT * FROM analysis_history WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analysis History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .history-panel { background: rgba(255,255,255,0.95); padding: 40px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 90%; margin: 120px auto; }
        .history-card { background: #f9f9f9; padding: 15px; margin-bottom: 25px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; }
        .history-card:hover { background: #f1f1f1; }
        .history-details { display: none; margin-top: 10px; }
        .row { display:flex; justify-content: space-between; align-items: center ; margin-bottom: 10px; }
        .meta { margin-top: 5px; font-size: 0.95em; color: #555; }
    </style>
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh;">
<div class="header">
    <h1>Analysis History</h1>
    <div class="auth-links">
        <a href="index.php">Back to Search</a>
        <a href="result.php">Back Search Result</a>
    </div>
</div>

<div class="history-panel" style="flex:1;">
    <h2>Hello, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>! Here is your analysis history:</h2>

    <?php if (empty($history)) {
        echo "<p>No history found.</p>";
    } else {
        foreach ($history as $record) {
            echo "<div class='history-card'>";
            echo "<div class='row' style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;'>";
            echo "<div style='flex: 0 0 auto;'><strong>Analysis Type:</strong> " . htmlspecialchars($record['analysis_type']) . "</div>";
            echo "<div style='flex: 0 0 auto; text-align: right;'><strong>Date:</strong> " . htmlspecialchars($record['created_at']) . "</div>";
            echo "</div>";

            if (in_array($record['analysis_type'], ['structure', 'motif'])) {
                $acc = htmlspecialchars($record['accession'] ?? '-');
                $org = htmlspecialchars($record['organism'] ?? '-');
                echo "<div class='meta'><strong>Accession:</strong> $acc | <strong>Organism:</strong> $org</div>";
            }

            echo "<div class='history-details'>";
            echo "<pre>" . htmlspecialchars($record['result_text'] ?? '') . "</pre>";
            if (!empty($record['image_path'])) {
                $images = json_decode($record['image_path'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($images)) {
                    foreach ($images as $img) {
                        echo "<img src='$img' alt='Analysis image' style='max-width:100%; margin-top:10px;'>";
                    }
                } else {
                    echo "<img src='" . htmlspecialchars($record['image_path']) . "' alt='Analysis image' style='max-width:100%; margin-top:10px;'>";
                }
            }
            echo "</div>";
            echo "</div>";
        }
    } ?>
</div>

<div class="footer">
    <a href="help.php">Help</a>
    <a href="about.php">About</a>
    <a href="statement.php">Statement of Credits</a>
</div>

<script>
    const cards = document.querySelectorAll('.history-card');
    cards.forEach(card => {
        card.addEventListener('click', () => {
            const details = card.querySelector('.history-details');
            details.style.display = details.style.display === 'block' ? 'none' : 'block';
        });
    });
</script>
</body>
</html>


