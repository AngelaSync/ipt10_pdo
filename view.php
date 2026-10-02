<?php
require_once 'config.php';

$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
// the id goes inside the execute() array, no bind_param() in PDO
$stmt->execute([$id]);
$row = $stmt->fetch();   // false when no row matched
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Details (PDO)</title>
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
    .uuid { font-size: .78rem; color: var(--brown); word-break: break-all; }
    .card { border: 1px solid var(--brown-light); border-radius: 16px; box-shadow: 0 6px 18px rgba(111,83,64,.08); }
    .btn { border-radius: 10px; font-weight: 500; }
    .btn-primary { --bs-btn-color:#fff; --bs-btn-bg:var(--matcha-dark); --bs-btn-border-color:var(--matcha-dark);
        --bs-btn-hover-color:#fff; --bs-btn-hover-bg:var(--matcha-deep); --bs-btn-hover-border-color:var(--matcha-deep);
        --bs-btn-active-color:#fff; --bs-btn-active-bg:var(--matcha-deep); --bs-btn-active-border-color:var(--matcha-deep); }
    .btn-secondary { --bs-btn-color:#fff; --bs-btn-bg:var(--brown); --bs-btn-border-color:var(--brown);
        --bs-btn-hover-color:#fff; --bs-btn-hover-bg:var(--brown-dark); --bs-btn-hover-border-color:var(--brown-dark);
        --bs-btn-active-color:#fff; --bs-btn-active-bg:var(--brown-dark); --bs-btn-active-border-color:var(--brown-dark); }
    .alert { border-radius: 12px; border: 1px solid transparent; }
    .alert-danger { background: #fdeaee; color: #a1384f; border-color: var(--pink-mid); }
    </style>
</head>
<body class="container py-4" style="max-width: 720px;">
<?php if (!$row): ?>
    <div class="alert alert-danger">Student not found.</div>
    <a href="index.php" class="btn btn-secondary">Back to list</a>
<?php else: ?>
    <?php
    $fullName = implode(' ', array_filter([
        $row['first_name'], $row['middle_name'], $row['last_name']
    ]));
    ?>
    <h2>Student Details <small class="text-muted fs-6">(PDO)</small></h2>
    <div class="card">
        <div class="card-body">
            <p><strong>Student ID:</strong> <span class="uuid"><?= htmlspecialchars($row['id']) ?></span></p>
            <p><strong>Full Name:</strong> <?= htmlspecialchars($fullName) ?></p>
            <p><strong>Birthday:</strong> <?= htmlspecialchars($row['birthday']) ?></p>
            <p><strong>Sex:</strong> <?= htmlspecialchars($row['sex']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
            <p><strong>Student Number:</strong> <?= htmlspecialchars($row['student_number']) ?></p>
            <p><strong>Program:</strong> <?= htmlspecialchars($row['program']) ?></p>
            <p><strong>Enrolment Date:</strong> <?= htmlspecialchars($row['enrolment_date']) ?></p>
            <p><strong>Created:</strong> <?= htmlspecialchars($row['created_at']) ?></p>
            <p class="mb-0"><strong>Last Updated:</strong> <?= htmlspecialchars($row['updated_at']) ?></p>
        </div>
    </div>
    <div class="mt-3">
        <a href="edit.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-primary">Edit</a>
        <a href="index.php" class="btn btn-secondary">Back to list</a>
    </div>
<?php endif; ?>
</body>
</html>