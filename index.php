<?php
require_once 'config.php';

$sql = 'SELECT id, first_name, last_name, email, enrolment_date
        FROM students
        ORDER BY enrolment_date DESC, id DESC';

// No user input here, so query() is the shorter, correct choice.
$rows = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Student Records </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
    :root {
        --matcha: #8fae7a; --matcha-dark: #7a9a68; --matcha-deep: #648553; --matcha-light: #eef4e6;
        --pink: #f8dce3; --pink-mid: #f1b8c6; --pink-deep: #d4849b;
        --brown: #b08968; --brown-dark: #6f5340; --brown-light: #e6d5c0; --cream: #fff9fa;
    }
    body { background: var(--cream); color: var(--brown-dark); font-family: "Segoe UI", system-ui, sans-serif; }
    h2 { color: var(--matcha-dark); font-weight: 700; padding-bottom: .5rem; margin-bottom: 1.25rem;
         border-bottom: 3px solid var(--pink-mid); display: inline-block; }
    a { color: var(--matcha-dark); }
    a:hover { color: var(--pink-deep); }
    .panel { background: #fff; border: 1px solid var(--brown-light); border-radius: 16px;
             box-shadow: 0 6px 18px rgba(111,83,64,.08); padding: 1.25rem; }
    .table { --bs-table-color: var(--brown-dark); --bs-table-bg: transparent;
             --bs-table-striped-bg: var(--matcha-light); --bs-table-striped-color: var(--brown-dark);
             --bs-table-hover-bg: var(--pink); --bs-table-hover-color: var(--brown-dark);
             --bs-table-border-color: var(--brown-light); margin-bottom: 0; }
    .table thead th { background: var(--matcha-dark); color: #fff; border: 0; font-weight: 600; }
    .table thead th:first-child { border-top-left-radius: 10px; }
    .table thead th:last-child { border-top-right-radius: 10px; }
    .uuid { font-size: .78rem; color: var(--brown); word-break: break-all; }
    .btn { border-radius: 10px; font-weight: 500; }
    .btn-primary { --bs-btn-color:#fff; --bs-btn-bg:var(--matcha-dark); --bs-btn-border-color:var(--matcha-dark);
        --bs-btn-hover-color:#fff; --bs-btn-hover-bg:var(--matcha-deep); --bs-btn-hover-border-color:var(--matcha-deep);
        --bs-btn-active-color:#fff; --bs-btn-active-bg:var(--matcha-deep); --bs-btn-active-border-color:var(--matcha-deep); }
    .btn-matcha { --bs-btn-color:var(--matcha-deep); --bs-btn-bg:var(--matcha-light); --bs-btn-border-color:var(--matcha);
        --bs-btn-hover-color:#fff; --bs-btn-hover-bg:var(--matcha-dark); --bs-btn-hover-border-color:var(--matcha-dark); }
    .btn-brown { --bs-btn-color:var(--brown-dark); --bs-btn-bg:#f5ebdd; --bs-btn-border-color:var(--brown-light);
        --bs-btn-hover-color:#fff; --bs-btn-hover-bg:var(--brown); --bs-btn-hover-border-color:var(--brown); }
    .btn-pink { --bs-btn-color:var(--pink-deep); --bs-btn-bg:var(--pink); --bs-btn-border-color:var(--pink-mid);
        --bs-btn-hover-color:#fff; --bs-btn-hover-bg:var(--pink-deep); --bs-btn-hover-border-color:var(--pink-deep); }
    </style>
</head>
<body class="container py-4">
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
    <h2 class="mb-0">All Student Records <small class="text-muted fs-6"></small></h2>
    <a href="create.php" class="btn btn-primary">+ Add Student</a>
</div>

<div class="panel mt-3 table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Enrolled</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td class="uuid"><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['enrolment_date']) ?></td>
                <td class="text-nowrap">
                    <a class="btn btn-sm btn-matcha" href="view.php?id=<?= htmlspecialchars($row['id']) ?>">View</a>
                    <a class="btn btn-sm btn-brown" href="edit.php?id=<?= htmlspecialchars($row['id']) ?>">Edit</a>
                    <a class="btn btn-sm btn-pink" href="delete.php?id=<?= htmlspecialchars($row['id']) ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>