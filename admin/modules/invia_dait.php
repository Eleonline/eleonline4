<!-- ===============================
     WebService – Invii
     =============================== -->
<style>
/* Colori personalizzati per i bottoni */
.btn-indigo { background-color: #6610f2; color: #fff; }
.btn-orange { background-color: #fd7e14; color: #fff; }
.btn-gold   { background-color: #ffc107; color: #000; }

/* Ridurre dimensioni bottoni Election Day */
.btn-election {
    padding: 0.25rem 0.5rem;  /* meno altezza e larghezza */
    font-size: 0.875rem;       /* testo leggermente più piccolo */
    line-height: 1.2;
}

/* Se vuoi ridurre anche lo spazio tra badge e testo */
.btn-election strong {
    display: block;
    margin-bottom: 0.2rem;
}
</style>
<?php
$electionDay = [
    ['nome'=>'Politiche','btnClass'=>'btn-info','badgeText'=>'Inviato','badgeColor'=>'success'],
    ['nome'=>'Regionali','btnClass'=>'btn-indigo','badgeText'=>'In Attesa','badgeColor'=>'warning'],
    ['nome'=>'Comunali','btnClass'=>'btn-orange','badgeText'=>'Errore','badgeColor'=>'danger'],
    ['nome'=>'Referendum','btnClass'=>'btn-gold','badgeText'=>'Pronto','badgeColor'=>'primary'],
];

$operazioni = [
    ['nome'=>'Invio Affluenza','badgeColor'=>'secondary','badgeText'=>'Non inviato','ultimo'=>'In attesa di comunicazione','btnColor'=>'primary','btnText'=>'Invio Affluenza','disabled'=>false],
	['nome'=>'Invio Liste Elettori','badgeColor'=>'success','badgeText'=>'Inviato','ultimo'=>'23/04/2024 – 10:15','btnColor'=>'secondary','btnText'=>'Invio Liste Elettori','disabled'=>false],
    ['nome'=>'Invio Candidati','badgeColor'=>'danger','badgeText'=>'Errore','ultimo'=>'23/04/2024 – 09:47','btnColor'=>'warning','btnText'=>'Ritenta Invio','disabled'=>false],
    ['nome'=>'Invio Sezioni Elettorali','badgeColor'=>'secondary','badgeText'=>'Non inviato','ultimo'=>'Nessun invio effettuato','btnColor'=>'primary','btnText'=>'Invio Sezioni','disabled'=>false],
    ['nome'=>'Invio Risultati Scrutinio','badgeColor'=>'success','badgeText'=>'Inviato','ultimo'=>'22/04/2024 – 18:30','btnColor'=>'secondary','btnText'=>'Invio Risultati','disabled'=>true],
];
?>

<section class="content">
  <div class="container-fluid">
    <h2><i class="fas fa-server"></i> WebService – Invii</h2>

    <!-- Modalità Invio -->
    <div class="card card-primary shadow-sm mb-3">
      <div class="card-header">
        <h3 class="card-title">Modalità Invio</h3>
      </div>
      <div class="card-body">
        <div class="custom-control custom-switch">
          <input type="checkbox" class="custom-control-input" id="modoTest" checked>
          <label class="custom-control-label" for="modoTest">Modalità TEST attiva</label>
        </div>
      </div>
    </div>

    <!-- Election Day -->
    <div class="card card-primary shadow-sm mb-3">
      <div class="card-header">
        <h3 class="card-title">Election Day</h3>
      </div>
      <div class="card-body">
        <div class="d-flex flex-wrap">
<?php foreach($electionDay as $ed): ?>
    <button class="btn <?= $ed['btnClass'] ?> btn-election mr-2 mb-2">
        <strong><?= $ed['nome'] ?></strong>
        <span class="badge badge-<?= $ed['badgeColor'] ?>"><?= $ed['badgeText'] ?></span>
    </button>
<?php endforeach; ?>
</div>
      </div>
    </div>

    <!-- Operazioni WebService -->
    <div class="card card-primary shadow-sm mb-3">
      <div class="card-header">
        <h3 class="card-title">Operazioni WebService</h3>
      </div>
      <div class="card-body table-responsive">
  <table class="table table-bordered table-hover mb-0">
    <thead class="thead-light">
      <tr>
        <th style="width:30%;">Operazione</th>
        <th style="width:45%;">Stato ultimo invio</th>
        <th style="width:25%;">Azione</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($operazioni as $op): ?>
      <tr>
        <td><?= $op['nome'] ?></td>
        <td>
          <span class="badge badge-<?= $op['badgeColor'] ?>"><?= $op['badgeText'] ?></span><br>
          <small>Ultimo invio: <?= $op['ultimo'] ?></small>
        </td>
        <td>
          <button class="btn btn-sm btn-<?= $op['btnColor'] ?>" <?= $op['disabled']?'disabled':'' ?>>
            <?= $op['btnText'] ?>
          </button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>


      <div class="card-footer text-muted">
        Puoi gestire gli invii WebService da qui.
      </div>
    </div>

  </div>
</section>
