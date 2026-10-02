
<?php
require_once 'config.php';

function e($value) {
    return htmlspecialchars((string)$value);
}

function valid_date($d) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)
        && checkdate(
            (int)substr($d, 5, 2),
            (int)substr($d, 8, 2),
            (int)substr($d, 0, 4)
        );
}

$id = trim($_GET['id'] ?? '');

if ($id === '') {
    die('Invalid student ID');
}

$fields = [
    'first_name',
    'middle_name',
    'last_name',
    'birthday',
    'sex',
    'email',
    'student_number',
    'program',
    'enrolment_date'
];

/*
 * Get the existing student.
 * PDO uses execute([$id]) instead of bind_param().
 */
$s = $pdo->prepare(
    'SELECT first_name, middle_name, last_name, birthday, sex, email,
            student_number, program, enrolment_date
     FROM students
     WHERE id = ?'
);

$s->execute([$id]);

$existing = $s->fetch();

$notFound = !$existing;

$v = [];

foreach ($fields as $f) {
    $v[$f] = (string)($existing[$f] ?? '');
}

$errors = [];
$success = '';
$notice = '';

if (!$notFound && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect submitted values
    foreach ($fields as $f) {
        $v[$f] = trim($_POST[$f] ?? '');
    }

    // Validate first name
    if (
        $v['first_name'] === '' ||
        strlen($v['first_name']) < 2 ||
        strlen($v['first_name']) > 100 ||
        !preg_match('/^[A-Za-z\s]+$/', $v['first_name'])
    ) {
        $errors['first_name'] =
            'First name is required: 2-100 letters and spaces only.';
    }

    // Validate last name
    if (
        $v['last_name'] === '' ||
        strlen($v['last_name']) < 2 ||
        strlen($v['last_name']) > 100 ||
        !preg_match('/^[A-Za-z\s]+$/', $v['last_name'])
    ) {
        $errors['last_name'] =
            'Last name is required: 2-100 letters and spaces only.';
    }

    // Validate middle name
    if (
        $v['middle_name'] !== '' &&
        (
            strlen($v['middle_name']) > 100 ||
            !preg_match('/^[A-Za-z\s]+$/', $v['middle_name'])
        )
    ) {
        $errors['middle_name'] =
            'Middle name: letters and spaces only, max 100.';
    }

    // Validate email
    if (
        $v['email'] === '' ||
        !filter_var($v['email'], FILTER_VALIDATE_EMAIL)
    ) {
        $errors['email'] =
            'A valid email address is required.';
    }

    // Validate birthday
    if (!valid_date($v['birthday'])) {
        $errors['birthday'] =
            'Birthday is required in YYYY-MM-DD format.';
    }

    // Validate sex
    if (!in_array($v['sex'], ['Male', 'Female'], true)) {
        $errors['sex'] =
            'Select Male or Female.';
    }

    // Validate student number
    if (
        $v['student_number'] !== '' &&
        (
            strlen($v['student_number']) > 50 ||
            !preg_match('/^[A-Za-z0-9]+$/', $v['student_number'])
        )
    ) {
        $errors['student_number'] =
            'Student number: letters and digits only, max 50.';
    }

    // Validate program
    if (strlen($v['program']) > 200) {
        $errors['program'] =
            'Program must be 200 characters or fewer.';
    }

    // Validate enrolment date
    if (!valid_date($v['enrolment_date'])) {
        $errors['enrolment_date'] =
            'Enrolment date is required in YYYY-MM-DD format.';
    }

    /*
     * Only update when validation succeeds.
     */
    if (empty($errors)) {

        try {

            $sql = 'UPDATE students
                    SET first_name = ?,
                        middle_name = ?,
                        last_name = ?,
                        birthday = ?,
                        sex = ?,
                        email = ?,
                        student_number = ?,
                        program = ?,
                        enrolment_date = ?
                    WHERE id = ?';

            $stmt = $pdo->prepare($sql);

            /*
             * IMPORTANT:
             * The order here must match the ? placeholders.
             *
             * The final ? is WHERE id = ?
             * Therefore $id MUST be last.
             */
            $stmt->execute([
                $v['first_name'],
                $v['middle_name'],
                $v['last_name'],
                $v['birthday'],
                $v['sex'],
                $v['email'],
                $v['student_number'],
                $v['program'],
                $v['enrolment_date'],
                $id
            ]);

            /*
             * rowCount() tells us how many rows were affected.
             *
             * 1 = a row was changed
             * 0 = no values changed, even though the UPDATE
             *     may have executed successfully
             */
            if ($stmt->rowCount() === 1) {
                $success = 'Student updated successfully!';
            } else {
                $notice =
                    'No changes were made: the values are identical to what is already saved.';
            }

        } catch (PDOException $ex) {

            /*
             * SQLSTATE 23000 is commonly used for
             * integrity constraint violations such as
             * duplicate email/student number.
             */
            if ($ex->getCode() === '23000') {
                $errors['email'] =
                    'Email or student number already exists.';
            } else {
                error_log('Update failed: ' . $ex->getMessage());
                $errors['form'] =
                    'Could not update the student.';
            }
        }
    }
}

