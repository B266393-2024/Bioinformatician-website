<?php
session_start();

if (isset($_POST['selected_sequences']) && !empty($_POST['selected_sequences'])) {
    $accession = htmlspecialchars($_POST['selected_sequences'][0]);
} else {
    echo "<p>No protein selected.</p>";
    exit();
}

$dsn = 'mysql:host=127.0.0.1;dbname=s2704757_my_first_db';
$username = 's2704757';
$password = 'Ziyiyang@2002!';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT accession, protein_name, organism, sequence FROM proteins WHERE accession = ?");
    $stmt->execute([$accession]);
    $protein = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$protein) {
        echo "<p>Protein not found in the database.</p>";
        exit();
    }
} catch (PDOException $e) {
    echo "<p>Database Error: " . $e->getMessage() . "</p>";
    exit();
}

$fasta_file = '/tmp/' . $protein['accession'] . '.fasta';
file_put_contents($fasta_file, ">{$protein['accession']}\n{$protein['sequence']}");

$output_file = '/tmp/' . $protein['accession'] . '_garnier.txt';
shell_exec("garnier -sequence $fasta_file -outfile $output_file");

$plot_file = '/localdisk/home/s2704757/public_html/ICA2/uploads/' . $protein['accession'] . '_structure.png';
shell_exec("python3 structure_image.py $output_file $plot_file");

// 读取结构预测的文本结果
$content = '';
if (file_exists($output_file)) {
    $content = file_get_contents($output_file);
}

// 插入数据库
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("INSERT INTO analysis_history (user_id, analysis_type, result_text, image_path, accession, organism, created_at) VALUES (?, 'structure', ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $_SESSION['user_id'],
        $content,
        "uploads/" . $protein['accession'] . "_structure.png",
        $protein['accession'],
        $protein['organism']
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secondary Structure Visualization</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .white-panel {
            background-color: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            margin: 120px auto;
            width: 80%;
            text-align: center;
        }
        pre {
            text-align: center;
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            overflow-x: auto;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Secondary Structure Prediction</h1>
    <div class="auth-links">
        <a href="history.php">History</a>
      <a href="index.php">Back to Search</a>
      <a href="result.php">Back Search Result</a>
    </div>
</div>

<div class="white-panel">
    <h3>Prediction for Accession: <?php echo htmlspecialchars($protein['accession']); ?></h3>
    <p><strong>Protein Name:</strong> <?php echo htmlspecialchars($protein['protein_name']); ?></p>
    <p><strong>Organism:</strong> <?php echo htmlspecialchars($protein['organism']); ?></p>
    <h4>Secondary Structure Map:</h4>
    <img src="uploads/<?php echo $protein['accession']; ?>_structure.png" alt="Secondary Structure Visualization" style="max-width:100%;">
    <h4>Formatted Structure Prediction:</h4>
    <?php
    if (!empty($content)) {
        echo '<pre>' . htmlspecialchars($content) . '</pre>';
    } else {
        echo "<p>Failed to generate secondary structure prediction.</p>";
    }
    ?>
</div>

<div class="footer">
    <a href="help.php">Help</a>
    <a href="about.php">About</a>
    <a href="statement.php">Statement of Credits</a>
</div>
</body>
</html>


