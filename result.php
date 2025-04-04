<?php
// The PHP part implements obtaining protein search results from the Session. 
// If it is the first search and there is no relevant data in the database, 
// the obtained data will be inserted into the database for storage. 
// In this way, in the subsequent multiple alignment, motif, and structure parts, 
// the sequence information can be obtained from the database through accession for subsequent analysis.
session_start();
if (!isset($_SESSION['result'])) {
    echo "No data found. Please try again.";
    exit();
}
$results = json_decode($_SESSION['result'], true);
if (!is_array($results)) {
    echo "<h2 style='color:red;'>?? JSON 解析失败，无法插入数据库。</h2>";
    echo "<pre>" . htmlspecialchars($_SESSION['result']) . "</pre>";
    exit();
}

$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$totalItems = count($results);
$totalPages = ceil($totalItems / $itemsPerPage);
if ($page > $totalPages) {
    $page = $totalPages;
}
$startIndex = ($page - 1) * $itemsPerPage;
$pagedResults = array_slice($results, $startIndex, $itemsPerPage);

$dsn = 'mysql:host=127.0.0.1;dbname=s2704757_my_first_db';
$username = 's2704757';
$password = 'Ziyiyang@2002!';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("INSERT IGNORE INTO proteins (accession, protein_name, organism, sequence_length, sequence)
                           VALUES (:accession, :protein_name, :organism, :sequence_length, :sequence)");
    
    $insertedCount = 0;
    foreach ($results as $protein) {
        if (!isset($protein['accession'], $protein['protein_name'], $protein['organism'], $protein['sequence_length'], $protein['sequence'])) {
            echo "记录格式错误：";
            print_r($protein);
            continue;
        }
        
        $stmt->bindValue(':accession', $protein['accession']);
        $stmt->bindValue(':protein_name', $protein['protein_name']);
        $stmt->bindValue(':organism', $protein['organism']);
        $stmt->bindValue(':sequence_length', (int)$protein['sequence_length']);
        $stmt->bindValue(':sequence', $protein['sequence']);
        
        $stmt->execute();
        $errorInfo = $stmt->errorInfo();
        if ($errorInfo[0] !== "00000") {
            echo "SQL error：" . implode(" | ", $errorInfo) . "<br>";
        } else {
            $insertedCount += $stmt->rowCount();
        }
    }
    echo "success：$insertedCount";
    
    $_SESSION['db_inserted'] = true;
} catch (PDOException $e) {
    echo "database error：" . $e->getMessage();
    exit();
}
if (isset($_GET['new_search']) && $_GET['new_search'] == 1) {
    echo "<script>localStorage.removeItem('selectedSequences');</script>";
}
?>

<!--  This code is a webpage template for displaying protein search results. It combines PHP to dynamically generate query result tables, supports users to select sequences through checkboxes and perform multiple sequence alignment, motif analysis, structure prediction, etc. The page contains paging logic, pop-up reminder mechanism, button activation control, and basic style design, making the interface clear and beautiful, interactive and friendly. Navigation links such as Help, About, and Acknowledgements are also provided at the bottom to enhance the user experience. -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protein Search Results</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; 
        }
        th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: center;
            word-wrap: break-word;
        }
        td {
            padding: 8px;
            text-align: center;
            word-wrap: break-word; 
            white-space: pre-wrap; 
            max-width: 300px; 
            overflow-wrap: break-word;
            overflow-x: auto; 
        }
        th:nth-child(1), td:nth-child(1) {
            width: 5%;
        }
        th:nth-child(2), td:nth-child(1) {
            width: 10%;
        }
        th:nth-child(3), td:nth-child(2) {
            width: 15%;
        }
        th:nth-child(4), td:nth-child(3) {
            width: 10%;
        }
        th:nth-child(5), td:nth-child(4) {
            width: 7%;
        }
        th:nth-child(6), td:nth-child(4) {
            width: 38%;
        }
        .main-content {
            margin: 100px 20px 120px 20px;
            background-color: white;
            padding: 40px;
            padding-bottom: 80px; 
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            
            max-width: 100%;
            
            position: relative;
        }
        .analysis-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        .analysis-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: not-allowed;
            background-color: #ccc;
            color: #fff;
            font-size: 14px;
        }
        .analysis-buttons button.enabled {
            cursor: pointer;
            background-color: #3b3c50; 
        }
        .analysis-buttons button.enabled:hover {
            background-color: #2e2f3b;
        }
        .error {
            color: red;
        }
        
        #confirmationModal {
            display: none; 
            position: fixed; 
            z-index: 2000; 
            left: 0; top: 0; 
            width: 100%; height: 100%; 
            background-color: rgba(0,0,0,0.5);
        }
        #confirmationModal > div {
            background-color: #fff; 
            border-radius: 10px; 
            padding: 20px; 
            max-width: 500px; 
            margin: 150px auto; 
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Protein Sequence</h1>
        <div class="auth-links">
            <a href="history.php">History</a>
            <a href="index.php">Back to Search</a>
        </div>
    </div>

