<?php require_once '../includes/check_access.php'; ?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['informazioni'] = [
    'come_si_vota' => [
        ['id'=>1, 'titolo'=>'Scheda A', 'descrizione'=>'Descrizione A', 'testo'=>'Testo completo A'],
        ['id'=>2, 'titolo'=>'Scheda B', 'descrizione'=>'Descrizione B', 'testo'=>'Testo completo B']
    ],
    'servizi' => [
        ['id'=>3, 'titolo'=>'Trasporto disabili', 'descrizione'=>'Servizio navetta gratuito', 'testo'=>'Prenotazione al numero 080111222'],
        ['id'=>4, 'titolo'=>'Assistenza voto', 'descrizione'=>'Supporto per anziani', 'testo'=>'Contatta il numero verde 800-123456']
    ],
    'numeri_utili' => [
        ['id'=>5, 'titolo'=>'Polizia Municipale', 'descrizione'=>'Emergenze e incidenti', 'testo'=>'0801234567'],
        ['id'=>6, 'titolo'=>'Protezione Civile', 'descrizione'=>'Contatti emergenza', 'testo'=>'0807654321']
    ],
    'link' => [
        ['id'=>7, 'titolo'=>'Sito del Comune', 'descrizione'=>'Vai al sito ufficiale', 'testo'=>'https://comune.it'],
        ['id'=>8, 'titolo'=>'Ministero Interno', 'descrizione'=>'Info voto ufficiali', 'testo'=>'https://interno.gov.it']
    ]
];

$sezioni = ['come_si_vota', 'servizi', 'numeri_utili', 'link'];
$etichette = [
    'come_si_vota' => 'Come si vota',
    'servizi' => 'Servizi',
    'numeri_utili' => 'Numeri utili',
    'link' => 'Link'
];
$tabSelezionato = $_GET['tab'] ?? 'come_si_vota';

// === SIMULAZIONE DATABASE IN SESSIONE ===
if (!isset($_SESSION['informazioni'])) {
    $_SESSION['informazioni'] = [
        'come_si_vota' => [['id'=>1, 'titolo'=>'Scheda A', 'descrizione'=>'Descrizione A', 'testo'=>'Testo completo A']],
        'servizi' => [],
        'numeri_utili' => [['id'=>3, 'titolo'=>'Polizia Municipale', 'descrizione'=>'Emergenze e incidenti', 'testo'=>'0801234567']],
        'link' => [['id'=>2, 'titolo'=>'Sito del Comune', 'testo'=>'https://comune.it', 'descrizione'=>'Vai al sito ufficiale']]
    ];
}
$informazioni = &$_SESSION['informazioni'];

// Funzione per generare nuovo ID unico
function generaId() {
    $max = 0;
    foreach ($_SESSION['informazioni'] as $cat => $arr) {
        foreach ($arr as $el) {
            if ($el['id'] > $max) $max = $el['id'];
        }
    }
    return $max + 1;
}

