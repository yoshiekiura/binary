<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module untuk management MLM tipe binary
switch( $Bbc->mod['task'] )
{
	case 'main': // daftar semua member dalam jaringan
	case 'list': // alias dari task "main"
		include 'list.php';
		break;
	case 'list_detail':
		include 'list_detail.php';
		break;
	case 'list_edit':
		include 'list_edit.php';
		break;
	case 'list_password':
		include 'list_password.php';
		break;
	case 'list_signin':
		include 'list_signin.php';
		break;

	case 'genealogy': // tampilan jaringan downline per member ber bentuk genealogy tiap member
		include 'genealogy.php';
		break;

	case 'bonus': // Data saldo baik itu bonus maupun pembayaran yang telah diterima dalam 3 bulan terakhir
		include 'bonus.php';
		break;

	case 'expense': // Menu untuk memasukkan data pengeluaran baik untuk operasional maupun support (tidak termasuk transfer bonus)
		include 'expense.php';
		break;

	case 'transfer': // Daftar Pembayaran bonus, yang bisa di download dan nanti akan diupload kembali untuk memberika laporan mana saja yang sudah di eksekusi. Format file excel nya sama persis seperti format hasil excel yang di download
	case 'transfer_list': // alias dari task "transfer"
		include 'transfer_list.php';
		break;
	case 'transfer_history': // History transfer yang telah dilakukan oleh perusahaan 3 bulan terakhir
		include 'transfer_history.php';
		break;
	case 'transfer_claim': // Menu untuk mengklaim bonus member untuk masuk kedalam finance perusahaan (untuk member di titik asset perusahaan)
		include 'transfer_claim.php';
		break;
	case 'transfer_claim_detail':
		include 'transfer_claim_detail.php';
		break;

	case 'reward': // daftar member yang berhak mendapatkan bonus, baik itu yang harus di klaim maupun yang otomatis diterima (tergantung settingan di market plan)
	case 'reward_list': // alias dari task "reward"
		include 'reward_list.php';
		break;
	case 'reward_list_detail':
		include 'reward_list_detail.php';
		break;
	case 'reward_potential': // Melihat daftar member yang berpotensi mendapatkan reward jika belum mengklaim (apabila auto reward tidak kondisi aktif)
		include 'reward_potential.php';
		break;

	case 'report': // laporan data member
	case 'report_member': // alias dari task "report"
		include 'report_member.php';
		break;
	case 'report_serial': // laporan untuk melihat semua serial beserta status nya (terpakai/aktif)
		include 'report_serial.php';
		break;
	case 'report_bonus': // laporan history total bonus bulanan berdasarkan tipe ( titik, sponsor, pasangan )
		include 'report_bonus.php';
		break;
	case 'report_transfer': // rekapitulasi bonus yang telah ditransfer serta laporan transfer yang dilakukan dalam hitungan yang telah ditentukan oleh marketplan
		include 'report_transfer.php';
		break;
	case 'report_activation': // laporan jumlah serial yang telah di aktifkan atau digunakan oleh member saat registrasi berdasarkan tanggal
		include 'report_activation.php';
		break;
	case 'report_reward': // laporan member mana saja yang telah mendapatkan reward
		include 'report_reward.php';
		break;
	case 'report_finance': // laporan total keuangan secara global sejak MLM di setup pertama kali
		include 'report_finance.php';
		break;
	case 'report_finance_monthly': // laporan keuangan berdasarkan bulan
		include 'report_finance_monthly.php';
		break;
	case 'report_finance_yearly': // laporan keuangan berdasarkan tahun
		include 'report_finance_yearly.php';
		break;

	case 'serial': // daftar serial yang sudah diaktifkan baik sudah terpakai maupun tidak
	case 'serial_list': // alias dari task "serial"
		include 'serial_list.php';
		break;
	case 'serial_activate': // daftar serial yg BELUM diaktifkan, nanti di bawah ada tombol untuk Activate All
		include 'serial_activate.php';
		break;

	case 'testimonial':  // daftar testimonial dari user, dimana admin bisa mempublish ataupun tidak mempublish testimonial tersebut
		include 'testimonial.php';
		break;
	case 'testimonial_edit':
		include 'testimonial_edit.php';
		break;
	case 'testimonial_detail':
		include 'testimonial_detail.php';
		break;

	case 'config': // menu konfigurasi untuk module binary MLM
	case 'config_marketplan': // ringkasan marketplan yang telah dibuat oleh admin
		$config = config('plan_a');
		include 'config_marketplan.php';
		break;
	case 'config_reward': // daftar reward yang bisa diterima oleh member beserta persyaratan yang ada
		include 'config_reward.php';
		break;
	case 'config_reward_edit': // daftar reward yang bisa diterima oleh member beserta persyaratan yang ada
		include 'config_reward_edit.php';
		break;
	case 'config_level': // daftar level peringkat member
		include 'config_level.php';
		break;
	case 'config_serialtype': // daftar tipe serial
		include 'config_serialtype.php';
		break;
	case 'config_balance': // daftar status message yang akan di masukkan ke dalam ewallet
		include 'config_balance.php';
		break;
	case 'config_limitation': // konfigurasi batas maksimum registrasi untuk tiap member berdasarkan data yang dimasukkan
		include 'config_limitation.php';
		break;
	case 'config_setup': // Menu untuk me-reset atau membuat marketplan serta menghapus semua data member yang sudah registrasi
			include 'config_setup.php';
			break;
	case 'config_marketplan_trial':
			include 'config_marketplan_trial.php';
			break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}
