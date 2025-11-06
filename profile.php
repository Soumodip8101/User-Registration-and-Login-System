<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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

        .profile-card {
            background: #fff;
            border-radius: 18px;
            border: 5px solid #212a72;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 35px 45px;
            width: 100%;
            max-width: 550px;
            text-align: center;
        }

        h2 {
            font-weight: 700;
            color: #000;
            margin-bottom: 25px;
        }

        img {
            border-radius: 50%;
            border: 4px solid #212a72;
            margin-bottom: 20px;
            width: 120px;
            height: 120px;
            object-fit: cover;
        }

        table {
            margin: 0 auto;
            text-align: left;
            font-size: 0.95rem;
            color: #333;
        }

        table td {
            padding: 6px 12px;
        }

        strong {
            color: #212a72;
        }

        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #212a72;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .logout-btn:hover {
            background-color: #1a215c;
        }

        @media (max-width: 600px) {
            .profile-card {
                padding: 25px 20px;
                max-width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="profile-card">
    <h2>My Profile</h2>
    <img src="uploads/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Picture">
    <table>
        <tr><td><strong>First Name:</strong></td><td><?php echo htmlspecialchars($user['first_name']); ?></td></tr>
        <tr><td><strong>Last Name:</strong></td><td><?php echo htmlspecialchars($user['last_name']); ?></td></tr>
        <tr><td><strong>Email:</strong></td><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
        <tr><td><strong>Contact:</strong></td><td><?php echo htmlspecialchars($user['contact']); ?></td></tr>
        <tr><td><strong>Username:</strong></td><td><?php echo htmlspecialchars($user['username']); ?></td></tr>
        <tr><td><strong>Registered On:</strong></td><td><?php echo htmlspecialchars($user['created_at']); ?></td></tr>
    </table>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
