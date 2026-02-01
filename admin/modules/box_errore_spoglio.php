<?php
// --- SIMULAZIONE ERRORI PER TEST ---
$errors = [
    ['tipo' => 'votanti', 'id' => 0, 'msg' => 'I voti inseriti 60 non corrispondono ai voti validi 70'],
    ['tipo' => 'lista', 'id' => 12, 'msg' => 'I voti della lista 12 non corrispondono al totale validi 50']
];

if(count($errors)):
?>
<div class="alert alert-warning mt-2 shadow-sm border-left-4" style="border-left:4px solid #f39c12; border-radius:4px; background: #fff8e1; padding: 1rem;" role="alert">
    <h5 style="font-weight:600; display:flex; align-items:center;">
        <i class="fas fa-exclamation-triangle" style="color:#f39c12; margin-right:0.5rem;"></i>
        Attenzione! Sono stati rilevati errori:
    </h5>
    <ul class="mb-0" style="margin-left:1.5rem;">
        <?php foreach($errors as $err): ?>
            <li><?= htmlspecialchars($err['msg']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
