<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Ini adalah module untuk menampilkan fitur2 yang ada di MLM Binary
switch( $Bbc->mod['task'] )
{
	case 'main' :
	case 'register': // Menu untuk registrasi member
		include 'register.php';
		break;

	case 'dashboard': // halaman pertama ringkasan laporan keseluruhan dari member. biasa nya digunakan untuk halaman pertama setelah login
		include 'dashboard.php';
		break;

	case 'profile': // melihat detail profile dari member itu sendiri
		include 'profile.php';
		break;
	case 'profile_edit': // untuk mengedit profile dari member
		include 'profile_edit.php';
		break;

	case 'network': // melihat genealogy jaringan
	case 'network_genealogy': // menu alias dari task 'network'
		include 'network_genealogy.php';
		break;
	case 'network_downline': // melihat data downline
		include 'network_downline.php';
		break;
	case 'network_sponsor': // melihat data member yang telah di sponsori
		include 'network_sponsor.php';
		break;

	case 'bonus': // melihat rangkuman bonus
	case 'bonus_status': // menu alias dari task 'bonus'
		include 'bonus_status.php';
		break;
	case 'bonus_status_withdraw':
		include 'bonus_status_withdraw.php';
		break;
	case 'bonus_monthly': // data bonus bulanan
		include 'bonus_monthly.php';
		break;
	case 'bonus_history': // daftar bonus apa aja yang pernah didapat
		include 'bonus_history.php';
		break;
	case 'bonus_transfer': // data hostory pembayaran transfer yang sudah pernah diterima
		include 'bonus_transfer.php';
		break;

	case 'reward': // melihat data reward yang tersedia dan di sini lah user bisa klaim jika reward tidak dibuat otomatis
	case 'reward_list': // menu alias dari task 'reward'
		include 'reward_list.php';
		break;
	case 'reward_list_detail':
		include 'reward_list_detail.php';
		break;
	case 'reward_history': // sejarah reward apa saja yang pernah di dapatkan
		include 'reward_history.php';
		break;

	case 'message': // fitur pengiriman pesan beserta inbox antar user selama masih dalam satu jaringan
		include 'message.php';
		break;

	case 'message_compose':
	  include 'message_compose.php';
	  break;

	case 'action':
	  include 'action.php';
		break;

	case 'testimoni': // menu untuk menambah testimoni
		include 'testimoni.php';
		break;

	case 'testimoni_edit':
		include 'testimoni_edit.php';
		break;

	case 'testimoni_image':
		include 'testimoni_image.php';
		break;

	case 'testimonial': // menu untuk menampilkan testimoni dari member
		include 'testimonial.php';
		break;

	case 'testimonial_detail':
		include 'testimonial_detail.php';
		break;

	case 'fetch':
		include 'fetch.php';
		break;
	case 'fix':
		include 'fix.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}
