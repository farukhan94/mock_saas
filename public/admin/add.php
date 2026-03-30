<?php

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../src/HostResolver.php';
require_once __DIR__ . '/../../src/CloudflareSaasService.php';

use App\HostResolver;
use App\CloudflareSaasService;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $domain = trim($_POST['domain'] ?? '');

    if (empty($name) || empty($domain)) {
        $error = "Both fields are required.";
    } elseif (!HostResolver::isValidDomain($domain)) {
        $error = "Invalid domain format. Please use example.com";
    } elseif ($tenantRepo->findByDomain($domain)) {
        $error = "This domain is already registered.";
    } else {
        // Register in DB
        if ($tenantRepo->create($name, $domain)) {
            // Integrate with Cloudflare (Stub)
            $cf = new CloudflareSaasService(
                $_ENV['CLOUDFLARE_API_TOKEN'] ?? '',
                $_ENV['CLOUDFLARE_ACCOUNT_ID'] ?? '',
                $_ENV['CLOUDFLARE_ZONE_ID'] ?? ''
            );
            $cf->createCustomHostname($domain);

            $success = "Tenant registered successfully! Redirecting...";
            header("Refresh: 2; url=index.php");
        } else {
            $error = "Database error. Please try again.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Custom Domain</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --text: #f8fafc;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --danger: #ef4444;
            --success: #22c55e;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            padding: 40px;
        }

        .form-card {
            max-width: 500px;
            margin: 0 auto;
            background: var(--card);
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--muted);
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            box-sizing: border-box;
        }

        input:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
        }

        .btn {
            width: 100%;
            background: var(--accent);
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .error {
            color: var(--danger);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .success-box {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            border: 1px solid rgba(34, 197, 94, 0.2);
            margin: 2rem 0;
        }

        .success-box h2 {
            color: var(--success);
            margin-top: 0;
            font-size: 1.8rem;
        }

        .success-box p {
            color: var(--text);
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="form-card">
        <?php if ($success): ?>
            <div class="success-box">
                <h2>Done!</h2>
                <p><?php echo $success; ?></p>
            </div>
        <?php else: ?>
            <h2>Add Custom Domain</h2>
            <p style="color: var(--muted); margin-bottom: 2rem;">Register a new tenant domain mapping.</p>

            <?php if ($error): ?>
                <div class="error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Tenant Name</label>
                    <input type="text" name="name" placeholder="e.g. Acme Corp" required>
                </div>
                <div class="form-group">
                    <label>Custom Domain</label>
                    <input type="text" name="domain" placeholder="e.g. acme.com or tenant.acme.com" required>
                </div>
                <button type="submit" class="btn">Register Domain & Setup SSL (Stub)</button>
            </form>
        <?php endif; ?>

        <a href="index.php" class="back">&larr; Back to Dashboard</a>
    </div>
</body>

</html>