$labels = [
    'first_name' => 'First Name',
    'middle_name' => 'Middle Name',
    'last_name' => 'Last Name',
    'birthday' => 'Birthday',
    'email' => 'Email',
    'student_number' => 'Student Number',
    'program' => 'Program',
    'enrolment_date' => 'Enrolment Date',
];

$types = [
    'birthday' => 'date',
    'enrolment_date' => 'date',
    'email' => 'email'
];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Edit Student</title>

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

    a {
        color: var(--matcha-dark);
    }

    a:hover {
        color: var(--pink-deep);
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

    .btn-primary {
        --bs-btn-color:#fff;
        --bs-btn-bg:var(--matcha-dark);
        --bs-btn-border-color:var(--matcha-dark);

        --bs-btn-hover-color:#fff;
        --bs-btn-hover-bg:var(--matcha-deep);
        --bs-btn-hover-border-color:var(--matcha-deep);

        --bs-btn-active-color:#fff;
        --bs-btn-active-bg:var(--matcha-deep);
        --bs-btn-active-border-color:var(--matcha-deep);
    }

    .btn-outline-primary {
        --bs-btn-color:var(--matcha-dark);
        --bs-btn-border-color:var(--matcha);

        --bs-btn-hover-color:#fff;
        --bs-btn-hover-bg:var(--matcha-dark);
        --bs-btn-hover-border-color:var(--matcha-dark);

        --bs-btn-active-color:#fff;
        --bs-btn-active-bg:var(--matcha-deep);
        --bs-btn-active-border-color:var(--matcha-deep);
    }

    .btn-secondary {
        --bs-btn-color:#fff;
        --bs-btn-bg:var(--brown);
        --bs-btn-border-color:var(--brown);

        --bs-btn-hover-color:#fff;
        --bs-btn-hover-bg:var(--brown-dark);
        --bs-btn-hover-border-color:var(--brown-dark);

        --bs-btn-active-color:#fff;
        --bs-btn-active-bg:var(--brown-dark);
        --bs-btn-active-border-color:var(--brown-dark);
    }

    .form-label {
        color: var(--brown-dark);
        font-weight: 500;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--brown-light);
        border-radius: 10px;
        background-color: #fffefb;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--matcha);
        box-shadow: 0 0 0 .25rem rgba(143,174,122,.28);
    }

    .alert {
        border-radius: 12px;
        border: 1px solid transparent;
    }

    .alert-success {
        background: var(--matcha-light);
        color: var(--matcha-deep);
        border-color: var(--matcha);
    }

    .alert-info {
        background: #f5ebdd;
        color: var(--brown-dark);
        border-color: var(--brown-light);
    }

    .alert-danger {
        background: #fdeaee;
        color: #a1384f;
        border-color: var(--pink-mid);
    }

    </style>

</head>

<body
    class="container py-4"
    style="max-width: 680px;"
>

<h2>Edit Student</h2>

<?php if ($notFound): ?>

    <div class="alert alert-danger">
        Student not found.
    </div>

    <a
        href="index.php"
        class="btn btn-secondary"
    >
        Back to list
    </a>

<?php else: ?>

    <?php if ($success): ?>

        <div class="alert alert-success">
            <?= e($success) ?>
        </div>

    <?php endif; ?>

    <?php if ($notice): ?>

        <div class="alert alert-info">
            <?= e($notice) ?>
        </div>

    <?php endif; ?>

    <?php if (isset($errors['form'])): ?>

        <div class="alert alert-danger">
            <?= e($errors['form']) ?>
        </div>

    <?php endif; ?>

    <div class="panel">

        <form method="POST" novalidate>

            <?php foreach ($labels as $name => $label): ?>

                <div class="mb-3">

                    <label
                        class="form-label"
                        for="<?= e($name) ?>"
                    >
                        <?= e($label) ?>
                    </label>

                    <input
                        class="form-control"
                        id="<?= e($name) ?>"
                        name="<?= e($name) ?>"
                        type="<?= e($types[$name] ?? 'text') ?>"
                        value="<?= e($v[$name]) ?>"
                    >

                    <?php if (isset($errors[$name])): ?>

                        <div class="text-danger">
                            <?= e($errors[$name]) ?>
                        </div>

                    <?php endif; ?>

                </div>

                <?php if ($name === 'birthday'): ?>

                    <div class="mb-3">

                        <label
                            class="form-label"
                            for="sex"
                        >
                            Sex
                        </label>

                        <select
                            class="form-select"
                            id="sex"
                            name="sex"
                        >

                            <option value="">
                                -- Select --
                            </option>

                            <?php foreach (['Male', 'Female'] as $opt): ?>

                                <option
                                    value="<?= e($opt) ?>"
                                    <?= $v['sex'] === $opt ? 'selected' : '' ?>
                                >
                                    <?= e($opt) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <?php if (isset($errors['sex'])): ?>

                            <div class="text-danger">
                                <?= e($errors['sex']) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            <?php endforeach; ?>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Update
            </button>

            <a
                href="view.php?id=<?= e($id) ?>"
                class="btn btn-outline-primary"
            >
                View
            </a>

            <a
                href="index.php"
                class="btn btn-secondary"
            >
                Back to list
            </a>

        </form>

    </div>

<?php endif; ?>

</body>
</html>
```
