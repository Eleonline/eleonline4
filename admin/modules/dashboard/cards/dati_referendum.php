<?php
// DATI DEMO (sostituisci con query reali)

$quesiti = [
  [
    'titolo' => 'Quesito 1 - Abrogazione articolo X',
    'si' => rand(1400,1700),
    'no' => rand(600,900)
  ],
  [
    'titolo' => 'Quesito 2 - Modifica legge Y',
    'si' => rand(2000,2400),
    'no' => rand(1000,1400)
  ]
];

foreach($quesiti as $q){

  $tot = $q['si'] + $q['no'];
  $p_si = $tot ? ($q['si'] * 100 / $tot) : 0;
  $p_no = $tot ? ($q['no'] * 100 / $tot) : 0;

  echo '
  <div class="mb-3 p-2 border rounded">

    <b>'.htmlspecialchars($q['titolo']).'</b>

    <div class="row text-center mt-2">

      <div class="col-6"><b>SÌ</b></div>
      <div class="col-6"><b>NO</b></div>

      <div class="col-6">'.number_format($q['si']).'</div>
      <div class="col-6">'.number_format($q['no']).'</div>

      <div class="col-6 text-success">'.number_format($p_si,1).'%</div>
      <div class="col-6 text-danger">'.number_format($p_no,1).'%</div>

    </div>

  </div>';
}
