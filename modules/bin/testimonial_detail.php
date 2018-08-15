<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id   = @intval($_GET['id']);
$q    = "SELECT * FROM `bin_testimonial` WHERE `id`={$id} AND `publish`=1";
$data = $db->getRow($q);
include tpl('testimonial_detail.html.php');