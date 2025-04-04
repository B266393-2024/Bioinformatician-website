<?php
// The main purpose of this about.php file is to introduce the functional architecture, technical details, file structure and development background of the entire project to developers. In addition, a link to return to the homepage is provided at the top, and a navigation bar is provided at the bottom to jump to the help, statement and other pages.
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Protein Sequence Analysis Platform</title>
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
        .content-panel ol li ol {
            margin-left: 20px;
            counter-reset: subitem;
        }
        .content-panel ol li ol li {
            counter-increment: subitem;
            margin-bottom: 5px;
        }
        .content-panel ol li ol li::before {
            content: "(" counter(subitem) ") ";
            font-weight: bold;
        }
        img.diagram {
            display: block;
            margin: 20px auto;
            max-width: 100%;
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
        <h1>About This Project</h1>

        <h2>Project Overview</h2>
        <p>This project is a "Protein Sequence Analysis Platform" designed for biologists and bioinformaticians. It provides integrated solutions for protein sequence retrieval, multiple sequence alignment (MSA), motif scanning, and secondary structure prediction through a web-based interface.</p>

        <h2>Technical Architecture</h2>
        <ol>
            <li><strong>Frontend:</strong> Built with PHP, CSS3, and vanilla JavaScript. Layout and styling are managed via a custom styles.css.</li>
            <li><strong>Backend:</strong> PHP handles user input, task scheduling, and interacts with MySQL using PDO for secure database operations.</li>
            <li><strong>Database:</strong> MySQL with tables including <code>users</code>, <code>analysis_history</code>, and <code>protein</code> to store user accounts, session analysis records, and the retrieved protein sequences for each query.</li>
            <li><strong>Bioinformatics Tools:</strong>
                <ol>
                    <li>NCBI Entrez (via BioPython/EDirect) for protein sequence retrieval.</li>
                    <li>Clustal Omega for MSA.</li>
                    <li>EMBOSS patmatmotifs for PROSITE motif scanning.</li>
                    <li>EMBOSS garnier for secondary structure prediction.</li>
                </ol>
            </li>
            <li><strong>Visualization:</strong> Results are visualized via Python scripts using Matplotlib or processed EMBOSS graphical outputs.</li>
        </ol>

        <h2>File Structure Overview</h2>
        <img src="filestructure.png" alt="File Structure Diagram" class="diagram">
        <ol>
            <li><strong>styles.css</strong>: Defines the overall visual design and layout of the entire website.</li>
            <li><strong>index.php</strong>: Main homepage that briefly introduces the platform functions. Users enter the taxonomic group and protein family here. It also serves as the login and logout entry point.</li>
            <li><strong>login.php</strong>: Login and registration page. The system intelligently detects whether a user exists. If not, registration is performed automatically. Passwords must meet complexity requirements: at least 8 characters including uppercase, lowercase, and numbers.</li>
            <li><strong>logout.php</strong>: Handles user logout and allows switching between accounts.</li>
            <li><strong>search.php</strong>: Processes user queries and initiates sequence retrieval.</li>
            <li><strong>search_protein.py</strong>: Uses NCBI Entrez (via BioPython/EDirect) to query and return protein sequence data from NCBI resources.</li>
            <li><strong>result.php</strong>: Displays the retrieved protein data with pagination. Users can select sequences for different analyses. Multiple selections enable alignment; single selections enable motif or structure analysis. This is controlled dynamically via JavaScript. If more than 30 sequences are selected, a modal warning is shown to prevent long runtime. The "select all" function applies only to the current page (10 sequences per page), ensuring flexible yet efficient selection.</li>
            <li><strong>alignment.php</strong>: Performs MSA using Clustal Omega. Both text alignment and visual conservation plots are generated.</li>
            <li><strong>alignment_image.py</strong>: Produces visualization of the MSA results using Python and Matplotlib.</li>
            <li><strong>motif.php</strong>: Scans selected sequences using EMBOSS patmatmotifs against the PROSITE database. Detected motifs and annotations are displayed clearly.</li>
            <li><strong>structure.php</strong>: Predicts secondary structure using EMBOSS garnier and presents the output visually.</li>
            <li><strong>structure_image.py</strong>: Uses graphical tools to display secondary structure features from prediction results.</li>
            <li><strong>history.php</strong>: Displays the analysis history within the current session. To prevent information overload, results are grouped in dropdowns. Each dropdown lists all results under that category. When collapsed, summary info like analysis type, timestamp, accession ID, and organism name is shown for easy access.</li>
            <li><strong>help.php</strong>: End-user manual.</li>
            <li><strong>about.php</strong>: Developer documentation (this page).</li>
            <li><strong>statement.php</strong>: Lists credits and AI tool usage.</li>
        </ol>

        <h2>Development Highlights</h2>
        <ol>
            <li>Guest ID system with automatic assignment on first visit for anonymous tracking.</li>
            <li>Secure database interaction using PDO with prepared statements to prevent SQL injection attacks.</li>
            <li>Integrated multiple bioinformatics tools including Clustal Omega, EMBOSS suite, and BioPython for end-to-end automation.</li>
            <li>Interactive user experience with JavaScript-based logic for sequence selection, dynamic button behavior, and smart alerts for long processing times.</li>
            <li>Modular design with each analysis module (search, alignment, motif, structure) built independently for maintainability and scalability.</li>
        </ol>

        <h2>Limitations</h2>
        <ol>
            <li>Inline and external CSS coexist, causing minor visual inconsistencies in some components.</li>
            <li>Session control is functional but may require further enhancement for edge cases.</li>
            <li>Feature scope remains limited; only core bioinformatics tasks are currently supported.</li>
            <li>Data stored in the database is not in JSON format, which limits flexibility for structured display and potential future expansions.</li>
        </ol>

        <h2>Future Improvements</h2>
        <ol>
            <li>Introduce a RESTful API layer to separate frontend and backend responsibilities.</li>
            <li>Refactor frontend using modern frameworks such as Vue.js or Bootstrap for responsive design and better UI components.</li>
            <li>Add new features like 3D structure prediction, phylogenetic tree building, and enhanced report exports.</li>
        </ol>

        <h2>Development Tools</h2>
        <ol>
            <li>Languages: PHP, Python, SQL, Bash</li>
            <li>Environment: bioinfmsc8 server</li>
            <li>Version Control: Git + GitHub</li>
            <li>Debug: Chrome DevTools, error logging, and manual test cases for functionality verification</li>
        </ol>
        
        <h2>Code Availability</h2>
        <p>The complete source code of this platform is publicly available at 
            <a href="https://github.com/B266393-2024/Bioinformatician-website.git" target="_blank">GitHub</a>.
        </p>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <a href="help.php">Help</a>
        <a href="about.php">About</a>
        <a href="statement.php">Statement of Credits</a>
    </footer>
</body>
</html>