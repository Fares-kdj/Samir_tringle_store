// Afficher une notification
function showLoginNotification(message, isError = true) {
    const notification = document.createElement('div');
    notification.className = 'login-notification';
    notification.innerHTML = `<i class="bi ${isError ? 'bi-exclamation-triangle' : 'bi-check-circle'}"></i> ${message}`;
    document.body.appendChild(notification);
  
    setTimeout(() => {
      notification.classList.add('show');
    }, 100);
  
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 3000);
  }
  
  // Bascule d'affichage du mot de passe
  document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    this.classList.toggle('bi-eye', isPassword);
    this.classList.toggle('bi-eye-slash', !isPassword);
  });
  
  // Soumission du formulaire
  document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault(); // Empêche la soumission par défaut
  
    const password = document.getElementById('password').value.trim();
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');
  
    // Réinitialiser les messages
    errorMessage.style.display = 'none';
    successMessage.style.display = 'none';
  
    // Validation côté client
    if (!password) {
      showLoginNotification('يرجى إدخال كلمة المرور', true);
      errorMessage.textContent = 'يرجى إدخال كلمة المرور';
      errorMessage.style.display = 'block';
      return;
    }
  
    // Soumission au serveur via fetch
    try {
      const response = await fetch('', { // Action vide car le formulaire soumet à la même page
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ password })
      });
  
      if (response.redirected) {
        // Redirection gérée par PHP (vers dashboard.php)
        showLoginNotification('تم تسجيل الدخول بنجاح', false);
        successMessage.textContent = 'تم تسجيل الدخول بنجاح';
        successMessage.style.display = 'block';
        window.location.href = response.url; // Suivre la redirection
      } else {
        // Erreur (mot de passe incorrect)
        const text = await response.text();
        // Extraire le message d'erreur du HTML (approche simplifiée)
        const parser = new DOMParser();
        const doc = parser.parseFromString(text, 'text/html');
        const serverError = doc.querySelector('.error-message')?.textContent || 'كلمة المرور غير صحيحة';
        showLoginNotification(serverError, true);
        errorMessage.textContent = serverError;
        errorMessage.style.display = 'block';
      }
    } catch (error) {
      showLoginNotification('حدث خطأ أثناء الاتصال بالخادم', true);
      errorMessage.textContent = 'حدث خطأ أثناء الاتصال بالخادم';
      errorMessage.style.display = 'block';
    }
  });