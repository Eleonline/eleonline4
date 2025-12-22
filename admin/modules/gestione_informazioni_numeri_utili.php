<?php require_once '../includes/check_access.php'; ?>
<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-phone-alt me-2"></i>Numeri Utili</h3>
      </div>
      <div class="card-body">

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

        <h5>Elenco Numeri Utili</h5>
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
              <td>Polizia Municipale</td>
              <td>Emergenze e incidenti</td>
              <td>0801234567</td>
              <td>
                <button class="btn btn-sm btn-info">Modifica</button>
                <button class="btn btn-sm btn-danger">Elimina</button>
              </td>
            </tr>
            <tr>
              <td>Protezione Civile</td>
              <td>Contatti emergenza</td>
              <td>0807654321</td>
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

<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.0/build/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('textarea[name="testo"]'))
  .catch(error => { console.error(error); });
</script>
