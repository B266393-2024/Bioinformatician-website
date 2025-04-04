<?php
// This help.php file is the user help page of the protein sequence analysis platform, which aims to provide users with operation instructions, function introduction and FAQ. The PHP part only contains session_start() to maintain the session state, and no other background logic. The HTML part builds a neat page structure, with the top navigation link returning to the homepage and the bottom navigation link jumping to the help, about and thank you pages.
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help - Protein Sequence Analysis Platform</title>
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
        .content-panel h2 {
            margin-top: 23px; 
        }
        .content-panel ul,
        .content-panel ol {
            list-style: none;
            padding-left: 0;
            margin-left: 0;
            list-style-position: inside;
        }
        .content-panel ul li::before {
            content: counter(item) ') ';
            counter-increment: item;
            font-weight: bold;
            margin-right: 5px;
        }
        .content-panel ul {
            counter-reset: item;
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
        <h1>Help / User Guide</h1>
        <p>Welcome to the Protein Sequence Analysis Platform! This platform is tailored for biologists and bioinformaticians to retrieve protein sequences from selected taxonomic groups, perform various analyses such as multiple sequence alignment, conservation analysis, motif prediction, and secondary structure prediction with data visualization.</p>

    <!-- This section mainly briefly introduces the platform functions and allows users to understand what analysis can be performed using the website. -->
        <h2>Platform Features</h2>
        <h3>1. Protein Sequence Retrieval</h3>
        <p>Users can define a taxonomic group (e.g., Aves, Mammalia, Rodentia) and a protein family (e.g., glucose-6-phosphatase, ABC transporters) to automatically retrieve corresponding protein sequences from the NCBI protein database. If users wish to explore more manually, can visit the <a href="https://www.ncbi.nlm.nih.gov/protein/" target="_blank">NCBI Protein Database</a>.</p>

        <h3>2. Conservation Analysis</h3>
        <p>The platform provides multiple sequence alignment (MSA) to generate conservation plots. This helps identify conserved and variable regions among species, allowing users to infer the biological functions or evolutionary significance of certain sequence segments.</p>

        <h3>3. Motif Scanning (PROSITE)</h3>
        <p>Protein sequences can be scanned against the PROSITE database to detect important motifs such as binding sites, domains, and active centers. The results page will provide a report that includes detailed motif site positions and their specific sequences.</p>

        <h3>4. Structural Information</h3>
        <p>Secondary structure prediction tools help visualize features such as alpha-helices, beta-sheets, and coils. These visual insights assist in understanding how a protein might fold and function.</p>

        <h3>5. Example Dataset</h3>
        <p>Users can explore a pre-loaded dataset consisting of glucose-6-phosphatase proteins retrieved from Aves (birds) as a working example.</p>

        <h3>6. History Tracking</h3>
        <p>All analysis activities are automatically saved for both guests and registered users and can be accessed from the History page during the current session. If a user is not logged in, the session is treated as guest mode. In this case, history will be lost upon session expiration or closing the browser. To retain results and enhance the experience, it is recommended to register by simply creating a username and password.</p>

    <!-- This is the specific process of using the website. -->
        <h2>How to Use</h2>
        <ol>
            <li><strong>Define Query:</strong> Enter the taxonomic group and protein family, then click "Start". Due to potential delays at NCBI, sequence retrieval might occasionally fail. If this happens, please try clicking "Start" a few more times.</li>
            <li><strong>Choose Analysis:</strong> Select alignment, motif scan, or structure prediction after retrieving sequences.</li>
            <li><strong>View & Download:</strong> Analysis results will be displayed on the results page for each function. Users can copy text results as needed, and images can be saved by right-clicking on them.</li>
            <li><strong>Review History:</strong> Use the "History" button to revisit previous analyses during your session.</li>
        </ol>

    <!-- This section contains some frequently asked questions, including the biological significance of the biological analysis functions in the website and some other questions, which can help people with a weak biological background understand the purpose and process of the website. -->
        <h2>FAQs</h2>
        <p><strong>Q:</strong> What taxonomic groups are supported?<br>
           <strong>A:</strong> Standard NCBI taxonomy such as Mammalia, Aves, Rodentia, Vertebrata, etc.
        </p>
        <p><strong>Q:</strong> What is MSA?<br>
           <strong>A:</strong> Multiple Sequence Alignment is used to align sequences to find conserved regions and evolutionary relationships.
        </p>
        <p><strong>Q:</strong> What is a motif?<br>
           <strong>A:</strong> A motif is a short conserved sequence region that often corresponds to a functional or structural domain.
        </p>
        <p><strong>Q:</strong> Do I need to register?<br>
           <strong>A:</strong> No, guest mode is supported with automatic session-based history tracking.
        </p>
        <p><strong>Q:</strong> How can I change the background image?<br>
           <strong>A:</strong> The background image is inspired by random Windows screensavers and the Edge search page's dynamic backgrounds. The developer believes that appreciating cute photos can add a touch of surprise in a busy, monotonous life. We are currently developing features to allow random background changes and manual background replacement.
        </p>

        <h2>Contact</h2>
        <p>If the page is prohibited or there is a problem with the image write permission, please contact the administrator to modify the permissions using "chmod 755 ${HOME}/public_html", "chmod 711 ${HOME}", "chmod 777 /localdisk/home/sxxxxxxx/public_html/ICA2/uploads/"</strong></p>
        <p>For technical or academic inquiries, please contact: <strong>sxxxxxxx@ed.ac.uk</strong></p>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <a href="help.php">Help</a>
        <a href="about.php">About</a>
        <a href="statement.php">Statement of Credits</a>
    </footer>
</body>
</html>


