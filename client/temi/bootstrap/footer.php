<footer class="it-footer">
    <div class="it-footer-small-prints clearfix">
		<div class="container border-bottom">
			<p class="text-center"><a href="<?php echo $link_autore;?>" target="_blank"><?php echo $versione_eleonline;?></a></p>
		</div>
    </div>
</footer>
<style>
  .cookie-banner {
    position: fixed !important;
    bottom: 1rem !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    z-index: 1050 !important;
    background-color: #fff !important;
    color: #212529 !important;
    padding: 0.75rem 1.25rem !important;
    max-width: 700px !important;
    width: 90% !important;
    border-radius: 0.375rem !important;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid #dee2e6 !important;
  }
</style>
<!-- BANNER COOKIE (Bootstrap Italia) fisso in basso allo schermo -->
<div id="cookie-banner" class="cookie-banner d-none" role="alert" aria-live="polite" aria-atomic="true">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      Questo sito utilizza cookie tecnici. <a href="modules.php?op=50&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>" class="text-primary text-decoration-underline">Leggi di più</a>.
    </div>
    <button id="accept-cookies" class="btn btn-primary btn-sm">Accetta</button>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const banner = document.getElementById('cookie-banner');
    const button = document.getElementById('accept-cookies');

    const consent = JSON.parse(localStorage.getItem('cookie_accepted'));
    const now = new Date();

    const durataGiorni = 30;

    if (!consent || new Date(consent.expires) < now) {
      banner.classList.remove('d-none');
    }

    button.addEventListener('click', function () {
      const expires = new Date();
      expires.setDate(expires.getDate() + durataGiorni);

      localStorage.setItem('cookie_accepted', JSON.stringify({
        value: true,
        expires: expires.toISOString()
      }));

      banner.classList.add('d-none');
    });
  });
</script>

