<?php
session_start();

// Dummy user credentials
$valid_username = 'Somaport';
$valid_password = 'Somaport';

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['loggedin'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error_message = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - MedicamentStockDB</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a9d8f;
            --secondary-color: #264653;
            --accent-color: #e9c46a;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: var(--secondary-color);
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .card-header h4 {
            color: white;
            font-weight: 600;
            margin: 0;
            font-size: 1.8rem;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 20px 25px 0;
            border-style: solid;
            border-color: var(--secondary-color) transparent transparent transparent;
        }

        .card-body {
            padding: 2.5rem;
            background: white;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(42,157,143,0.1);
        }

        .input-group-text {
            background: white;
            border: 2px solid #e9ecef;
            border-left: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .input-group-text:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .btn-login {
            background: var(--primary-color);
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #228176;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 8px;
            padding: 12px 20px;
            border: none;
        }

        .brand-logo {
            width: 60px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="d-flex align-items-center">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-card">
                <div class="card-header">
                    <h4><i class="fas fa-prescription-bottle-medical"></i> Somaport Pharma</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div><?php echo $error_message; ?></div>
                        </div>
                    <?php endif; ?>
                    <form action="" method="post">
                        <div class="form-group mb-4">
                            <label for="username" class="form-label text-secondary">Nom d'utilisateur</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label for="password" class="form-label text-secondary">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button type="button" class="btn btn-outline-secondary" id="showPasswordBtn">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-login btn-block text-white">Connexion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts remain the same -->
<script>
    document.getElementById('showPasswordBtn').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>
</body>
</html>