// Gestione AJAX POST per aggiungi/modifica/elimina
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['azione'])) {
    header('Content-Type: application/json');

    $azione = $_POST['azione'];
    $categoria = $_POST['categoria'] ?? null;
    if (!in_array($categoria, $sezioni)) {
        echo json_encode(['success'=>false, 'msg'=>'Categoria non valida']);
        exit;
    }

    if ($azione === 'aggiungi') {
        $titolo = trim($_POST['titolo'] ?? '');
        $descrizione = trim($_POST['descrizione'] ?? '');
        $testo = trim($_POST['testo'] ?? '');

        if ($titolo === '' || $testo === '') {
            echo json_encode(['success'=>false, 'msg'=>'Titolo e testo obbligatori']);
            exit;
        }

        $nuovoId = generaId();
        $elemento = [
            'id' => $nuovoId,
            'titolo' => $titolo,
            'descrizione' => $descrizione,
            'testo' => $testo
        ];

        $informazioni[$categoria][] = $elemento;

        // COMMENTA/DECOMMENTA QUI PER DB MYSQL
        /*
        $conn = new mysqli("localhost", "user", "password", "eleonline");
        $c = $conn->real_escape_string($categoria);
        $t = $conn->real_escape_string($titolo);
        $d = $conn->real_escape_string($descrizione);
        $tx = $conn->real_escape_string($testo);
        $conn->query("INSERT INTO informazioni (categoria, titolo, descrizione, testo) VALUES ('$c','$t','$d','$tx')");
        $nuovoId = $conn->insert_id;
        $conn->close();
        */

        echo json_encode(['success'=>true, 'elemento'=>$elemento]);
        exit;
    }
    elseif ($azione === 'modifica') {
        $id = intval($_POST['id'] ?? 0);
        $titolo = trim($_POST['titolo'] ?? '');
        $descrizione = trim($_POST['descrizione'] ?? '');
        $testo = trim($_POST['testo'] ?? '');

        if ($id <= 0 || $titolo === '' || $testo === '') {
            echo json_encode(['success'=>false, 'msg'=>'Dati non validi']);
            exit;
        }

        $trovato = false;
        foreach ($informazioni[$categoria] as &$el) {
            if ($el['id'] == $id) {
                $el['titolo'] = $titolo;
                $el['descrizione'] = $descrizione;
                $el['testo'] = $testo;
                $trovato = true;
                break;
            }
        }
        unset($el);

        if (!$trovato) {
            echo json_encode(['success'=>false, 'msg'=>'Elemento non trovato']);
            exit;
        }

        // COMMENTA/DECOMMENTA QUI PER DB MYSQL
        /*
        $conn = new mysqli("localhost", "user", "password", "eleonline");
        $id_sql = intval($id);
        $c = $conn->real_escape_string($categoria);
        $t = $conn->real_escape_string($titolo);
        $d = $conn->real_escape_string($descrizione);
        $tx = $conn->real_escape_string($testo);
        $conn->query("UPDATE informazioni SET titolo='$t', descrizione='$d', testo='$tx' WHERE id=$id_sql AND categoria='$c'");
        $conn->close();
        */

        echo json_encode(['success'=>true]);
        exit;
    }
    elseif ($azione === 'elimina') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success'=>false, 'msg'=>'ID non valido']);
            exit;
        }

        $ind = null;
        foreach ($informazioni[$categoria] as $k => $el) {
            if ($el['id'] == $id) {
                $ind = $k;
                break;
            }
        }

        if ($ind === null) {
            echo json_encode(['success'=>false, 'msg'=>'Elemento non trovato']);
            exit;
        }

        array_splice($informazioni[$categoria], $ind, 1);

        // COMMENTA/DECOMMENTA QUI PER DB MYSQL
        /*
        $conn = new mysqli("localhost", "user", "password", "eleonline");
        $id_sql = intval($id);
        $c = $conn->real_escape_string($categoria);
        $conn->query("DELETE FROM informazioni WHERE id=$id_sql AND categoria='$c'");
        $conn->close();
        */

        echo json_encode(['success'=>true]);
        exit;
    }
}

