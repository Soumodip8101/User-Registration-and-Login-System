<?php
session_start();
include('db.php');

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['user'] = $row;
        header("Location: profile.php");
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #a7c5ff 0%, #b3f5c2 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-box {
            background: #fff;
            border-radius: 18px;
            border: 5px solid #212a72;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 35px 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            font-weight: 700;
            color: #000;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 0.95rem;
            margin-bottom: 14px;
        }

        .btn-primary {
            width: 100%;
            border-radius: 8px;
            background-color: #212a72;
            border: none;
            font-weight: 600;
            padding: 10px;
            font-size: 0.95rem;
        }

        .btn-primary:hover {
            background-color: #1a215c;
        }

        .error {
            background: #ffeaea;
            color: #d63333;
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 0.9rem;
        }

        p {
            margin-top: 15px;
            font-size: 0.9rem;
        }

        a {
            color: #212a72;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Welcome Back</h2>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" name="login" class="btn btn-primary">Login</button>
    </form>
    <p>Don’t have an account? <a href="register.php">Create one</a></p>
</div>

</body>
</html>