<!-- white panle -->
  <div class="main-content">
    <h2 style="margin-top:6px; padding-top:2px;">Search Results</h2>

    <!-- form -->
    <form id="protein-form" method="POST">
      <!-- Analysis Options -->
      <div class="analysis-buttons">
        <button id="multiple-alignment" type="submit" disabled>Multiple alignment</button>
        <button id="motif" type="submit" disabled>Motif</button>
        <button id="structure" type="submit" disabled>Structure</button>
      </div> 
    
            <?php
            if (isset($results['error'])) {
                echo "<p class='error'>Error: " . htmlspecialchars($results['error']) . "</p>";
            } else {
                echo '<table border="1">
                        <thead>
                            <tr>
                                <th>
                                    Select <input type="checkbox" id="select-all"> 
                                </th>
                                <th>Accession</th>
                                <th>Protein</th>
                                <th>Organism</th>
                                <th>Sequence Length</th>
                                <th>Sequence</th>
                            </tr>
                        </thead>
                        <tbody>';
                foreach ($pagedResults as $protein) {
                    echo '<tr>
                            <td>
                                <input type="checkbox" name="selected_sequences[]" value="' . htmlspecialchars($protein['accession']) . '" class="sequence-checkbox">
                            </td>
                            <td>' . htmlspecialchars($protein['accession']) . '</td>
                            <td>' . htmlspecialchars($protein['protein_name']) . '</td>
                            <td>' . htmlspecialchars($protein['organism']) . '</td>
                            <td>' . htmlspecialchars($protein['sequence_length']) . '</td>
                            <td>' . nl2br(htmlspecialchars($protein['sequence'])) . '</td>
                        </tr>';
                }
                echo '</tbody>
                    </table>';
            }
            if ($totalPages > 1) {
                echo '<div class="pagination" style="margin-top: 20px; text-align: center;">';
                if ($page > 1) {
                    echo '<a href="?page=' . ($page - 1) . '">Previous</a> ';
                }
                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                        echo '<span class="current-page" style="padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px; background-color: #999; color: #fff; margin: 0 5px;">' . $i . '</span>';
                    } else {
                        echo '<a href="?page=' . $i . '" style="padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px; margin: 0 5px; text-decoration: none;">' . $i . '</a>';
                    }
                }
                if ($page < $totalPages) {
                    echo ' <a href="?page=' . ($page + 1) . '">Next</a>';
                }
                echo '</div>';
            }
            ?>
        </form>

    <div id="confirmationModal">
      <div class="modal-content">
        <p>If the number of sequences you selected is greater than or equal to 30, the analysis may take too long and the waiting time may be too long. If you want to perform a faster analysis, please reduce the number of sequences.</p>
        <button id="proceedAlignment" class="modal-button">Process</button>
        <button id="cancelAlignment" class="modal-button">Reselect</button>
      </div>
    </div>
  </div> 

  <!-- footer -->
  <div class="footer">
    <a href="help.php">Help</a>
    <a href="about.php">About</a>
    <a href="statement.php">Statement of Credits</a>
  </div>
  
  
    <!-- This JavaScript script is used to manage the sequence selection logic in the protein search results page, support automatic recovery of user selections, control the enabled state of the analysis button, and implement the "select all" function. When the analysis button is clicked, the selected sequence will be submitted to the corresponding page as a hidden field; if more than 30 items are selected, a prompt pop-up window will pop up. This script enhances the interactivity and user experience of the page. -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('protein-form');
    const checkboxes = document.querySelectorAll('.sequence-checkbox');
    const multipleAlignmentBtn = document.getElementById('multiple-alignment');
    const motifBtn = document.getElementById('motif');
    const structureBtn = document.getElementById('structure');
    const selectAllCheckbox = document.getElementById('select-all');
    
    let savedSelections = JSON.parse(localStorage.getItem('selectedSequences')) || [];
    checkboxes.forEach(function(checkbox) {
        if (savedSelections.indexOf(checkbox.value) !== -1) {
            checkbox.checked = true;
        }
    });

    function updateButtonStates() {
        let saved = JSON.parse(localStorage.getItem('selectedSequences')) || [];
        let count = saved.length;  

        if (count > 1) {
            multipleAlignmentBtn.disabled = false;
            multipleAlignmentBtn.classList.add('enabled');
        } else {
            multipleAlignmentBtn.disabled = true;
            multipleAlignmentBtn.classList.remove('enabled');
        }

        if (count === 1) {
            motifBtn.disabled = false;
            structureBtn.disabled = false;
            motifBtn.classList.add('enabled');
            structureBtn.classList.add('enabled');
        } else {
            motifBtn.disabled = true;
            structureBtn.disabled = true;
            motifBtn.classList.remove('enabled');
            structureBtn.classList.remove('enabled');
        }

        
        selectAllCheckbox.checked = checkboxes.length > 0 && [...checkboxes].every(cb => cb.checked);
    }

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            let saved = JSON.parse(localStorage.getItem('selectedSequences')) || [];
            if (checkbox.checked) {
                if (saved.indexOf(checkbox.value) === -1) {
                    saved.push(checkbox.value);
                }
            } else {
                saved = saved.filter(function(val) {
                    return val !== checkbox.value;
                });
            }
            localStorage.setItem('selectedSequences', JSON.stringify(saved));
            updateButtonStates();
        });
    });

    selectAllCheckbox.addEventListener('change', function() {
        let isChecked = this.checked;
        let saved = JSON.parse(localStorage.getItem('selectedSequences')) || [];
        checkboxes.forEach(function(cb) {
            cb.checked = isChecked;
            if (isChecked) {
                if (!saved.includes(cb.value)) {
                    saved.push(cb.value);
                }
            } else {
                saved = saved.filter(v => v !== cb.value);
            }
        });
        localStorage.setItem('selectedSequences', JSON.stringify(saved));
        updateButtonStates();
    });

    function syncLocalStorageToForm() {
        document.querySelectorAll('input[type="hidden"][name="selected_sequences[]"]').forEach(el => el.remove());
        
        let saved = JSON.parse(localStorage.getItem('selectedSequences')) || [];
        saved.forEach(function(acc) {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_sequences[]';
            input.value = acc;
            form.appendChild(input);
        });
    }

    multipleAlignmentBtn.addEventListener('click', function(e) {
        e.preventDefault();
        let saved = JSON.parse(localStorage.getItem('selectedSequences')) || [];
        if (saved.length >= 30) {
            document.getElementById('confirmationModal').style.display = 'block';
        } else {
            syncLocalStorageToForm();
            form.action = 'alignment.php';
            form.submit();
        }
    });

    document.getElementById('proceedAlignment').addEventListener('click', function() {
        document.getElementById('confirmationModal').style.display = 'none';
        syncLocalStorageToForm();
        form.action = 'alignment.php';
        form.submit();
    });

    document.getElementById('cancelAlignment').addEventListener('click', function() {
        document.getElementById('confirmationModal').style.display = 'none';
    });

    motifBtn.addEventListener('click', function(e) {
        syncLocalStorageToForm();
        form.action = 'motif.php';
        form.submit();
    });
    structureBtn.addEventListener('click', function(e) {
        syncLocalStorageToForm();
        form.action = 'structure.php';
        form.submit();
    });

    updateButtonStates();
});
</script>


</body>
</html>

