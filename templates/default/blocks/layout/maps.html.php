<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$cfg     = get_config('village','village');
$latlong = $cfg['latlong'];
$pos     = explode(',', $latlong);
$address = $cfg['address'];

?>
<div class="map-canvas" id="peta_desa" data-lat="<?php echo $pos[0] ?>" data-long="<?php echo $pos[1] ?>" data-detail="<?php echo $address ?>"></div>
<script type="text/javascript">
	_Bbc(function($){
		$(function(){var a=[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}];var b=new google.maps.Map($('.map-canvas')[0],{zoom:16,styles:a,scrollwheel:false,center:new google.maps.LatLng($("#peta_desa").data("lat"),$("#peta_desa").data("long"))});var c=new google.maps.Marker({map:b,position:new google.maps.LatLng($("#peta_desa").data("lat"),$("#peta_desa").data("long"))});var d=new SnazzyInfoWindow({marker:c,content:'<p>'+$("#peta_desa").data('detail')+'</p>',closeOnMapClick:false});d.open()});
	});
</script>
<?php
// $sys->link_js($sys->template_url.'js/map-script.js', false);
