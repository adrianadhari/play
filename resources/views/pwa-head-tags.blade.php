<!-- PWA Manifest dan Meta Tags -->
<link rel="manifest" href="{{ route('pwa.manifest') }}">
<meta name="theme-color" content="#000000">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="Play">
<meta name="mobile-web-app-capable" content="yes">

<!-- iOS specific meta tags -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Play">

<!-- Apple touch icons -->
<link rel="apple-touch-icon" href="/images/icons/icon-152x152.png">
<link rel="apple-touch-icon" sizes="152x152" href="/images/icons/icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/images/icons/icon-192x192.png">

<!-- Microsoft specific -->
<meta name="msapplication-TileImage" content="/images/icons/icon-144x144.png">
<meta name="msapplication-TileColor" content="#000000">

<!-- Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/serviceworker.js')
      .then(function(registration) {
        console.log('SW registered: ', registration);
      })
      .catch(function(registrationError) {
        console.log('SW registration failed: ', registrationError);
      });
  });
}
</script>