?>
<style>
.ck-editor__editable_inline {
  min-height: 400px; /* o quello che vuoi */
}
</style>
<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cogs me-2"></i>Gestione Informazioni</h3>
      </div>
      <div class="card-body">

        <ul class="nav nav-tabs" id="infoTab" role="tablist">
          <?php foreach ($sezioni as $i => $s): ?>
            <li class="nav-item">
              <a class="nav-link <?= $i==0 ? 'active' : '' ?>" data-toggle="tab" href="#<?= $s ?>" role="tab" aria-controls="<?= $s ?>" aria-selected="<?= $i==0 ? 'true' : 'false' ?>">
                <?= $etichette[$s] ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="tab-content mt-4">
          <?php foreach ($sezioni as $i => $s): ?>
            <div class="tab-pane fade show <?= $i==0 ? 'active' : '' ?>" id="<?= $s ?>" role="tabpanel">
              <form id="form-<?= $s ?>" data-categoria="<?= $s ?>">
                <input type="hidden" name="id" value="">
                <input type="hidden" name="categoria" value="<?= $s ?>">

                <?php if ($s === 'link'): ?>
                  <div class="form-group">
                    <label for="titolo_<?= $s ?>">Titolo</label>
                    <input type="text" name="titolo" id="titolo_<?= $s ?>" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label for="testo_<?= $s ?>">URL del link</label>
                    <input type="url" name="testo" id="testo_<?= $s ?>" class="form-control" placeholder="https://..." required>
                  </div>
                  <div class="form-group">
                    <label for="descrizione_<?= $s ?>">Descrizione</label>
                    <textarea name="descrizione" rows="3" id="descrizione_<?= $s ?>" class="form-control"></textarea>
                  </div>
                <?php else: ?>
                  <div class="form-group">
                    <label for="titolo_<?= $s ?>">Titolo</label>
                    <input type="text" name="titolo" id="titolo_<?= $s ?>" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label for="descrizione_<?= $s ?>">Descrizione</label>
                    <textarea name="descrizione" rows="3" id="descrizione_<?= $s ?>" class="form-control"></textarea>
                  </div>
                  <div class="form-group">
                    <label for="testo_<?= $s ?>">Testo</label>
                    <textarea name="testo" id="testo_<?= $s ?>" rows="20" class="form-control" required></textarea>
                  </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary mt-2 btn-save">Salva</button>
                <button type="button" class="btn btn-secondary mt-2 btn-cancel">Annulla</button>
              </form>

              <hr>

              <h5>Elenco <?= $etichette[$s] ?></h5>
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Titolo</th>
                    <th>Descrizione</th>
                    <th><?= $s === 'link' ? 'URL' : 'Testo' ?></th>
                    <th>Azioni</th>
                  </tr>
                </thead>
                <tbody id="lista-<?= $s ?>">
                  <?php foreach ($informazioni[$s] as $elem): ?>
                    <tr data-id="<?= $elem['id'] ?>">
                      <td class="titolo"><?= htmlspecialchars($elem['titolo']) ?></td>
                      <td class="descrizione"><?= htmlspecialchars($elem['descrizione']) ?></td>
                      <td class="testo">
                        <?php if ($s === 'link'): ?>
                          <a href="<?= htmlspecialchars($elem['testo']) ?>" target="_blank"><?= htmlspecialchars($elem['testo']) ?></a>
                        <?php else: ?>
                          <?= nl2br(htmlspecialchars($elem['testo'])) ?>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-info btn-edit">Modifica</button>
                        <button class="btn btn-sm btn-danger btn-delete">Elimina</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CKEditor 5 Classic da CDN -->
<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.0/build/ckeditor.js"></script>

<script>
const sezioni = <?= json_encode($sezioni) ?>;
let editorInstances = {};

