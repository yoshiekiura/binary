<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Menampilkan daftar testimoni dari member MLM, bs diurutkan berdasarkan terbaru, terlama atau terpilih

$limit = intval($config['limit']);
switch (@$config['tag_type'])
{
	case 1:
		$ordby = '`id` DESC';
		break;
	case 2:
		$ordby = '`id` ASC';
		break;

	default:
		$ordby = 'RAND()';
		break;
}
$q = "SELECT * FROM `bin_testimonial` WHERE `publish`=1 ORDER BY {$ordby} LIMIT {$limit}";
$r_data = $db->getAll($q);
if (empty($r_data))
{
	$r_data = json_decode(file_read(__DIR__.'/sample.json'), 1);
}
include tpl(@$config['template'].'.html.php', 'default.html.php');
