<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');


$page  = @intval($_GET['page']);
$limit = 8;
$start = $page*$limit;
$query = "SELECT * FROM `bin_testimonial` WHERE `publish`=1 ORDER BY id DESC LIMIT {$start}, {$limit}";
$datas = $db->getAll($query);
$total = $db->getOne("SELECT COUNT(*) FROM `bin_testimonial` WHERE `publish`=1");
include tpl('testimonial.html.php');