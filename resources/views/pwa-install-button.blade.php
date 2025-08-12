{{-- resources/views/pwa-install-button.blade.php --}}

<!-- PWA Install Banner (Desktop/Mobile) -->
<div id="pwa-install-banner" style="display: none; position: fixed; top: 0; left: 0; right: 0; z-index: 10000; background: linear-gradient(to right, #059669, #047857); color: white; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
  <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; max-width: 80rem; margin: 0 auto;">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div style="flex-shrink: 0;">
        <svg style="width: 32px; height: 32px;" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
      </div>
      <div>
        <h3 style="font-weight: 600; font-size: 18px; line-height: 28px; margin: 0;">Install Play App</h3>
        <p style="color: #bbf7d0; font-size: 14px; line-height: 20px; margin: 0;">Get faster access and work offline</p>
      </div>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
      <button id="pwa-install-btn" style="background-color: white; color: #047857; padding: 8px 16px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; transition: background-color 0.15s;">
        Install
      </button>
      <button id="pwa-dismiss-btn" style="color: #bbf7d0; padding: 8px; border-radius: 8px; background: transparent; border: none; cursor: pointer; transition: color 0.15s;">
        <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
      </button>
    </div>
  </div>
</div>

<!-- Fallback Button untuk Mobile -->
<button
  id="pwa-install-btn-mobile"
  type="button"
  aria-label="Install app"
  style="display: none; position: fixed; right: 16px; bottom: 16px; z-index: 9999; border-radius: 12px; padding: 14px 14px; background-color: #059669; color: white; font-weight: 600; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: none; cursor: pointer; transition: background-color 0.15s;">
  📱 Install App
</button>

<script>
(() => {
  const banner = document.getElementById('pwa-install-banner');
  const installBtn = document.getElementById('pwa-install-btn');
  const dismissBtn = document.getElementById('pwa-dismiss-btn');
  const mobileBtn = document.getElementById('pwa-install-btn-mobile');

  // Check if already installed
  const isInstalled = window.matchMedia('(display-mode: standalone)').matches || 
                     window.navigator.standalone === true ||
                     localStorage.getItem('pwa-dismissed') === 'true';
  
  if (isInstalled) return;

  let deferredPrompt = null;
  let showBannerTimeout = null;

  // Register Service Worker
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/serviceworker.js')
      .then(registration => {
        console.log('SW registered:', registration);
      })
      .catch(error => {
        console.log('SW registration failed:', error);
      });
  }

  // Listen for beforeinstallprompt event
  window.addEventListener('beforeinstallprompt', (e) => {
    console.log('beforeinstallprompt event fired');
    e.preventDefault();
    deferredPrompt = e;
    
    // Show banner after a short delay (avoid layout shift)
    showBannerTimeout = setTimeout(() => {
      if (!localStorage.getItem('pwa-dismissed')) {
        banner.style.display = 'block';
        // Add slide down animation
        banner.style.transform = 'translateY(-100%)';
        banner.style.transition = 'transform 0.3s ease-out';
        setTimeout(() => {
          banner.style.transform = 'translateY(0)';
        }, 10);
      }
    }, 2000); // Show after 2 seconds
  });

  // Install button click handler
  const handleInstallClick = async () => {
    if (!deferredPrompt) {
      // Fallback for browsers/situations where beforeinstallprompt doesn't fire
      const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
      
      if (isMobile) {
        alert('To install this app:\n1. Tap the menu button (⋮ or ☰)\n2. Select "Add to Home screen"\n3. Tap "Install" or "Add"');
      } else {
        alert('To install this app:\n1. Look for the install icon (⊕) in your browser\'s address bar\n2. Click it and select "Install"');
      }
      return;
    }

    try {
      // Show the install prompt
      deferredPrompt.prompt();
      
      // Wait for the user to respond to the prompt
      const { outcome } = await deferredPrompt.userChoice;
      
      console.log(`User response to the install prompt: ${outcome}`);
      
      if (outcome === 'accepted') {
        console.log('User accepted the install prompt');
        hideBanner();
      } else {
        console.log('User dismissed the install prompt');
      }
      
      // Reset the deferred prompt
      deferredPrompt = null;
    } catch (error) {
      console.error('Error during installation:', error);
    }
  };

  // Dismiss button click handler
  const handleDismissClick = () => {
    localStorage.setItem('pwa-dismissed', 'true');
    hideBanner();
  };

  // Hide banner function
  const hideBanner = () => {
    if (showBannerTimeout) {
      clearTimeout(showBannerTimeout);
    }
    
    banner.style.transform = 'translateY(-100%)';
    setTimeout(() => {
      banner.style.display = 'none';
    }, 300);
  };

  // Event listeners
  installBtn.addEventListener('click', handleInstallClick);
  dismissBtn.addEventListener('click', handleDismissClick);
  mobileBtn.addEventListener('click', handleInstallClick);

  // Show mobile button as fallback if banner doesn't show
  setTimeout(() => {
    if (banner.style.display === 'none' && !localStorage.getItem('pwa-dismissed')) {
      mobileBtn.style.display = 'block';
    }
  }, 5000);

  // Hide everything when app gets installed
  window.addEventListener('appinstalled', () => {
    console.log('PWA was installed');
    hideBanner();
    mobileBtn.style.display = 'none';
    localStorage.setItem('pwa-installed', 'true');
  });

  // Check if user is on mobile and show install hint
  const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
  const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
  
  // For iOS, beforeinstallprompt doesn't fire, so show the mobile button
  if (isIOS && !localStorage.getItem('pwa-dismissed')) {
    setTimeout(() => {
      mobileBtn.style.display = 'block';
    }, 3000);
  }

  // Clear dismissed status after 7 days
  const dismissTime = localStorage.getItem('pwa-dismiss-time');
  if (dismissTime && Date.now() - parseInt(dismissTime) > 7 * 24 * 60 * 60 * 1000) {
    localStorage.removeItem('pwa-dismissed');
    localStorage.removeItem('pwa-dismiss-time');
  }
})();
</script>
