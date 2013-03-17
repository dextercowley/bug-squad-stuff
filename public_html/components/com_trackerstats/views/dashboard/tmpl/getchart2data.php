<?php
$s1 = array(200,600,700,1000);
$s2 = array(460, 210, 690, 820);
$s3 = array(260, 440, 320, 200);
$data = array($s1,$s2,$s3);
$ticks = array('Jan', 'Feb', 'Mar', 'Apr');
$label1 = new stdClass();
$label2 = new stdClass();
$label3 = new stdClass();
$label1->label = 'Hotel';
$label2->label = 'Event Registration';
$label3->label = 'Airfare';
$labels = array($label1, $label2, $label3);
// assemble array
echo json_encode(array($data, $ticks, $labels));
die();
