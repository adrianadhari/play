{{-- resources/views/pwa-install-button.blade.php --}}

<!-- PWA Install Banner (Desktop/Mobile) -->
<div id="pwa-install-banner" class="hidden fixed top-0 left-0 right-0 z-[10000] bg-gradient-to-r from-green-600 to-green-700 text-white shadow-xl">
  <div class="flex items-center justify-between p-4 max-w-7xl mx-auto">
    <div class="flex items-center space-x-3">
      <div class="flex-shrink-0">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
      </div>
      <div>
        <h3 class="font-semibold text-lg">Install Play App</h3>
        <p class="text-green-100 text-sm">Get faster access and work offline</p>
      </div>
    </div>
    <div class="flex items-center space-x-2">
      <button id="pwa-install-btn" class="bg-white text-green-700 px-4 py-2 rounded-lg font-semibold hover:bg-green-50 transition-colors">
        Install
      </button>
      <button id="pwa-dismiss-btn" class="text-green-100 hover:text-white p-2 rounded-lg transition-colors">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
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
  class="hidden fixed right-4 bottom-4 z-[9999] rounded-xl px-3.5 py-2.5
         bg-green-600 text-white font-semibold shadow-lg hover:bg-green-700
         focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
        banner.classList.remove('hidden');
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
      banner.classList.add('hidden');
    }, 300);
  };

  // Event listeners
  installBtn.addEventListener('click', handleInstallClick);
  dismissBtn.addEventListener('click', handleDismissClick);
  mobileBtn.addEventListener('click', handleInstallClick);

  // Show mobile button as fallback if banner doesn't show
  setTimeout(() => {
    if (banner.classList.contains('hidden') && !localStorage.getItem('pwa-dismissed')) {
      mobileBtn.classList.remove('hidden');
    }
  }, 5000);

  // Hide everything when app gets installed
  window.addEventListener('appinstalled', () => {
    console.log('PWA was installed');
    hideBanner();
    mobileBtn.classList.add('hidden');
    localStorage.setItem('pwa-installed', 'true');
  });

  // Check if user is on mobile and show install hint
  const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
  const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
  
  // For iOS, beforeinstallprompt doesn't fire, so show the mobile button
  if (isIOS && !localStorage.getItem('pwa-dismissed')) {
    setTimeout(() => {
      mobileBtn.classList.remove('hidden');
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
