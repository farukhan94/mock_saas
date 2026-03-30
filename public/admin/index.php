<?php

require_once __DIR__ . '/../../config/bootstrap.php';

$tenants = $tenantRepo->getAll();

if (isset($_GET['delete'])) {
    $tenantRepo->delete((int) $_GET['delete']);
    header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SaaS Admin - Tenants</title>
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
            padding: 40px;
            margin: 0;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .btn {
            background: var(--accent);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger);
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        th {
            background: rgba(0, 0, 0, 0.2);
            color: var(--muted);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }

        .domain-link {
            color: var(--accent);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--muted);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Tenant Directory</h1>
            <a href="add.php" class="btn">+ Add Domain</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Custom Domain</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tenants)): ?>
                    <tr>
                        <td colspan="4" class="empty-state">No tenants found. Add your first custom domain!</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tenants as $tenant): ?>
                        <tr>
                            <td><strong>
                                    <?php echo htmlspecialchars($tenant['tenant_name']); ?>
                                </strong></td>
                            <td><a href="http://<?php echo htmlspecialchars($tenant['domain']); ?>" target="_blank"
                                    class="domain-link">
                                    <?php echo htmlspecialchars($tenant['domain']); ?>
                                </a></td>
                            <td>
                                <?php echo htmlspecialchars($tenant['created_at']); ?>
                            </td>
                            <td>
                                <a href="?delete=<?php echo $tenant['id']; ?>" class="btn btn-danger"
                                    onclick="return confirm('Remove this domain?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>