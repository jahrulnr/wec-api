/**
 * Authentication Form Validation and UX Enhancements
 */
document.addEventListener('DOMContentLoaded', function() {
  // Password strength indicator
  const passwordFields = document.querySelectorAll('input[type="password"]');
  
  passwordFields.forEach(field => {
    if (field.name === 'password') {
      field.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        updatePasswordStrengthIndicator(this, strength);
      });
      
      // Add strength indicator after password fields
      const indicator = document.createElement('div');
      indicator.className = 'password-strength-indicator';
      indicator.innerHTML = `
        <div class="strength-bars">
          <span></span>
          <span></span>
          <span></span>
          <span></span>
        </div>
        <div class="strength-text">Password strength</div>
      `;
      
      if (field.parentNode) {
        field.parentNode.insertBefore(indicator, field.nextSibling);
      }
    }
  });
  
  // Form submission with loading state
  const authForms = document.querySelectorAll('.form-container form');
  authForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
        submitBtn.disabled = true;
      }
    });
  });
  
  // Two-factor code input auto tab
  const codeInput = document.querySelector('.verification-code');
  if (codeInput) {
    codeInput.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '').substr(0, 6);
    });
    
    codeInput.addEventListener('keydown', function(e) {
      // Allow only numbers and control keys
      if (!/^[0-9]$/.test(e.key) && 
          !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
        e.preventDefault();
      }
    });
  }
  
  // Hide alerts after 5 seconds
  const alerts = document.querySelectorAll('.alert-box');
  if (alerts.length) {
    setTimeout(() => {
      alerts.forEach(alert => {
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.style.display = 'none';
        }, 300);
      });
    }, 5000);
  }
});

/**
 * Calculate password strength from 0-4
 * @param {string} password 
 * @returns {number} strength level
 */
function calculatePasswordStrength(password) {
  if (!password) return 0;
  
  let strength = 0;
  
  // Length
  if (password.length >= 8) strength++;
  
  // Contains lowercase
  if (/[a-z]/.test(password)) strength++;
  
  // Contains uppercase
  if (/[A-Z]/.test(password)) strength++;
  
  // Contains number or special char
  if (/[0-9]/.test(password) || /[^a-zA-Z0-9]/.test(password)) strength++;
  
  return strength;
}

/**
 * Update the password strength indicator
 * @param {HTMLElement} passwordField 
 * @param {number} strength 
 */
function updatePasswordStrengthIndicator(passwordField, strength) {
  // Find the indicator that follows this password field
  const indicator = passwordField.nextElementSibling;
  if (!indicator || !indicator.classList.contains('password-strength-indicator')) return;
  
  // Update bars
  const bars = indicator.querySelectorAll('.strength-bars span');
  const strengthText = indicator.querySelector('.strength-text');
  
  // Reset all bars
  bars.forEach((bar, index) => {
    bar.className = index < strength ? `strength-${strength}` : '';
  });
  
  // Update text
  const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
  strengthText.textContent = password.value ? labels[strength] : 'Password strength';
  
  // Add CSS classes based on strength
  strengthText.className = 'strength-text';
  if (strength > 0) {
    strengthText.classList.add(`text-strength-${strength}`);
  }
}
