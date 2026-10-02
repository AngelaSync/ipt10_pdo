<?php
require_once 'config.php';

function e($value) {
    return htmlspecialchars((string)$value);
}

/*
 * Step 1: Get the student ID from the URL
 */
$id = trim($_GET['id'] ?? '');

if ($id === '') {
    die('Invalid student ID');
}

/*
 * Step 2: Find the student first
 */
$stmt = $pdo->prepare(
    'SELECT first_name, last_name
     FROM students
     WHERE id = ?'
);

$stmt->execute([$id]);

$student = $stmt->fetch();

/*
 * Handle student not found
 */
if (!$student) {
    die('Student not found.');
}

$success = '';
$error = '';

/*
 * Step 3: Delete only after POST confirmation
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $d = $pdo->prepare(
            'DELETE FROM students
             WHERE id = ?'
        );

        $d->execute([$id]);

        /*
         * rowCount() tells us how many rows
         * were affected by the DELETE.
         */
        if ($d->rowCount() === 1) {
            $success = 'Student deleted successfully!';
        } else {
            $error = 'Student could not be deleted.';
        }

    } catch (PDOException $ex) {

        error_log('Delete failed: ' . $ex->getMessage());
        $error = 'Could not delete the student.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Delete Student</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    >

    <style>
    :root {
        --matcha: #8fae7a;
        --matcha-dark: #7a9a68;
        --matcha-deep: #648553;
        --matcha-light: #eef4e6;

        --pink: #f8dce3;
        --pink-mid: #f1b8c6;
        --pink-deep: #d4849b;

        --brown: #b08968;
        --brown-dark: #6f5340;
        --brown-light: #e6d5c0;

        --cream: #fff9fa;
    }

    body {
        background: var(--cream);
        color: var(--brown-dark);
        font-family: "Segoe UI", system-ui, sans-serif;
    }

    h2 {
        color: var(--matcha-dark);
        font-weight: 700;
        padding-bottom: .5rem;
        margin-bottom: 1.25rem;
        border-bottom: 3px solid var(--pink-mid);
        display: inline-block;
    }

    .panel {
        background: #fff;
        border: 1px solid var(--brown-light);
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(111,83,64,.08);
        padding: 1.25rem;
    }

    .btn {
        border-radius: 10px;
        font-weight: 500;
    }

    .btn-danger {
        --bs-btn-color: #fff;
        --bs-btn-bg: #c96f7f;
        --bs-btn-border-color: #c96f7f;

        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #a95768;
        --bs-btn-hover-border-color: #a95768;
    }

    .btn-secondary {
        --bs-btn-color: #fff;
        --bs-btn-bg: var(--brown);
        --bs-btn-border-color: var(--brown);

        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: var(--brown-dark);
        --bs-btn-hover-border-color: var(--brown-dark);
    }

    .alert {
        border-radius: 12px;
    }

    .alert-success {
        background: var(--matcha-light);
        color: var(--matcha-deep);
        border-color: var(--matcha);
    }

    .alert-danger {
        background: #fdeaee;
        color: #a1384f;
        border-color: var(--pink-mid);
    }
    </style>
</head>

<body class="container py-4" style="max-width: 680px;">

<h2>Delete Student</h2>

<div class="panel">

    <?php if ($success): ?>

        <div class="alert alert-success">
            <?= e($success) ?>
        </div>

        <a
            href="index.php"
            class="btn btn-secondary"
        >
            Back to list
        </a>

    <?php elseif ($error): ?>

        <div class="alert alert-danger">
            <?= e($error) ?>
        </div>

        <a
            href="index.php"
            class="btn btn-secondary"
        >
            Back to list
        </a>

    <?php else: ?>

        <p>
            Are you sure you want to delete
            <strong>
                <?= e($student['first_name'] . ' ' . $student['last_name']) ?>
            </strong>
           ?
        </p>

        <form method="POST">

            <button
                type="submit"
                class="btn btn-danger"
            >
                Yes, Delete
            </button>

            <a
                href="index.php"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </form>

    <?php endif; ?>

</div>

</body>
</html>