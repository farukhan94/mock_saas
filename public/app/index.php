<?php
/**
 * This file is included by the main public/index.php front controller
 * when a valid tenant is resolved.
 * 
 * Variables available:
 * @var array $tenant The resolved tenant data from the DB
 */

$pageTitle = "Welcome to " . htmlspecialchars($tenant['tenant_name']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $pageTitle; ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent: #38bdf8;
            --success: #22c55e;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .tenant-container {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 600px;
            width: 100%;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        h1 {
            margin: 1rem 0;
            font-weight: 600;
            letter-spacing: -0.025em;
        }

        p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: monospace;
            color: var(--accent);
        }

        .badge {
            background: rgba(56, 189, 248, 0.1);
            color: var(--accent);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .data-box {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: left;
        }

        .data-box h3 {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .data-box p {
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }

        .status-active {
            color: var(--success);
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class='tenant-container'>
        <span class='badge'>Tenant Site</span>
        <h1><?php echo htmlspecialchars($tenant['tenant_name']); ?></h1>
        <p class='domain-subtitle'>Serving content for: <code><?php echo htmlspecialchars($tenant['domain']); ?></code>
        </p>
        <div class='data-box'>
            <h3>Tenant Metadata</h3>
            <p><strong>Member Since:</strong> <?php echo htmlspecialchars($tenant['created_at']); ?></p>
            <p><strong>Status:</strong> <span class='status-active'>Active</span></p>
        </div>
    </div>
</body>

</html>