<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$config = config('plan_a');
link_js(_LIB.'pea/includes/formIsRequire.js');
link_js(_LIB.'pea/includes/FormTags.js');
link_js('config_setup-form.js');
$default_in = 'form';

if (!empty($_POST['submit']))
{
	$post = array();
	$msg = 'Gagal memverifikasi marketplan anda';
	if ($_POST['submit']=='form')
	{
		$default_in = 'form';
		unset($_POST['submit']);
		$post = $_POST;
		$boolean_field = array(
      'serial_use',
      'serial_check',
      'is_withdraw',
      'bonus_node_ok',
      'bonus_node_gen',
      'bonus_gen_node_ok',
      'bonus_sponsor_ok',
      'bonus_sponsor_gen',
      'bonus_pair_ok',
      'serial_flushout_ok',
      'flushwait',
      'bonus_pair_gen',
      'reward_use',
      'reward_auto'
    );
		foreach ($boolean_field as $field)
		{
			if (!isset($post[$field]))
			{
				$post[$field] = 0;
			}
		}
		if (empty($post['reward_use']))
		{
      $post['reward_list'] = array();
      $post['reward_auto'] = 0;
		}
		if (empty($post['level_list']))
		{
			$post['level_list'] = array('Member');
		}
		if (empty($post['serial_use']))
		{
			$post['serial_list'] = array('Reguler');
		}
		$r = array('bonus_node', 'bonus_sponsor', 'bonus_pair');
		foreach ($r as $f)
		{
			if (empty($post[$f.'_ok']))
			{
				$post[$f] = array();
			}else{
				if (!empty($post[$f.'_gen']))
				{
					$i=1;
					while (!empty($post[$f.'_gen_'.$i]))
					{
						$post[$f][] = $post[$f.'_gen_'.$i];
						unset($post[$f.'_gen_'.$i]);
						$i++;
					}
				}
			}
			unset($post[$f.'_ok'], $post[$f.'_gen']);
		}
    // Custom bonus generasi titik
    $f        = 'bonus_gen_node';
    $post[$f] = array();
    if (!empty($post['bonus_gen_node_ok']))
    {
      $i        = 1;
      $post[$f] = array(0);
      while (!empty($post[$f.'_gen_'.$i]))
      {
        $post[$f][] = $post[$f.'_gen_'.$i];
        unset($post[$f.'_gen_'.$i]);
        $i++;
      }
    }
    $post = array(
      'plan_a'  => $post,
      'reward'  => array(),
      'level'   => array(),
      'balance' => array(),
      );
	}else
	if (is_uploaded_file($_FILES['myconfig']['tmp_name']))
	{
		$default_in = 'file';
		if (preg_match('~\.json$~is', $_FILES['myconfig']['name']))
		{
      if (move_uploaded_file($_FILES['myconfig']['tmp_name'], _CACHE.'ok.json'))
      {
        $post = json_decode(file_read(_CACHE.'ok.json'), 1);
				unlink(_CACHE.'ok.json');
			}else{
				$msg = 'Gagal menyimpan file config anda';
			}
		}else{
			$msg = 'Mohon upload file config anda dengan format .json';
		}
	}

	if (!empty($post) && file_write(_CACHE.'marketplan.json', json_encode(bin_marketplan_validate($post), JSON_PRETTY_PRINT)))
	{
		redirect('index.php?mod=bin.config_setup&type=validation');
	}else{
    if (!empty($Bbc->error_marketplan))
    {
      $msg = $Bbc->error_marketplan;
    }
		echo msg($msg, 'danger');
	}
}
?>
<div class="panel-group" id="setupform" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingfile">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#setupform" href="#setupfile" aria-expanded="false" aria-controls="setupfile">
          Setup Market Plan dari file config sebelumnya
        </a>
      </h4>
    </div>
    <div id="setupfile" class="panel-collapse collapse<?php echo ($default_in == 'file'? ' in' : '');?>" role="tabpanel" aria-labelledby="headingfile">
      <form action="" method="post" enctype="multipart/form-data" role="form" class="formIsRequire">
        <div class="panel-body">
          <div class="form-group">
            <label>File Config</label>
            <input type="file" name="myconfig" class="form-control" placeholder="Upload Config" req="any true" />
            <div class="help-block">Jika anda sudah memiliki file config sebelumnya maka anda bisa upload di sini, sehingga anda tidak perlu memasukkan kembali config anda satu per satu pada form di bawah</div>
          </div>
        </div>
        <div class="panel-footer">
          <span type="button" class="btn btn-default" onclick="document.location.href='index.php?mod=bin.config_setup'"><span class="glyphicon glyphicon-chevron-left"></span></span>
          <button type="submit" class="btn btn-primary" name="submit" value="file">Submit <span class="glyphicon glyphicon-chevron-right"></span></button>
        </div>
      </form>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingmanual">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#setupform" href="#setupmanual" aria-expanded="true" aria-controls="setupmanual">
          Setup Market Plan Manual
        </a>
      </h4>
    </div>
    <div id="setupmanual" class="panel-collapse collapse<?php echo ($default_in == 'form'? ' in' : '');?>" role="tabpanel" aria-labelledby="headingmanual">
      <form action="" method="post" enctype="multipart/form-data" role="form" class="formIsRequire">
        <div class="panel-body">
          <div class="form-group">
            <label>User Group yang akan diberlakukan sebagai Member MLM</label>
            <?php
            $r = $db->getAll("SELECT `id`, `name` FROM `bbc_user_group` WHERE `is_admin`=0 ORDER BY `score` DESC");
            ?>
            <select name="group_id" class="form-control"><?php echo createOption($r); ?></select>
            <div class="help-block">Ini hanya untuk menentukan di <a href="index.php?mod=_cpanel.group" rel="admin_link">User Group</a> mana member MLM akan dimasukkan agar dapat ditentukan privilege menu apa saja yang bisa diakses</div>
          </div>
          <div class="form-group">
            <label>Prefix Serial</label>
            <input type="text" name="prefix" class="form-control" placeholder="Prefix Serial" req="any true" />
            <div class="help-block">
              Ini adalah prefix atau awalan yang akan digunakan untuk setiap kartu serial bagi para calon member. Contoh jika perusahaan anda bernama "Maju Bersama" maka anda bisa memasukkan "MB" maka serial akan berbentuk: MB000001, MB000002, MB000003, dst.
            </div>
          </div>
          <div class="form-group">
            <label>Registrasi</label>
            <input id="serial_option2" type="text" name="price" class="form-control" placeholder="Biaya Registrasi" req="number true" />
            <div class="clearfix"></div>
            <label>
              <input type="checkbox" class="toggle" target="#serial_option" name="serial_use" id="serial_use" value="1" />
              Gunakan tipe serial lebih dari satu tipe
            </label>
            <div id="serial_option">
              <div class="serial_type"></div>
              <div class="form-inline">
                <input type="text" id="serial_name" class="form-control nochange" placeholder="nama serial" req="any false" />
                <input type="text" id="serial_price" class="form-control nochange" placeholder="biaya registrasi" req="number false" />
                <input type="text" id="serial_flushout" class="form-control nochange serial_flushout" placeholder="total flushout" req="number false" />
                <a href="#" id="serial_add"><?php echo icon('fa-plus'); ?></a>
                <div class="help-block">urutkan nilai biaya registrasi tiap tipe serial dari nilai terkecil ke nilai yang lebih besar</div>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="serial_check" value="1" />
                  Pastikan hanya serial yang sudah di beli oleh member saja yang bisa diaktifasi
                </label>
              </div>
            </div>
            <script type="text/template" id="tpl_serial">
              <div class="form-group">
                <div class="form-inline" id="serial_{level}">
                  <input type="text" name="serial_name[{level}]" value="{name}" class="form-control nochange" placeholder="nama serial" req="any true" />
                  <input type="text" name="serial_price[{level}]" value="{value}" class="form-control nochange" placeholder="biaya registrasi" req="number true" />
                  <input type="text" name="serial_flushout[{level}]" value="{flushout}" class="form-control nochange serial_flushout" placeholder="total flushout" req="number false" />
                  <a href="#"><?php echo icon('fa-trash'); ?></a>
                </div>
              </div>
            </script>
          </div>
          <div class="form-group">
            <label>Kebijakan Transfer Bonus</label>
            <input type="text" class="form-control" name="min_transfer" req="number true" placeholder="Minimum Transfer" />
            <div class="checkbox">
              <label>
                <input type="checkbox" name="is_withdraw" value="1" />
                Member harus melakukan withdraw untuk bisa untuk menarik bonus agar transfer bisa di proses oleh admin (jangan centang jika ingin auto withdraw)
              </label>
            </div>
            <div class="help-block">
              Jika anda tidak mengaktifkan auto withdraw di atas (tidak mencentang), maka admin harus memproses transfer secara berkala pada menu "<a href="index.php?mod=bin.transfer_list" rel="admin_link">Transfer / Pending Transfer</a>" dengan: - mendownload file pending transfer - menandai yang sudah di transfer - lalu mengupload kembali
            </div>
            <br /><label>Potongan Biaya Transfer Bonus (masukkan 0 jika tidak ada)</label>
            <input type="text" class="form-control" name="surcharge" placeholder="tambahkan % di belakang angka jika ingin menggunakan presentase dari total transfer" req="any true" />
            <br /><label>Potongan Biaya untuk member dengan NPWP (masukkan 0 jika tidak ada)</label>
            <input type="text" class="form-control" name="surcharge_npwp" placeholder="tambahkan % di belakang angka jika ingin menggunakan presentase dari total transfer" req="any true" />
            <br /><label>Potongan Biaya untuk member tanpa NPWP (masukkan 0 jika tidak ada)</label>
            <input type="text" class="form-control" name="surcharge_npwp_no" placeholder="tambahkan % di belakang angka jika ingin menggunakan presentase dari total transfer" req="any true" />
          </div>
          <div class="form-group">
            <label>
              <input type="checkbox" name="bonus_node_ok" class="toggle" target="#bonus_node" value="1" />
              Aktifkan Bonus Titik
            </label>
            <div id="bonus_node">
              <div class="form-inline">
                <input type="text" name="bonus_node[]" class="form-control" placeholder="Nilai bonus titik..." />
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="bonus_node_gen" class="toggle" target="#bonus_node_gen" trigger=".add" value="1" />
                    Aktifkan Bonus Level Titik (jalur upline)
                  </label>
                </div>
              </div>
              <div id="bonus_node_gen">
                <div class="level"></div>
                <button type="button" class="btn btn-sm btn-default add" source=""><span class="glyphicon glyphicon-plus"></span> Tambah Level</button>
                <button type="button" class="btn btn-sm btn-default reset" source=""><span class="glyphicon glyphicon-trash"></span> Reset</button>
              </div>
              <?php echo bin_check_func('bin_bonus_node');?>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="bonus_gen_node_ok" class="toggle" target="#bonus_gen_node" trigger=".add" value="1" />
                  Aktifkan Bonus Generasi Titik (jalur sponsor)
                </label>
              </div>
              <div id="bonus_gen_node">
                <div class="level"></div>
                <button type="button" class="btn btn-sm btn-default add" source=""><span class="glyphicon glyphicon-plus"></span> Tambah Level</button>
                <button type="button" class="btn btn-sm btn-default reset" source=""><span class="glyphicon glyphicon-trash"></span> Reset</button>
                <?php echo bin_check_func('bin_bonus_gen_node');?>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>
              <input type="checkbox" name="bonus_sponsor_ok" class="toggle" target="#bonus_sponsor" value="1" />
              Aktifkan Bonus Sponsor
            </label>
            <div id="bonus_sponsor">
              <div class="form-inline">
                <input type="text" name="bonus_sponsor[]" class="form-control" placeholder="Nilai bonus sponsor..." />
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="bonus_sponsor_gen" class="toggle" target="#bonus_sponsor_gen" trigger=".add" value="1" />
                    Aktifkan Bonus Generasi Sponsor
                  </label>
                </div>
              </div>
              <div id="bonus_sponsor_gen">
                <div class="level"></div>
                <button type="button" class="btn btn-sm btn-default add" source=""><span class="glyphicon glyphicon-plus"></span> Tambah Level</button>
                <button type="button" class="btn btn-sm btn-default reset" source=""><span class="glyphicon glyphicon-trash"></span> Reset</button>
                <label style="font-weight: normal;">
                  <input type="checkbox" name="bonus_sponsor_double" class="toggle" target="#bonus_sponsor" value="1" />
                  Bonus Generasi Level 1 akan diterima oleh sponsor itu sendiri (bukan sponsor di atasnya)
                </label>
              </div>
              <?php echo bin_check_func('bin_bonus_sponsor');?>
            </div>
          </div>
          <div class="form-group">
            <label>
              <input type="checkbox" name="bonus_pair_ok" id="bonus_pair_ok" class="toggle" target="#bonus_pair" value="1" />
              Aktifkan Bonus Pasangan
            </label>
            <div id="bonus_pair">
              <div class="form-inline">
                Maximal flushout yang diterima:
                <input type="text" name="flushout_total" id="flushout_total" class="form-control" placeholder="total flushout" />
                <div class="checkbox serial_flushout_ok">
                  <label>
                    <input type="checkbox" name="serial_flushout_ok" id="serial_flushout_ok" value="1" />
                    Tentukan total flushout di tiap serial
                  </label>
                </div>
              </div>
              <div class="form-inline">
                Masa flushout terhitung setiap:
                <input type="text" name="flushout_time" class="form-control" placeholder="time" value="1" />
                <select name="flushout_duration" class="form-control">
                  <option value="HOUR">Jam</option>
                  <option value="DAY" selected>Hari</option>
                  <option value="WEEK">Minggu</option>
                  <option value="MONTH">Bulan</option>
                  <option value="YEAR">Tahun</option>
                </select>
                <?php echo help('Tentukan jangka waktu untuk membatasi masa flushout'); ?>
              </div>
              <div class="form-inline">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="flushwait" id="flushwait" checked="true" value="1" />
                    Hanguskan titik menunggu jika sudah:
                  </label>
                </div>
                <input type="text" name="flushwait_time" id="flushwait_time" class="form-control" placeholder="time" value="1" />
                <select name="flushwait_duration" id="flushwait_duration" class="form-control">
                  <option value="HOUR">Jam</option>
                  <option value="DAY" selected>Hari</option>
                  <option value="WEEK">Minggu</option>
                  <option value="MONTH">Bulan</option>
                  <option value="YEAR">Tahun</option>
                </select>
                <?php echo help('Jika ada titik menunggu yang sudah melebihi batas yang ditentukan maka tidak bisa lagi di pasangkan'); ?>
              </div>
              <div class="form-inline">
                <input type="text" name="bonus_pair[]" class="form-control" placeholder="Nilai bonus Pasangan..." />
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="bonus_pair_gen" class="toggle" target="#bonus_pair_gen" trigger=".add" value="1" />
                    Aktifkan Bonus Generasi Pasangan
                  </label>
                </div>
              </div>
              <div id="bonus_pair_gen">
                <div class="level"></div>
                <button type="button" class="btn btn-sm btn-default add" source=""><span class="glyphicon glyphicon-plus"></span> Tambah Level</button>
                <button type="button" class="btn btn-sm btn-default reset" source=""><span class="glyphicon glyphicon-trash"></span> Reset</button>
              </div>
              <?php echo bin_check_func('bin_bonus_pair');?>
            </div>
          </div>
          <div class="form-group">
            <label>
              <input type="checkbox" class="toggle" target="#reward_option" name="reward_use" value="1" />
              Aktifkan Member Reward
            </label>
            <?php
            $token = array(
              'table'  => 'bin_reward',
              'field'  => 'name',
              'id'     => 'id',
              'format' => 'CONCAT(name, " (", id, ")")',
              'sql'    => 'id<1',
              'expire' => strtotime('+2 HOURS'),
              );
            ?>
            <div id="reward_option">
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="reward_auto" value="1" />
                  Aktifkan auto reward, agar member mendapatkan reward/hadiah tanpa harus klaim terlebih dahulu
                </label>
              </div>
              <div class="form-control tags">
                <span></span>
                <span data-token="<?php echo encode(json_encode($token)); ?>" data-isallowednew="1" name="reward_list" contenteditable></span>
              </div>
              <div class="help-block">
                Masukkan reward yang akan di berikan ke member dan urutkan dari nilai terkecil ke terbesar. Gunakan tombol tab/enter untuk memasukkan reward baru. <br />
                Untuk persyaratan setiap reward bisa anda tentukan setelah market plan selesai dibuat
                <?php echo bin_check_func('bin_reward');?>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Peringkat Member</label>
            <?php
            $token = array(
              'table'  => 'bin_level',
              'field'  => 'name',
              'id'     => 'id',
              'format' => 'CONCAT(name, " (", id, ")")',
              'sql'    => 'id<1',
              'expire' => strtotime('+2 HOURS'),
              );
            ?>
            <div class="form-control tags">
              <span></span>
              <span data-token="<?php echo encode(json_encode($token)); ?>" data-isallowednew="1" name="level_list" req="any true" contenteditable></span>
            </div>
            <div class="help-block">
              Tentukan peringkat member dari terendah ke tertinggi. Gunakan tombol tab/enter untuk memasukkan nama peringkat baru. <br />
              Untuk persyaratan kenaikan peringkat bisa anda tentukan setelah marketplan selesai dibuat.
              <?php echo bin_check_func('bin_level');?>
            </div>
          </div>
        </div>
        <div class="panel-footer">
          <span type="button" class="btn btn-default" onclick="document.location.href='index.php?mod=bin.config_setup'"><span class="glyphicon glyphicon-chevron-left"></span></span>
          <button type="submit" class="btn btn-primary" name="submit" value="form">Submit <span class="glyphicon glyphicon-chevron-right"></span></button>
        </div>
        <script type="text/template" id="tpl_input">
          <div class="form-inline">
            Level {level} :
            <input type="text" name="{name}_gen_{level}" class="form-control" placeholder="nilai bonus" req="number true" />
          </div>
        </script
      </form>
    </div>
  </div>
</div>
<style type="text/css">
  #setupmanual form > .panel-body > .form-group {
    margin-bottom: 15px;
    border: 1px solid #ccc;
    padding: 15px;
    border-radius: 10px;
  }
</style>