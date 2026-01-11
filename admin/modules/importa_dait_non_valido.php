<?php require_once '../includes/check_access.php'; ?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-warning shadow-sm">

      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Importazione non supportata
        </h3>
      </div>

      <div class="card-body text-center">
        <div class="py-5">
          <i class="fas fa-ban fa-3x mb-3 text-warning"></i>

          <h4 class="mb-2">Tipo di importazione non supportata</h4>

          <p class="text-muted">
            Il tipo di consultazione per 
            <strong>
              <?php echo strtoupper($tipo_consultazione ?? 'N/D'); ?>
            </strong>
            non è attualmente supportato dal sistema.
          </p>

          <p class="text-muted">
            Non è possibile procedere con l’importazione dal DAIT.
          </p>
        </div>
      </div>

    </div>
  </div>
</section>
