<?php
session_start();
require_once '../config/db_config.php';

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Login | Matthew Rillera's Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap');
        
        body {
            background: #f8f9fa;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 0;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 450px;
            padding: 50px;
            border: 1px solid #eee;
        }
        .brand-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .btn-dark {
            border-radius: 0;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            background: #000;
        }
        .form-control {
            border-radius: 0;
            padding: 12px;
            border: 1px solid #eee;
            background: #fbfbfb;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #000;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card animate__animated animate__fadeIn">
        <div class="text-center mb-5">
            <h1 class="brand-text">Matthew Rillera</h1>
            <p class="text-secondary small text-uppercase" style="letter-spacing: 3px;">Artist Access</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger small py-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary text-uppercase">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary text-uppercase">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-dark w-100 mb-3">SIGN IN</button>
            <div class="text-center">
                <a href="../index.php" class="text-secondary small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Back to Gallery</a>
            </div>
        </form>
    </div>
</body>
</html>
