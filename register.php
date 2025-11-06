<?php
// DEVELOPMENT: show all errors (turn off in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('db.php'); // ensure this file sets $conn = mysqli_connect(...)

$msg = "";
$success = false;

if (isset($_POST['submit'])) {
    // Basic server-side validation & sanitization
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $rawPassword = $_POST['password'] ?? '';

    if ($first === '' || $last === '' || $email === '' || $contact === '' || $username === '' || $rawPassword === '') {
        $msg = '<div class="alert error">Please fill all required fields.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="alert error">Please provide a valid email address.</div>';
    } else {
        // Hash password
        $password = password_hash($rawPassword, PASSWORD_DEFAULT);

        // Handle image upload (optional)
        $image_name = 'default.png';
        if (!empty($_FILES['profile_image']['name'])) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $msg = '<div class="alert error">Failed to create uploads directory.</div>';
                }
            }

            if ($msg === "") {
                $tmpName = $_FILES['profile_image']['tmp_name'];
                $origName = basename($_FILES['profile_image']['name']);
                $fileSize = $_FILES['profile_image']['size'];
                $fileType = mime_content_type($tmpName);

                // Accept only common image MIME types and size < 3MB
                $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($fileType, $allowedMime, true)) {
                    $msg = '<div class="alert error">Profile image must be JPG, PNG, GIF or WEBP.</div>';
                } elseif ($fileSize > 3 * 1024 * 1024) {
                    $msg = '<div class="alert error">Profile image must be smaller than 3MB.</div>';
                } else {
                    // Make filename safe and unique
                    $ext = pathinfo($origName, PATHINFO_EXTENSION);
                    $safeBase = preg_replace("/[^A-Z0-9._-]/i", "_", pathinfo($origName, PATHINFO_FILENAME));
                    $image_name = strtolower($safeBase . '_' . time() . '.' . $ext);
                    $targetFile = $uploadDir . $image_name;

                    if (!move_uploaded_file($tmpName, $targetFile)) {
                        $msg = '<div class="alert error">Failed to save uploaded image.</div>';
                    }
                }
            }
        }

        // If still no error, insert into DB using prepared statement
        if ($msg === "") {
            // Prevent duplicate username/email (optional)
            $checkSql = "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1";
            if ($stmtCheck = mysqli_prepare($conn, $checkSql)) {
                mysqli_stmt_bind_param($stmtCheck, "ss", $username, $email);
                mysqli_stmt_execute($stmtCheck);
                mysqli_stmt_store_result($stmtCheck);
                if (mysqli_stmt_num_rows($stmtCheck) > 0) {
                    $msg = '<div class="alert error">Username or email already exists. Try another.</div>';
                }
                mysqli_stmt_close($stmtCheck);
            } else {
                $msg = '<div class="alert error">Database error (check connection).</div>';
            }
        }

        if ($msg === "") {
            $insertSql = "INSERT INTO users (first_name, last_name, email, contact, username, password, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $insertSql)) {
                mysqli_stmt_bind_param($stmt, "sssssss", $first, $last, $email, $contact, $username, $password, $image_name);
                if (mysqli_stmt_execute($stmt)) {
                    $success = true;
                    $msg = '<div class="alert success">Account created successfully! You can now <a href="login.php">login here</a>.</div>';
                } else {
                    // On failure, if we saved an uploaded file, consider deleting it (optional)
                    if ($image_name !== 'default.png' && file_exists(__DIR__ . '/uploads/' . $image_name)) {
                        @unlink(__DIR__ . '/uploads/' . $image_name);
                    }
                    $msg = '<div class="alert error">Database Error: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
                }
                mysqli_stmt_close($stmt);
            } else {
                $msg = '<div class="alert error">Failed to prepare statement: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Create Account</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg,#e0e7ff 0%,#f8f9fc 100%);
        font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        min-height: 100vh;
        display:flex;
        align-items:center;
        justify-content:center;
        margin:0;
        padding:20px;
    }

    .auth-box {
        background:#fff;
        border-radius:14px;
        border:5px solid #212a72;
        box-shadow:0 10px 30px rgba(0,0,0,0.08);
        display:flex;
        overflow:hidden;
        max-width:900px;
        width:100%;
        min-height:320px;
    }

    .auth-left {
        background:#f0f2ff;
        flex:0.9;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        padding:18px;
    }

    .auth-left img{max-width:160px;margin-bottom:10px;}
    .auth-left h5{font-weight:600;margin-bottom:4px;}
    .auth-left p{color:#6c757d;font-size:.85rem;}

    .auth-right{flex:1.3;display:flex;align-items:center;justify-content:center;padding:18px 28px;}

    .form-card{width:100%;max-width:360px;}
    .form-card h4{text-align:center;font-weight:700;margin-bottom:6px;}
    .form-card p{text-align:center;color:#6c757d;margin-bottom:14px;font-size:.9rem;}

    .form-control{border-radius:8px;padding:8px 10px;font-size:.92rem;}
    .btn-primary{width:100%;border-radius:8px;background:#212a72;border:none;padding:9px;font-weight:600;}
    .btn-primary:hover{background:#1a215c;}

    .alert{padding:10px;border-radius:8px;margin-bottom:12px;font-size:.95rem;}
    .alert.success{background:#e7ffe9;color:#155724;border:1px solid #5cb85c;}
    .alert.error{background:#ffeaea;color:#721c24;border:1px solid #f5c6cb;}

    @media (max-width: 900px){
        .auth-box{flex-direction:column;max-width:420px;min-height:auto;}
        .auth-left{border-bottom:3px solid #212a72;}
    }
</style>
</head>
<body>

<div class="auth-box" role="region" aria-label="Create account">
    <div class="auth-left text-center">
        <!-- You had embedded a base64 image earlier; you can replace src with your image or external svg -->
        <img src="data:image/png;base64,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" alt="Illustration">
        <h5>Already Have an Account?</h5>
        <p>We are happy to have you back</p>
        <a href="login.php" class="btn btn-outline-primary mt-2">Login</a>
    </div>

    <div class="auth-right">
        <div class="form-card">
            <h4>Create Account</h4>
            <p>Get started by creating your account</p>

            <?php if ($msg) echo $msg; ?>

            <?php if (!$success): ?>
            <form method="post" enctype="multipart/form-data" novalidate>
                <div class="mb-2">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>">
                </div>
                <div class="mb-2">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
                </div>
                <div class="mb-2">
                    <input type="email" name="email" class="form-control" placeholder="Email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                <div class="mb-2">
                    <input type="text" name="contact" class="form-control" placeholder="Contact" required value="<?= isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : '' ?>">
                </div>
                <div class="mb-2">
                    <input type="text" name="username" class="form-control" placeholder="Username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>
                <div class="mb-2">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <input type="file" name="profile_image" class="form-control" accept="image/*">
                </div>

                <div class="form-check mb-3 text-start">
                    <input type="checkbox" id="termsCheck" class="form-check-input" required>
                    <label for="termsCheck" class="form-check-label small text-muted">I agree to the Terms and Conditions.</label>
                </div>

                <button type="submit" name="submit" class="btn btn-primary">Register</button>

                <div class="text-center mt-3">
                    <p class="text-muted small mb-2">or sign up with</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn" title="Google"><img src="https://cdn-icons-png.flaticon.com/512/2991/2991148.png" width="18" alt="Google"></button>
                        <button type="button" class="btn" title="Apple"><img src="https://cdn-icons-png.flaticon.com/512/731/731985.png" width="18" alt="Apple"></button>
                        <button type="button" class="btn" title="Facebook"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="18" alt="Facebook"></button>
                    </div>
                </div>
            </form>
            <?php else: ?>
                <div class="text-center mt-2">
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>
