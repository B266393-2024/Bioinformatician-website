<?php
// This statement.php file is the acknowledgment and statement page of the protein sequence analysis platform, which is used to clarify the external resources referenced during the project development, the AI ??tools used, and the independent contributions of the author. The main content is composed of HTML, and the page uses the .content-panel container to display the content in the center, listing three major sections in a structured way: Code Sources, AI Tools Used, and Author Note.
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement of Credits</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .content-panel {
            background: rgba(255, 255, 255, 0.95);
            padding: 60px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 1100px;
            margin: 120px auto;
            line-height: 1.8;
        }
        .content-panel ol {
            padding-left: 0;
            list-style: none;
            counter-reset: section;
        }
        .content-panel ol > li {
            counter-increment: section;
            margin-bottom: 12px;
        }
        .content-panel ol > li::before {
            content: counter(section) ". ";
            font-weight: bold;
        }
        .content-panel h2 {
            margin-top: 23px; /* adjusted to match help.php section spacing */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <h1>Bioinformatician</h1>
        <div class="auth-links">
            <a href="index.php">Homepage</a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="content-panel">
        <h1>Statement of Credits</h1>

        <h2>Code Sources</h2>
        <p>Course materials from <a href="https://bioinfmsc8.bio.ed.ac.uk/IWD2.html" target="_blank">University of Edinburgh IWDD Course</a>.</p>
        <p>PHP PDO connection example adapted from <a href="https://www.php.net/manual/en/pdo.connections.php" target="_blank">PHP.net Manual</a>.</p>
        <p>PHP tutorial from <a href="https://www.w3schools.com/php/default.asp" target="_blank">W3Schools PHP Guide</a>.</p>
        <p>Clustal Omega usage guide from <a href="https://www.ebi.ac.uk/Tools/msa/clustalo/" target="_blank">EMBL-EBI Clustal Omega Tool</a>.</p>
        <p>Bootstrap CSS grid system reference from <a href="https://getbootstrap.com/docs/5.3/layout/grid/" target="_blank">Bootstrap 5 Documentation</a>.</p>
        <p>Patmatmotifs EMBOSS documentation consulted from <a href="http://emboss.sourceforge.net/apps/cvs/emboss/apps/patmatmotifs.html" target="_blank">EMBOSS Official Docs</a>.</p>
        <p>MySQL tutorial from <a href="https://dev.mysql.com/doc/refman/8.0/en/tutorial.html" target="_blank">MySQL Official Docs</a>.</p>
        <p>Website background image sourced from free images on <a href="https://pixabay.com/zh/" target="_blank">Pixabay</a>.</p>

        <h2>AI Tools Used</h2>
        <ol>
            <li>ChatGPT 4o (OpenAI, March 2025) was used for:</li>
            <li>Page layout optimization for result.php, alignment.php, motif.php, and structure.php.</li>
            <li>Debugging structure_image.py and alignment_image.py, ensuring correct output and plot generation.</li>
            <li>Assisting in learning and applying best practices for scripting languages, including typical use cases and differences among PHP, JavaScript, and Python.</li>
            <li>Debugging support through code walkthroughs, error message interpretation, and optimization suggestions for logical clarity and stability.</li>
        </ol>

        <h2>Author Note</h2>
        <ol>
            <li>I independently designed and implemented the MySQL database structure and handled all database management tasks.</li>
            <li>The entire website's design, navigation, and functional planning were completed by myself.</li>
            <li>This project showcases full-stack development skills, including backend PHP + SQL logic, Python integration, and front-end design.</li>
            <li>The general content of help, about, and statement pages was drafted by me.</li>
        </ol>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <a href="help.php">Help</a>
        <a href="about.php">About</a>
        <a href="statement.php">Statement of Credits</a>
    </footer>
</body>
</html>

