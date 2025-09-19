<?php
xmlwriter_start_element($body, 'com:executionPlanInput');
xmlwriter_start_attribute($body, 'xmlns:com');
xmlwriter_text($body, $com_namespace);
xmlwriter_end_attribute($body); // com
xmlwriter_start_element($body, 'com:comando');
xmlwriter_text($body, '?');
xmlwriter_end_element($body); // com:comando
xmlwriter_start_element($body, 'com:parametri');
xmlwriter_start_element($body, 'com:nome');
xmlwriter_text($body, '?');
xmlwriter_end_element($body); // com:nome
xmlwriter_start_element($body, 'com:valore');
xmlwriter_text($body, '?');
xmlwriter_end_element($body); // com:valore
xmlwriter_end_element($body); // com:parametri
xmlwriter_end_element($body); // com:executionPlanInput
?>