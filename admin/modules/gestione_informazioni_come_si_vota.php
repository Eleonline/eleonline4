<?php require_once '../includes/check_access.php'; ?>
<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-vote-yea me-2"></i>Come si vota</h3>
      </div>
      <div class="card-body">

        <!-- Form per inserimento/modifica -->
        <form id="form">
          <div class="form-group">
            <label>Titolo</label>
            <input type="text" name="titolo" class="form-control" value="">
          </div>
          <div class="form-group">
            <label>Descrizione</label>
            <textarea name="descrizione" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Testo</label>
            <textarea name="testo" class="form-control" rows="10"></textarea>
          </div>
          <button type="submit" class="btn btn-primary mt-2">Salva</button>
          <button type="reset" class="btn btn-secondary mt-2">Annulla</button>
        </form>

        <hr>

        <!-- Tabella elenco -->
        <h5>Elenco Come si vota</h5>
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Titolo</th>
              <th>Descrizione</th>
              <th>Testo</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Scheda A</td>
              <td>Descrizione A</td>
              <td>Testo completo A</td>
              <td>
                <button class="btn btn-sm btn-info">Modifica</button>
                <button class="btn btn-sm btn-danger">Elimina</button>
              </td>
            </tr>
            <tr>
              <td>Scheda B</td>
              <td>Descrizione B</td>
              <td>Testo completo B</td>
              <td>
                <button class="btn btn-sm btn-info">Modifica</button>
                <button class="btn btn-sm btn-danger">Elimina</button>
              </td>
            </tr>
            <tr>
              <td>Scheda C</td>
              <td>Descrizione C</td>
              <td>Testo completo C</td>
              <td>
                <button class="btn btn-sm btn-info">Modifica</button>
                <button class="btn btn-sm btn-danger">Elimina</button>
              </td>
            </tr>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</section>

<!-- CKEditor 5 Classic -->
<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.0/build/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('textarea[name="testo"]'))
  .catch(error => { console.error(error); });
</script>
