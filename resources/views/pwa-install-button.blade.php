{{-- resources/views/pwa-install-button.blade.php --}}
<button
  id="pwa-install-btn"
  type="button"
  aria-label="Install app"
  class="hidden fixed right-4 bottom-4 z-[9999] rounded-xl px-3.5 py-2.5
         bg-sky-500 text-white font-semibold shadow-lg hover:bg-sky-600
         focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
  Install app
</button>

<script>
(() => {
  const btn = document.getElementById('pwa-install-btn');

  // Sembunyikan jika sudah terpasang
  const installed = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
  if (installed) return;

  let deferredPrompt = null;

  // Muncul hanya jika browser support & syarat terpenuhi
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    // Tunda sedikit untuk menghindari layout shift sesaat page load
    setTimeout(() => { btn.classList.remove('hidden'); }, 300);
  }, { once: true });

  // Klik -> tampilkan prompt
  btn.addEventListener('click', async () => {
    if (!deferredPrompt) {
      // fallback edukasi: arahkan ke menu Add to Home screen
      alert('Untuk memasang aplikasi, pilih "Add to Home screen" dari menu browser.');
      return;
    }
    deferredPrompt.prompt();
    try { await deferredPrompt.userChoice; } finally {
      deferredPrompt = null;
      btn.classList.add('hidden');
    }
  });

  // Jika terpasang dari mana pun, sembunyikan tombol
  window.addEventListener('appinstalled', () => btn.classList.add('hidden'));
})();
</script>
