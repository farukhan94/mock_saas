<?php

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../src/HostResolver.php';

use App\HostResolver;

$resolver = new HostResolver($_ENV['APP_BASE_DOMAIN']);
$currentHost = $resolver->getNormalizedHost();

// 1. Route to Admin if the host matches the base domain
if ($resolver->isAdminHost($currentHost)) {
    header("Location: /admin/index.php");
    exit;
}

// 2. Resolve the tenant from the database
$tenant = $tenantRepo->findByDomain($currentHost);

// 3. Dispatch to the tenant application if found
if ($tenant) {
    // Determine the current path
    $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

    // Redirect root to /home as requested
    if ($requestUri === '/' || $requestUri === '') {
        header("Location: /home");
        exit;
    }

    // Lead the tenant-specific application from the /app folder
    require_once __DIR__ . '/app/index.php';
    exit;
}

// 4. Fallback: 404 - Domain not registered
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Domain Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --text: #f8fafc;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --danger: #ef4444;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
            padding: 20px;
        }

        .error-container {
            background: var(--card);
            padding: 3rem;
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: var(--danger);
            margin-top: 0;
        }

        p {
            color: var(--muted);
            line-height: 1.6;
        }

        a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <h1>404 - Domain Not Found</h1>
        <p>The domain <strong><?php echo htmlspecialchars($currentHost); ?></strong> is not registered on our SaaS
            platform.</p>
        <p>If you are the owner, please add it via the <a
                href="http://<?php echo $_ENV['APP_BASE_DOMAIN']; ?>/admin/add.php">Admin Panel</a>.</p>
    </div>
</body>

</html>