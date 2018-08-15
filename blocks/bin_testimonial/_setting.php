<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'tag_type' => array(
		'text'   => 'Tampilkan testimoni yang..',
		'type'   => 'select',
		'option' => array(1 => 'Terbaru', 2 => 'Terlama', 3 => 'Acak')
		),
	'limit'    => array(
		'text'    => 'Batasi jumlah maximal tesimoni yang tampil',
		'type'    => 'text',
		'default' => '5',
		'add'     => 'item'
		)
	);