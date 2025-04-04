<?php
// The function of search.php is to receive the information of animal groups and protein families submitted by users from index.php. If the user does not fill in any information, the default sample data set will be searched, and then the python script will be called for processing, and finally the result page will be jumped to display.
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taxGroup   = $_POST['taxonomic-group'] ?? '';
    $protFamily = $_POST['protein-family'] ?? '';

    
    if (trim($taxGroup) === '') {
        $taxGroup = 'aves';
    }
    if (trim($protFamily) === '') {
        $protFamily = 'glucose-6-phosphatase';
    }

    
    $command = 'python3 search_protein.py '
             . escapeshellarg($protFamily) . ' '
             . escapeshellarg($taxGroup);
    $safeCmd = escapeshellcmd($command);
    $output = shell_exec($safeCmd);
    $_SESSION['result'] = $output;

    
    
    $_SESSION['result'] = $output;
    header("Location: result.php?new_search=1");
    exit();
}
?>
