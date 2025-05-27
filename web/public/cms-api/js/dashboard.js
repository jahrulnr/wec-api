/**
 * WEC API Dashboard Global Scripts
 * - CSRF Token handling for AJAX requests
 * - Session management helpers
 */

// Set up CSRF token for all AJAX requests
document.addEventListener('DOMContentLoaded', function() {
  // Get the CSRF token from meta tag
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  // Add CSRF token to all fetch requests
  const originalFetch = window.fetch;
  window.fetch = function(url, options = {}) {
    // Only add headers if this is a same-origin request
    if (url.toString().startsWith(window.location.origin) || url.toString().startsWith('/')) {
      options.headers = options.headers || {};
      options.headers['X-CSRF-TOKEN'] = token;
      
      // For POST requests without explicit Content-Type, set the proper one
      if (options.method && options.method.toUpperCase() === 'POST' && !options.headers['Content-Type']) {
        options.headers['Content-Type'] = 'application/json';
      }
    }
    
    return originalFetch(url, options);
  };
  
  // Add CSRF token to all axios requests if axios is loaded
  if (window.axios) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
  }
  
  // Add CSRF token to jQuery ajax requests if jQuery is loaded
  if (window.jQuery) {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': token
      }
    });
  }
  
  // Check if user's session is still valid periodically (if logged in)
  if (document.body.classList.contains('hold-transition')) {
    setInterval(function() {
      fetch('/cms-api/session/check', { method: 'POST' })
        .then(response => {
          if (!response.ok) {
            // Redirect to login page if session expired
            window.location.href = '/cms-api/login';
          }
          return response.json();
        })
        .then(data => {
          // Nothing to do if session is valid
        })
        .catch(error => {
          console.error('Session check error:', error);
        });
    }, 5 * 60 * 1000); // Check every 5 minutes
  }
});

/**
 * Flash a notification message
 * @param {string} message - Message to display
 * @param {string} type - 'success', 'error', 'warning', or 'info'
 * @param {number} duration - Duration in milliseconds
 */
function flashNotification(message, type = 'info', duration = 3000) {
  const container = document.querySelector('.content-wrapper') || document.body;
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `flash-notification ${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas ${getIconForType(type)}"></i>
      <span>${message}</span>
    </div>
    <button class="close-btn">&times;</button>
  `;
  
  // Add to DOM
  container.appendChild(notification);
  
  // Add visible class to trigger animation
  setTimeout(() => notification.classList.add('visible'), 10);
  
  // Auto-dismiss after duration
  const dismissTimeout = setTimeout(() => {
    dismissNotification(notification);
  }, duration);
  
  // Close button handler
  const closeBtn = notification.querySelector('.close-btn');
  closeBtn.addEventListener('click', () => {
    clearTimeout(dismissTimeout);
    dismissNotification(notification);
  });
}

/**
 * Remove a notification with animation
 * @param {HTMLElement} notification - Notification element to dismiss
 */
function dismissNotification(notification) {
  notification.classList.remove('visible');
  setTimeout(() => {
    if (notification.parentNode) {
      notification.parentNode.removeChild(notification);
    }
  }, 300);
}

/**
 * Get icon class for notification type
 * @param {string} type - Notification type
 * @returns {string} - FontAwesome icon class
 */
function getIconForType(type) {
  switch(type) {
    case 'success': return 'fa-check-circle';
    case 'error': return 'fa-exclamation-circle';
    case 'warning': return 'fa-exclamation-triangle';
    default: return 'fa-info-circle';
  }
}
