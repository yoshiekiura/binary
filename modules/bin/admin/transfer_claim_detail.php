<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id   = @intval($_GET['id']);
$data = $db->getRow("SELECT * FROM `bin_claim` WHERE `id`={$id}");
if (!empty($data))
{
	$_GET['id'] = $data['bin_id'];
	include 'list_detail.php';
}