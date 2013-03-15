<?php
$label = array('label' => 'Europe (EU27)');
$v = array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
$label = array('label' => 'Europe (EU27)' . mt_rand(0,10000));
$numbers = array(array(1999, $v[0]), array(2000, $v[1]), array(2001, $v[2]), array(2002, $v[3]) );
echo json_encode(array('label' => 'Europe (EU27)' . mt_rand(0,10000), 'data' => $numbers));
die();
