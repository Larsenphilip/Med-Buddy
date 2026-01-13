<?php
session_start();

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../doctor_login.php");
    exit;
}

include '../db_config.php';
$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'];

// AI Model Integration Logic
$search_result = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['patient_id'])) {
    $patient_id = trim($_POST['patient_id']);
    
    // Execute the local Python AI script
    // Using absolute path since generic 'python' command wasn't found in PATH
    $python_path = "C:\\Users\\LARSEN\\AppData\\Local\\Programs\\Python\\Python312\\python.exe";
    $command = "\"$python_path\" ai_patient_summary.py " . escapeshellarg($patient_id) . " 2>&1";
    $output = shell_exec($command);
    
    if ($output) {
        // Basic Markdown to HTML conversion for rendering
        // 1. Escape HTML to prevent XSS
        $safe_output = htmlspecialchars($output);
        
        // 2. Convert **Bold** to <strong>Bold</strong>
        $formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong style="color: var(--text-dark);">$1</strong>', $safe_output);
        
        // 3. Convert *Italic* to <em>Italic</em>
        $formatted = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $formatted);
        
        // 4. Convert newlines to <br>
        $formatted = nl2br($formatted);
        
        $search_result = $formatted;
    } else {
        $search_result = "Error: No response from the AI system. Please ensure the Python script is executable and Ollama is running.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Patient - Med Buddy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0066CC;
            --secondary-color: #f4fdf0;
            --white: #ffffff;
            --text-dark: #1F2937;
            --border-color: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background-color: #f9fafb;
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--white);
            border-right: 1px solid var(--border-color);
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 3rem;
        }

        .menu-item {
            padding: 0.75rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            font-weight: 500;
            display: block;
        }

        .menu-item.active {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        .menu-item:hover {
            background-color: #f3f4f6;
        }

        .logout {
            margin-top: auto;
            color: red;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        h2 {
            margin-top: 0;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
        }

        /* Search Form Styles */
        .search-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .search-box {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-input:focus {
            border-color: var(--primary-color);
        }

        .btn-search {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-search:hover {
            background-color: #0052a3;
        }

        /* AI Summary Placeholder */
        .ai-result-area {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 3rem;
            text-align: center;
            color: #6b7280;
            background-color: #f9fafb;
        }

        .result-content {
            text-align: left;
            padding: 1rem;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            color: #0c4a6e;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">Med Buddy</div>
        <a href="index.php" class="menu-item">Dashboard</a>
        <a href="search_patient.php" class="menu-item active">Patient Search</a>
        <a href="logout.php" class="menu-item logout">Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Patient Intelligence System</h1>
            <div>Doctor: <strong><?php echo htmlspecialchars($doctor_name); ?></strong></div>
        </div>

        <div class="card">
            <div class="search-container">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <h2>Find Patient Records</h2>
                    <p style="color: #666;">Enter a Patient ID to generate an AI-powered summary of their medical history.</p>
                </div>

                <form method="POST" class="search-box">
                    <input type="text" name="patient_id" class="search-input" placeholder="Enter Patient ID (e.g., PAT-2023-001)" required>
                    <button type="submit" class="btn-search">Search & Analyze</button>
                </form>

                <?php if ($search_result): ?>
                    <div class="result-content">
                        <h3>Analysis Result</h3>
                        <p><?php echo $search_result; ?></p>
                    </div>
                <?php else: ?>
                    <div class="ai-result-area">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-bottom: 1rem; color: #9ca3af;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <p>Patient data and AI summary will appear here after search.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
