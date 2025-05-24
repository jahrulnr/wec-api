<!-- Loading overlay -->
<style>
  #loading-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(30, 30, 30, 0.92);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.5s ease;
  }
  #loading-overlay.fade-out {
    opacity: 0;
    pointer-events: none;
  }
  .modern-spinner {
    width: 60px;
    height: 60px;
    border: 6px solid #3498db;
    border-top: 6px solid #f3f3f3;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .loading-text {
    color: #fff;
    font-size: 1.2rem;
    margin-top: 1.5rem;
    letter-spacing: 1px;
  }
</style>
<div id="loading-overlay">
  <div style="text-align:center;">
    <div class="modern-spinner"></div>
    <div class="loading-text">Loading, please wait...</div>
  </div>
</div>
<script>
  window.addEventListener('load', function() {
    setTimeout(function() {
      var overlay = document.getElementById('loading-overlay');
      if (overlay) {
        overlay.classList.add('fade-out');
        setTimeout(function() { overlay.style.display = 'none'; }, 500);
      }
    }, 1000); // 1 second delay after load
  });
</script>