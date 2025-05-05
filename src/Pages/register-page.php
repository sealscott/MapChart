<?php
session_start();
    $errors = $_SESSION['register_errors'] ?? []; // Get errors from session, if any
    unset($_SESSION['register_errors']); // Clear the errors from the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/src/CSS/register.css">
    <link rel="icon" type="image/x-icon" href="/src/Icons/icon.png">
</head>

<body>
    <header>
        <h1 class="WeatherMap-header">WeatherMap Social</h1>
        <h2>Register</h2>
    </header>

    <section>
        <form action="/src/PHP-Backend/register.php" method="POST">
            <div class="form-group">
                <input placeholder="Email" type="email" id="email" name="email" required>
                <?php if (isset($errors['email'])): ?>
                    <p class="error"><?php echo htmlspecialchars($errors['email']); ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input placeholder="Display Name" type="text" id="display-name" name="display-name" required>
            </div>

            <div class="form-group">
                <input placeholder="Password" type="password" id="password" name="password" required>
                <?php if (isset($errors['password'])): ?>
                    <p class="error"><?php echo htmlspecialchars($errors['password']); ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input placeholder="Confirm Password" type="password" id="confirm-password" name="confirm-password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <p class="error"><?php echo htmlspecialchars($errors['confirm_password']); ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" value="submit" name="submit">Register</button>
        </form>
    </section>
</body>
</html>
