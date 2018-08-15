<div class="col-sm-12 card__wrapper section__status">
  <div class="card border-0">
    <div class="card-body">
      <h5 class="card-title"><?php echo ucwords($member['name']); ?></h5>
      <ul class="flex card__list">
        <li class="card__each col-sm-3">
          <p class="card__title"><?php echo lang('Serial'); ?></p>
          <p class="card__text"><?php echo $account['serial']; ?></p>
        </li>
        <li class="card__each col-sm-3">
          <p class="card__title"><?php echo lang('Upline'); ?></p>
          <p class="card__text"><?php echo $account['upline']; ?></p>
        </li>
        <li class="card__each col-sm-3">
          <p class="card__title"><?php echo lang('Sponsor'); ?></p>
          <p class="card__text"><?php echo $account['sponsor']; ?></p>
        </li>
        <li class="card__each col-sm-3">
          <p class="card__title"><?php echo lang('Tanggal Bergabung'); ?></p>
          <p class="card__text"><?php echo date('M jS, Y', strtotime($member['created'])); ?></p>
        </li>
      </ul>
      <ul class="row minicard border-0">
        <li class="col-md-3 card--color-one minicard__each">
          <div class="minicard__group text-center">
            <p class="minicard__title"><?php echo lang('Posisi'); ?></p>
            <p class="minicard__text"><?php echo $member['position'] ? lang('Kanan') : lang('Kiri'); ?></p>
          </div>
        </li>
        <li class="col-md-3 card--color-two minicard__each">
          <div class="minicard__group text-center">
            <p class="minicard__title"><?php echo lang('Total Sponsoring'); ?></p>
            <p class="minicard__text"><?php echo money($member['total_sponsor'], true); ?></p>
          </div>
        </li>
        <li class="col-md-3 card--color-three minicard__each">
          <div class="minicard__group text-center">
            <p class="minicard__title"><?php echo lang('Jaringan Kiri'); ?></p>
            <p class="minicard__text"><?php echo money($member['total_left'], true); ?></p>
          </div>
        </li>
        <li class="col-md-3 card--color-four minicard__each">
          <div class="minicard__group text-center">
            <p class="minicard__title"><?php echo lang('Jaringan Kanan'); ?></p>
            <p class="minicard__text"><?php echo money($member['total_right'], true); ?></p>
          </div>
        </li>
      </ul>
      <?php
      if (!empty($exists))
      {
        link_js(_ROOT.'modules/bin/images/chart.js');
        ?>
        <div class="container" id="myreport">
          <span class="pull-left navi"><a href="" id="link_prev"><?php echo icon('fa-angle-left'); ?></a></span>
          <span class="pull-right navi"><a href="" id="link_next"><?php echo icon('fa-angle-right'); ?></a></span>
          <div id="container" style="height: 400px; margin: 0 auto"><center style="padding-top: 155px;"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i> <span class="sr-only">Loading...</span></center></div>
          <div class="table-responsive"></div>
        </div>
        <div class="clearfix"></div>
        <style type="text/css">
          .container {
            position: relative;;
          }
          .container .navi {
            position: absolute;
            top: 120px;
            z-index: 99;
            display: none;
            font-size: 60px;
          }
          .container .navi a {
            color: #ccc;
          }
          .container .navi a:hover {
            color: #333;
          }
          .container .pull-left {
            left: 10px;
          }
          .container .pull-right {
            right: 10px;
          }
          .table td {
            .pull-right()
          }
        </style>
        <?php
      }
      ?>
    </div>
  </div>
</div>
<div class="col-sm-6 card__wrapper">
  <div class="card border-0">
    <div class="card-body">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo lang('Status Komisi'); ?></h3>
        </div>
        <div class="panel-body">
          <?php include _ROOT.'modules/bin/bonus_status.php'; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="col-sm-6 card__wrapper">
  <div class="card border-0">
    <div class="card-body">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo lang('Potensi Komisi Hari Ini').' '.date('d M Y', time()); ?></h3>
        </div>
        <div class="panel-body">
          <!-- cari yang credit = 1 & finance = 0 -->
          <?php
            $r_type = bin_bonus_list();
            $date = date('Y-m-d', time());
            $tables  = array();
            $total   = 0;
            foreach ($r_type as $key => $value)
            {
              $bonus = $db->getRow("SELECT amount,ondate FROM `bin_balance` WHERE `bin_id`={$id} AND `type_id`={$key} AND `ondate`='{$date}'");
              $tables[] = array(ucwords($value['name']), money($bonus['amount']));
              $total += $bonus['amount'];
            }
            $tables[] = array('<b>'.lang('Jumlah').'</b>', '<b>'.money($total).'</b>');
            echo table($tables, array(lang('Keterangan'), 'Total (Rp.)'));
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
