<?php
// blocco_referendum.php

// valori di esempio, puoi sostituirli con quelli reali
$referendum = [
    'si' => $si ?? 0,
    'no' => $no ?? 0,
    'valide' => $valide ?? 0,
    'nulle' => $nulle ?? 0,
    'bianche' => $bianche ?? 0,
    'contestate' => $contestate ?? 0,
    'tot_non_valide' => $tot_non_valide ?? 0
];
?>
<div class="card-header bg-primary text-white">
    <h3 class="card-title text-uppercase mb-0">Consultazione Referendaria</h3>
</div>

    <div class="card-body p-2">
        <table class="table table-bordered table-sm text-center mb-0">
            <thead>
                <tr>
                    <th>Quesito</th>
                    <th>Sì</th>
                    <th>No</th>
                    <th>Voti Validi</th>
                    <th>Schede Nulle</th>
                    <th>Schede Bianche</th>
                    <th>Voti Contestati</th>
                    <th>Tot. Voti non Validi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Quesito Referendario</td>
                    <td><?= number_format($referendum['si']) ?></td>
                    <td><?= number_format($referendum['no']) ?></td>
                    <td><?= number_format($referendum['valide']) ?></td>
                    <td><?= number_format($referendum['nulle']) ?></td>
                    <td><?= number_format($referendum['bianche']) ?></td>
                    <td><?= number_format($referendum['contestate']) ?></td>
                    <td><?= number_format($referendum['tot_non_valide']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
