<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    if ($password === 'ramzi') { // Simple password
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
        exit();
    } else {
        $error_message = "كلمة المرور غير صحيحة.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول</title>
  <link rel="stylesheet" href="./log2.css"> <!-- Remplacez par le chemin de votre CSS -->
  <!-- Inclure Bootstrap Icons pour les notifications et toggle -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
  <div class="login-container">
    <h2>تسجيل الدخول</h2>
    <form id="loginForm" method="POST" action="">
      <div class="form-group password-toggle">
        <label for="password">كلمة المرور</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="أدخل كلمة المرور" required>
        <i id="togglePassword" class="bi bi-eye"></i>
      </div>
      <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>
      <div class="error-message" id="errorMessage" style="display: none;"></div>
      <div class="success-message" id="successMessage" style="display: none;"></div>
      <div class="button-container">
        <button type="submit">تسجيل الدخول</button>
      </div>
    </form>
  </div>

  <script src="./log.js"></script> <!-- Remplacez par le chemin de votre JS -->
</body>
</html>