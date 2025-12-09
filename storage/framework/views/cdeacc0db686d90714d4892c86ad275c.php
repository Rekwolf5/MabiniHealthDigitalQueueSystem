<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mabini Health Center</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            background: url('/images/Mabini.png?v=<?php echo time(); ?>') center/cover no-repeat fixed !important;
            min-height: 100vh !important;
        }
        .login-container {
            background: transparent !important;
            background-color: transparent !important;
        }
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-container img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-bottom: 1rem;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <img src="<?php echo e(asset('images/health-center-logo.png')); ?>?v=<?php echo e(time()); ?>" alt="Mabini Health Center Logo">
            </div>
            <div class="login-header">
                <h1>Mabini Health Center</h1>
                <p>Please sign in to continue</p>
            </div>

            <?php if($errors->any()): ?>
                <div class="error-message">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="login-form">
                <?php echo csrf_field(); ?>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="remember" id="remember" style="width: auto; margin: 0;">
                        <label for="remember" style="margin: 0; cursor: pointer;">Remember me</label>
                    </div>
                    <a href="<?php echo e(route('password.request')); ?>" style="color: #059669; text-decoration: none; font-size: 0.875rem;">
                        Forgot Password?
                    </a>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: #718096; margin: 0;">
                    <a href="<?php echo e(route('password.request')); ?>" style="color: #059669; text-decoration: none;">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\ramir\Desktop\Health-Center-Queue-System-main\resources\views/auth/login.blade.php ENDPATH**/ ?>