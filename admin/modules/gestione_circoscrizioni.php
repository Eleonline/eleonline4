<?php
require_once '../includes/check_access.php';

// Dati fittizi PHP
$circoscrizioni = [
    ['numero' => '1', 'denominazione' => 'Centro città'],
    ['numero' => '2', 'denominazione' => 'Nord'],
    ['numero' => '3', 'denominazione' => 'Sud'],
    ['numero' => '4', 'denominazione' => 'Est'],
    ['numero' => '5', 'denominazione' => 'Ovest'],
];
?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-flag me-2"></i>Gestione Circoscrizioni</h3>
      </div>

      <div class="card-body table-responsive" style="max-height:400px; overflow-y:auto;">
        <form id="formCircoscrizione" class="mb-3" onsubmit="aggiungiCircoscrizione(event)">
          <div class="row">
            <div class="col-md-2">
              <label for="numero">Numero</label>
              <input type="number" class="form-control" id="numero" required>
            </div>
            <div class="col-md-7">
              <label for="denominazione">Denominazione</label>
              <input type="text" class="form-control" id="denominazione" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-50 me-2" id="btnAggiungi">Aggiungi</button>
              <button type="button" class="btn btn-secondary w-50 d-none" id="btnAnnulla" onclick="annullaModifica()">Annulla</button>
            </div>
          </div>
        </form>
      </div>

      <div class="card shadow-sm mb-3">
        <div class="card-header bg-secondary text-white">
          <h3 class="card-title">Lista Circoscrizioni</h3>
        </div>
        <div class="card-body table-responsive" style="overflow-y:auto; border: 1px solid #dee2e6; border-radius: 0 0 0.25rem 0.25rem;">
          <table class="table table-striped mb-0" id="tabellaCircoscrizioni">
            <thead>
              <tr>
                <th style="width: 10%;">Numero</th>
                <th>Denominazione</th>
                <th style="width: 20%;">Azioni</th>
              </tr>
            </thead>
            <tbody id="righeCircoscrizioni">
              <!-- Righe generate dinamicamente -->
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer text-muted">
        Puoi gestire le circoscrizioni elettorali da qui.
      </div>
    </div>
  </div>
</section>

<script>
  let circoscrizioni = <?php echo json_encode($circoscrizioni); ?>;
  let indiceModifica = null;

  function aggiornaTabella() {
    const tbody = document.getElementById("righeCircoscrizioni");
    tbody.innerHTML = "";

    circoscrizioni.forEach((c, i) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${c.numero}</td>
        <td>${c.denominazione}</td>
        <td>
          <button class="btn btn-sm btn-warning me-1" onclick="modificaCircoscrizione(${i})">Modifica</button>
          <button class="btn btn-sm btn-danger" onclick="mostraConfermaEliminazione(${i})">Elimina</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function aggiungiCircoscrizione(e) {
    e.preventDefault();
    const numero = document.getElementById("numero").value.trim();
    const denominazione = document.getElementById("denominazione").value.trim();
    const btn = document.getElementById("btnAggiungi");

    if (numero === "" || denominazione === "") {
      alert("Compila tutti i campi.");
      return;
    }

    const numeroEsiste = circoscrizioni.some((c, i) =>
      c.numero === numero && i !== indiceModifica
    );

    if (indiceModifica !== null) {
      circoscrizioni[indiceModifica] = { numero, denominazione };
      indiceModifica = null;
      btn.textContent = "Aggiungi";
      btn.classList.remove("btn-success");
      btn.classList.add("btn-primary");

      document.getElementById("btnAnnulla").classList.add("d-none");
      alert("Modifica salvata con successo.");
    } else {
      if (numeroEsiste) {
        alert("Attenzione: esiste già una circoscrizione con questo numero. Verrà comunque aggiunta.");
      }
      circoscrizioni.push({ numero, denominazione });
      alert("Circoscrizione aggiunta.");
    }

    aggiornaTabella();
    e.target.reset();
  }

  function modificaCircoscrizione(index) {
    const c = circoscrizioni[index];
    document.getElementById("numero").value = c.numero;
    document.getElementById("denominazione").value = c.denominazione;
    indiceModifica = index;

    const btn = document.getElementById("btnAggiungi");
    const btnAnnulla = document.getElementById("btnAnnulla");

    btn.textContent = "Salva modifiche";
    btn.classList.remove("btn-primary");
    btn.classList.add("btn-success");

    btnAnnulla.classList.remove("d-none");

    document.querySelector(".card-header").scrollIntoView({ behavior: "smooth", block: "start" });
  }

  function annullaModifica() {
    indiceModifica = null;
    document.getElementById("formCircoscrizione").reset();

    const btn = document.getElementById("btnAggiungi");
    const btnAnnulla = document.getElementById("btnAnnulla");

    btn.textContent = "Aggiungi";
    btn.classList.remove("btn-success");
    btn.classList.add("btn-primary");

    btnAnnulla.classList.add("d-none");
  }

  function mostraConfermaEliminazione(index) {
    const c = circoscrizioni[index];
    if (confirm(`Sei sicuro di voler eliminare la circoscrizione ${c.numero} - ${c.denominazione}?`)) {
      confermaEliminazione(index);
    }
  }

  function confermaEliminazione(index) {
    circoscrizioni.splice(index, 1);
    aggiornaTabella();
    alert("Circoscrizione eliminata.");
  }

  window.onload = function() {
    aggiornaTabella();
  };
</script>
