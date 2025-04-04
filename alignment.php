<?php
/// The workflow of the PHP part is to obtain the accession (sequence ID) selected by the user from the form, query the actual content of these sequences (amino acid sequences) from the database, write the query results into a temporary FASTA file, call Clustal Omega for multiple sequence alignment, output .aln file, call Python script (alignment_image.py) to generate three images (text alignment map, heat map, conservation profile), record these image paths and alignment text into the database analysis_history table (if the user is logged in), and display the alignment text and three images on the page. Since the code part is long, I also wrote point-by-point steps on each small code segment.
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Alignment</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: rgba(249, 249, 249, 0.9); background-image: url('back.jpg'); background-size: cover; background-position: center center; background-attachment: fixed; display: flex; flex-direction: column; min-height: 100vh;">

  <!-- Top Header -->
  <div class="header">
    <h1>Alignment</h1>
    <div class="auth-links">
      <a href="history.php">History</a>
      <a href="index.php">Back to Search</a>
      <a href="result.php">Back Search Result</a>
    </div>
  </div>

  <!-- Main Content Container -->
  <div class="container" style="display: flex; justify-content: center; align-items: flex-start; flex: 1; margin:100px 70px 40px auto;width: 100%;">
    <div class="form-container" style="background-color: white; padding: 40px; border-radius: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 98%; max-width: 1600px; overflow-y: auto; margin: 0 auto;">

<?php
if (isset($_POST['selected_sequences']) && !empty($_POST['selected_sequences'])) {
    $selected_accessions = $_POST['selected_sequences'];

    $dsn = 'mysql:host=127.0.0.1;dbname=s2704757_my_first_db';
    $username = 's2704757';
    $password = 'Ziyiyang@2002!';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch sequences from DB
        $placeholders = str_repeat('?,', count($selected_accessions) - 1) . '?';
        $sql = "SELECT accession, sequence FROM proteins WHERE accession IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($selected_accessions);
        $proteins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build a FASTA file in /tmp
        $fastaData = [];
        foreach ($proteins as $protein) {
            $fastaData[] = ">" . $protein['accession'] . "\n" . $protein['sequence'];
        }
        $sequencesFile = '/tmp/sequences.fasta';
        file_put_contents($sequencesFile, implode("\n", $fastaData));

        // Run Clustal Omega 
        $alignmentFile = '/tmp/alignment_output.aln';
        $clustalCmd = "clustalo -i $sequencesFile -o $alignmentFile --force --outfmt=clu";
        shell_exec($clustalCmd);

        $resultText = '';
        if (file_exists($alignmentFile)) {
            $resultText = file_get_contents($alignmentFile);
            echo "<h3>Multiple Sequence Alignment Result (Clustal Format):</h3>";
            echo "<pre>" . htmlspecialchars($resultText) . "</pre>";
        } else {
            echo "<p>Alignment file not found. Please check logs.</p>";
        }
        $outputBase = '/localdisk/home/s2704757/public_html/ICA2/uploads/alignment_output';

        $accStr = implode('_', $selected_accessions);
        $hash = substr(sha1($accStr), 0, 10);
        
        //  Call alignment_image.py with hash string
        $pythonCmd = escapeshellcmd("python3 alignment_image.py \"$sequencesFile\" \"$outputBase\" \"$hash\"");
        shell_exec($pythonCmd);
        
        //  Use the shortened hash to construct image paths
        $textPng    = "uploads/alignment_output_{$hash}_text.png";
        $heatmapPng = "uploads/alignment_output_{$hash}_heatmap.png";
        $profilePng = "uploads/alignment_output_{$hash}_profile.png";
        
        
        echo "<h3>Alignment Text Image:</h3>";
        echo "<img src='$textPng' alt='Alignment Text Image' style='max-width:100%; display: block; margin:0 auto'><br>";
        
        echo "<h3>Conservation Heatmap:</h3>";
        echo "<img src='$heatmapPng' alt='Conservation Heatmap' style='max-width:100%; display: block; margin:0 auto'><br>";
        
        echo "<h3>Conservation Profile:</h3>";
        echo "<img src='$profilePng' alt='Conservation Profile' style='max-width:100%; display: block; margin:0 auto'>";

        

        // Insert into analysis_history
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("
                INSERT INTO analysis_history (user_id, analysis_type, result_text, image_path, created_at)
                VALUES (?, 'alignment', ?, ?, NOW())
            ");
            $imgJson = json_encode([$textPng, $heatmapPng, $profilePng]);
            $stmt->execute([
                $_SESSION['user_id'],
                $resultText,
                $imgJson
            ]);
        }

    } catch (PDOException $e) {
        echo "<p>Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>No sequences selected. Please go back and select sequences for alignment.</p>";
}
?>

    </div>
  </div>

  <div class="footer" style="text-align: center; background-color: #f1f1f1; padding: 10px 0; width: 100%;">
    <a href="help.php">Help</a>
    <a href="about.php">About</a>
    <a href="statement.php">Statement of Credits</a>
  </div>
</body>
</html>

