<?php
// The main function of the motif page is to receive a protein sequence selected by the user, call the patmatmotifs tool to find the conserved motif information matching the PROSITE database, and display the results in a formatted manner on the web page. At the same time, the analysis history is inserted into the database for the user to view later in the history.
session_start();
?>

<!-- In the HTML part, style is used to briefly define the style of the web page. -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Motif Analysis</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .header {
      position: fixed;
      top: 0; left: 0; right: 0;
      height: 80px; 
      background-color: #3b3c50;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: space-between;
      color: #fff;
      padding: 0 20px;
    }
    .header h1 {
      font-size: 24px;
    }
    .auth-links a {
      color: white;
      margin-left: 15px;
      text-decoration: none;
    }

    .container {
      display: flex;
      justify-content: center;   
      align-items: center;       
      min-height: calc(100vh - 80px);
      margin-top: 80px;
      padding: 10px;
    }

    
    .white-panel {
      background-color: rgba(255,255,255,0.95);
      padding: 20px;
      border-radius: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      width: 92%;
      max-width: 1600px;
      min-height: 70vh;
      margin: 0 auto;
      text-align: center;
    }

    .white-panel h3 {
      text-align: center;
    }

    .white-panel pre {
      text-align: left;
      display: inline-block;
      margin: auto;
    }

  </style>
</head>
<body>
  <div class="header">
    <h1>Motif Analysis</h1>
    <div class="auth-links">
      <a href="history.php">History</a>
      <a href="index.php">Back to Search</a>
      <a href="result.php">Back Search Result</a>
    </div>
  </div>

  <div class="container" >
    <div class="white-panel">
      <?php
      /// insert database
      if (isset($_POST['selected_sequences']) && !empty($_POST['selected_sequences'])) {
          $selected_accessions = $_POST['selected_sequences'];

          $dsn = 'mysql:host=127.0.0.1;dbname=s2704757_my_first_db';
          $username = 's2704757';
          $password = 'Ziyiyang@2002!';

          try {
              $pdo = new PDO($dsn, $username, $password);
              $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              $placeholders = str_repeat('?,', count($selected_accessions) - 1) . '?';
              $sql = "SELECT accession, sequence, organism FROM proteins WHERE accession IN ($placeholders)";
              $stmt = $pdo->prepare($sql);
              $stmt->execute($selected_accessions);
              $proteins = $stmt->fetchAll(PDO::FETCH_ASSOC);

              
              $protein = $proteins[0];
              $sequencesFile = '/tmp/' . $protein['accession'] . '.fasta';
              $fastaData = ">" . $protein['accession'] . "\n" . $protein['sequence'];
              file_put_contents($sequencesFile, $fastaData);

              /// show results
              echo "<h3>Accession: " . htmlspecialchars($protein['accession']) . "</h3>";
              echo "<h3>Organism: " . htmlspecialchars($protein['organism']) . "</h3>";

              $output_dir = '/tmp/motif_output/';
              if (!file_exists($output_dir)) {
                  mkdir($output_dir, 0777, true);
              }
              $output_file = $output_dir . "motif_" . $protein['accession'] . ".txt";
              $cmd = "patmatmotifs -sequence $sequencesFile -outfile $output_file";
              exec($cmd);

              echo "<h3>Motif Analysis Results:</h3>";
              if (file_exists($output_file) && filesize($output_file) > 100) {
                  $resultText = file_get_contents($output_file);
                  echo "<pre>" . htmlspecialchars($resultText) . "</pre>";
              } else {
                  echo "<p style='color: red; font-weight: bold;'>No motifs found for this sequence based on PROSITE database.</p>";
                  $resultText = "No motifs found.";
              }

              
              if (isset($_SESSION['user_id'])) {
                  $stmt = $pdo->prepare("INSERT INTO analysis_history (user_id, analysis_type, result_text, image_path, created_at, accession, organism) VALUES (?, 'motif', ?, '', NOW(),?,?)");
                  $stmt->execute([
                      $_SESSION['user_id'],
                      $resultText,
                      $protein['accession'],
                      $protein['organism']
                  ]);
              }

          } catch (PDOException $e) {
              echo "<p class='error'>Database Error: " . $e->getMessage() . "</p>";
          }
      } else {
          echo "<p>No sequences selected. Please go back and select sequences for motif analysis.</p>";
      }
      ?>
    </div>
  </div>

  <div class="footer">
    <a href="help.php">Help</a>
    <a href="about.php">About</a>
    <a href="statement.php">Statement of Credits</a>
  </div>
</body>
</html>
