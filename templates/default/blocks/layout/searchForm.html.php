<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$placeholder = lang($config['caption']);
$value       = '';
if (!empty($_SESSION['currSearch']))
{
	$placeholder = $_SESSION['currSearch'];
	$value       = $_SESSION['currSearch'];
}
?>

<form method="post" class="form-inline" id="block_search<?php echo $block->id ?>" action="" role="form">
	<input type="text" name="keyword" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder;?>" >
	<button class="f-p-bg-color tran3s" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
</form>

<script type="text/javascript">
	_Bbc(function($){
		$("#block_search<?php echo $block->id ?>").submit(function(e){
			e.preventDefault();
			var a = $('input[name="keyword"]');
			if (a.val()=="") {
				alert("<?php echo lang('Please Insert Keyword!'); ?>");
				a.focus();
			}else{
				var b = _URL+'search.htm';
				var c = encodeURIComponent(a.val());
				if (c.length>12) {
					b += '?id=';
				}else{
					b += '/';
				}
				b += c;
				document.location.href = b;
			}
		})
	});
</script>