sezioni.forEach(cat => {
  if(cat !== 'link') {
    const textarea = document.querySelector(`#form-${cat} textarea[name="testo"]`);
    if(textarea) {
      ClassicEditor.create(textarea, {
        toolbar: {
          items: [
            'heading', '|',
            'bold', 'italic', 'underline', 'strikethrough', 'code', 'codeBlock', '|',
            'subscript', 'superscript', '|',
            'link', 'blockQuote', '|',
            'bulletedList', 'numberedList', 'todoList', '|',
            'outdent', 'indent', '|',
            'alignment', '|',
            'insertTable', 'tableColumn', 'tableRow', 'mergeTableCells', '|',
            'imageUpload', 'mediaEmbed', '|',
            'undo', 'redo', 'removeFormat'
          ]
        },
        language: 'it',
        image: {
          toolbar: [
            'imageTextAlternative', 'imageStyle:full', 'imageStyle:side'
          ]
        },
        table: {
          contentToolbar: [
            'tableColumn', 'tableRow', 'mergeTableCells'
          ]
        },
        simpleUpload: {
          uploadUrl: '/path/to/your/image/upload/endpoint',
          headers: {
            'X-CSRF-TOKEN': 'CSRF-Token-Here' // se usi token CSRF
          }
        },
        alignment: {
          options: [ 'left', 'center', 'right', 'justify' ]
        }
      })
      .then(editor => {
        editorInstances[cat] = editor;
      })
      .catch(error => {
        console.error(error);
      });
    }
  }
});


  // Funzione per caricare i dati nel form
  function caricaForm(categoria, dati) {
    const form = document.querySelector(`#form-${categoria}`);
    form.querySelector('input[name="id"]').value = dati.id || '';
    form.querySelector('input[name="titolo"]').value = dati.titolo || '';
    form.querySelector('input[name="descrizione"]').value = dati.descrizione || '';

    if (categoria === 'link') {
      form.querySelector('input[name="testo"]').value = dati.testo || '';
    } else {
      if (editorInstances[categoria]) {
        editorInstances[categoria].setData(dati.testo || '');
      } else {
        form.querySelector('textarea[name="testo"]').value = dati.testo || '';
      }
    }

    // Attiva il tab corretto e scrolla al form
    const tabTrigger = document.querySelector(`#tab-${categoria}-tab`);
    if (tabTrigger) new bootstrap.Tab(tabTrigger).show();

    setTimeout(() => {
      document.querySelector(`#form-${categoria}`).scrollIntoView({ behavior: 'smooth' });
    }, 300);
  }

  // Pulizia form
  function resetForm(categoria) {
    caricaForm(categoria, { id: '', titolo: '', descrizione: '', testo: '' });
  }

  // Gestione submit, reset, edit, delete
  sezioni.forEach(categoria => {
    const form = document.querySelector(`#form-${categoria}`);
    const lista = document.querySelector(`#lista-${categoria}`);

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const id = form.querySelector('input[name="id"]').value;
      const titolo = form.querySelector('input[name="titolo"]').value.trim();
      const descrizione = form.querySelector('input[name="descrizione"]').value.trim();
      let testo;

      if (categoria === 'link') {
        testo = form.querySelector('input[name="testo"]').value.trim();
      } else {
        testo = editorInstances[categoria].getData().trim();
      }

      if (!titolo || !testo) {
        alert('Titolo e testo sono obbligatori');
        return;
      }

      const azione = id ? 'modifica' : 'aggiungi';
      const data = new FormData();
      data.append('azione', azione);
      data.append('categoria', categoria);
      data.append('titolo', titolo);
      data.append('descrizione', descrizione);
      data.append('testo', testo);
      if (id) data.append('id', id);

      fetch('', { method: 'POST', body: data })
        .then(res => res.json())
        .then(res => {
          if (res.success) {
            if (azione === 'aggiungi') {
              aggiungiRiga(categoria, res.elemento);
            } else {
              aggiornaRiga(categoria, { id: parseInt(id), titolo, descrizione, testo });
            }
            resetForm(categoria);
          } else {
            alert(res.msg || 'Errore durante il salvataggio');
          }
        })
        .catch(() => alert('Errore di comunicazione'));
    });

    form.querySelector('.btn-cancel').addEventListener('click', e => {
      e.preventDefault();
      resetForm(categoria);
    });

    lista.addEventListener('click', e => {
      if (e.target.classList.contains('btn-edit')) {
        const tr = e.target.closest('tr');
        const id = tr.getAttribute('data-id');
        const titolo = tr.querySelector('.titolo').textContent;
        const descrizione = tr.querySelector('.descrizione').textContent;
        let testo;

        if (categoria === 'link') {
          testo = tr.querySelector('.testo a').textContent;
        } else {
          testo = tr.querySelector('.testo').innerHTML
            .replace(/<br\s*\/?>/gi, "\n")
            .replace(/&nbsp;/g, ' ')
            .replace(/&amp;/g, '&')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>');
        }

        caricaForm(categoria, { id, titolo, descrizione, testo });
      }

      if (e.target.classList.contains('btn-delete')) {
        if (confirm('Sei sicuro di voler eliminare questo elemento?')) {
          const tr = e.target.closest('tr');
          const id = tr.getAttribute('data-id');
          const data = new FormData();
          data.append('azione', 'elimina');
          data.append('categoria', categoria);
          data.append('id', id);

          fetch('', { method: 'POST', body: data })
            .then(res => res.json())
            .then(res => {
              if (res.success) {
                tr.remove();
                resetForm(categoria);
              } else {
                alert(res.msg || 'Errore durante la cancellazione');
              }
            })
            .catch(() => alert('Errore di comunicazione'));
        }
      }
    });
  });

  function aggiungiRiga(categoria, elem) {
    const tbody = document.querySelector(`#lista-${categoria}`);
    const tr = document.createElement('tr');
    tr.setAttribute('data-id', elem.id);

    tr.innerHTML = `
      <td class="titolo">${escapeHtml(elem.titolo)}</td>
      <td class="descrizione">${escapeHtml(elem.descrizione)}</td>
      <td class="testo">
        ${categoria === 'link' 
          ? `<a href="${escapeHtml(elem.testo)}" target="_blank">${escapeHtml(elem.testo)}</a>` 
          : nl2br(escapeHtml(elem.testo))
        }
      </td>
      <td>
        <button class="btn btn-sm btn-info btn-edit">Modifica</button>
        <button class="btn btn-sm btn-danger btn-delete">Elimina</button>
      </td>
    `;

    tbody.appendChild(tr);
  }

  function aggiornaRiga(categoria, elem) {
    const tr = document.querySelector(`#lista-${categoria} tr[data-id="${elem.id}"]`);
    if (!tr) return;

    tr.querySelector('.titolo').textContent = elem.titolo;
    tr.querySelector('.descrizione').textContent = elem.descrizione;
    if (categoria === 'link') {
      tr.querySelector('.testo').innerHTML = `<a href="${escapeHtml(elem.testo)}" target="_blank">${escapeHtml(elem.testo)}</a>`;
    } else {
      tr.querySelector('.testo').innerHTML = nl2br(escapeHtml(elem.testo));
    }
  }

  function escapeHtml(text) {
    return text.replace(/&/g, "&amp;")
               .replace(/</g, "&lt;")
               .replace(/>/g, "&gt;")
               .replace(/"/g, "&quot;")
               .replace(/'/g, "&#039;");
  }

  function nl2br(str) {
    return str.replace(/\n/g, '<br>');
  }
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', function () {
        const tr = this.closest('tr');
        const categoria = tr.closest('tbody').id.replace('lista-', '');
        const id = tr.dataset.id;
        const titolo = tr.querySelector('.titolo').textContent.trim();
        const descrizione = tr.querySelector('.descrizione').textContent.trim();
        const testoRaw = tr.querySelector('.testo').textContent.trim();
        const form = document.querySelector(`#form-${categoria}`);

        // Cambia tab
        const tabLink = document.querySelector(`a[href="#${categoria}"]`);
        if (tabLink) {
          new bootstrap.Tab(tabLink).show();
        }

        setTimeout(() => {
          // Scrolla al form
          form.scrollIntoView({ behavior: 'smooth', block: 'start' });

          form.querySelector('input[name="id"]').value = id;
          form.querySelector('input[name="titolo"]').value = titolo;
          if (form.querySelector('textarea[name="descrizione"]')) {
            form.querySelector('textarea[name="descrizione"]').value = descrizione;
          }

          if (categoria === 'link') {
            form.querySelector('input[name="testo"]').value = tr.querySelector('.testo a')?.textContent.trim() || '';
          } else {
            if (editorInstances[categoria]) {
              editorInstances[categoria].setData(testoRaw);
            } else {
              form.querySelector('textarea[name="testo"]').value = testoRaw;
            }
          }
        }, 300);
      });
    });
  });
</script>
