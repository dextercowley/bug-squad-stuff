<?php
$label = array('label' => 'Europe (EU27)');
$numbers = array(array(1999, 3.0), array(2000, 3.9), array(2001, 2.0), array(2002, 1.2) );
echo json_encode(array('label' => 'Europe (EU27)', 'data' => $numbers));
die;
