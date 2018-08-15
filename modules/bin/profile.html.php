<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">
      <?php
      echo ucwords($member['name']);
      ?>
    </h3>
  </div>
  <div class="panel-body">
    <div class="col-sm-4">
      <div class="avatar__image">
        <div class="img-responsive">
          <?php
          if (empty($user->image))
          {
            $user->image = $sys->template_url.'images/avatar.png';
          }
          ?>
          <img src="<?php echo $user->image; ?>" alt="" />
        </div>
      </div>
    </div>
    <div class="col-sm-8">
      <div class="col-sm-4">
        <p class="card__title"><?php echo lang('Serial'); ?></p>
        <p class="card__text"><?php echo $account['serial'] ?></p>
      </div>
      <div class="col-sm-4">
        <p class="card__title"><?php echo lang('Upline'); ?></p>
        <p class="card__text"><?php echo $account['upline'] ?></p>
      </div>
      <div class="col-sm-4">
        <p class="card__title"><?php echo lang('Sponsor'); ?></p>
        <p class="card__text"><?php echo $account['sponsor'] ?></p>
      </div>
      <div class="clearfix"></div>
      <?php
      $r_type = config('plan_a', 'serial_list');
      if (count($r_type) > 1)
      {
        ?>
        <div class="col-sm-4">
          <p class="card__title"><?php echo lang('Tipe Serial'); ?></p>
          <p class="card__text"><?php echo @$r_type[$member['serial_type_id']-1]; ?></p>
        </div>
        <?php
      }else{
        ?>
        <div class="col-sm-4">
          <p class="card__title"><?php echo lang('PIN'); ?></p>
          <p class="card__text"><?php echo $member['serial_pin'] ?></p>
        </div>
        <?php
      }
      ?>
      <div class="col-sm-4">
        <p class="card__title"><?php echo lang('Bergabung'); ?></p>
        <p class="card__text"><?php echo date('M jS, Y', strtotime($member['created'])); ?></p>
      </div>
      <div class="col-sm-4">
        <p class="card__title"><?php echo lang('Lokasi'); ?></p>
        <p class="card__text"><?php echo $member['location_name'] ?></p>
      </div>
      <div class="clearfix"></div>
      <?php
      if (count($r_type) > 1)
      {
        ?>
        <div class="col-sm-4">
          <p class="card__title"><?php echo lang('PIN'); ?></p>
          <p class="card__text"><?php echo $member['serial_pin'] ?></p>
        </div>
        <?php
      }
      ?>
      <div class="col-sm-4">
        <p class="card__title"><?php echo lang('Alamat'); ?></p>
        <p class="card__text"><?php echo $member['location_address'] ?></p>
      </div>
      <div class="col-sm-4">
        <p class="card__title"><?php echo lang('Koordinat'); ?></p>
        <p class="card__text">
          <?php
          if (!empty($member['location_latlong']))
          {
            ?>
            <a href="https://www.google.com/maps?q=<?php echo $member['location_latlong']; ?>" target="_blank">
              <?php echo $member['location_latlong']; ?>
            </a>
            <?php
          }
          ?>
        </p>
      </div>
      <?php
      if (!empty($editbutton))
      {
        ?>
        <div class="clearfix"></div>
        <a href="<?php echo $Bbc->mod['circuit'].'.profile_edit&return='.urlencode(seo_url()); ?>" class="pull-right">
          <button type="button" name="button" class="btn esd-btn esd-btn--default text-right">
            <i class="fa fa-edit"></i> <?php echo lang('Edit Profile') ?>
          </button>
        </a>
        <?php
      }
      ?>
    </div>
    <!--
    <div class="clearfix"></div>
    <div class="col-sm-12">
      <div class="col-md-3 card--color-one minicard__each">
        <div class="minicard__group text-center">
          <p class="minicard__title"><?php echo lang('Posisi'); ?></p>
          <p class="minicard__text"><?php echo $member['position'] ? lang('Kanan') : lang('Kiri'); ?></p>
        </div>
      </div>
      <div class="col-md-3 card--color-two minicard__each">
        <div class="minicard__group text-center">
          <p class="minicard__title"><?php echo lang('Total Sponsoring'); ?></p>
          <p class="minicard__text"><?php echo money($member['total_sponsor'], true); ?></p>
        </div>
      </div>
      <div class="col-md-3 card--color-three minicard__each">
        <div class="minicard__group text-center">
          <p class="minicard__title"><?php echo lang('Jaringan Kiri'); ?></p>
          <p class="minicard__text"><?php echo money($member['total_left'], true); ?></p>
        </div>
      </div>
      <div class="col-md-3 card--color-four minicard__each">
        <div class="minicard__group text-center">
          <p class="minicard__title"><?php echo lang('Jaringan Kanan'); ?></p>
          <p class="minicard__text"><?php echo money($member['total_right'], true); ?></p>
        </div>
      </div>
    </div>
    -->
</div>
</div>
<div class="clearfix"></div>
<div class="panel panel-default">
  <div class="panel-body">
    <div class="col-md-4">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo lang('Identitas'); ?></h3>
        </div>
        <div class="panel-body">
          <p class="card__title"><?php echo lang('No. KTP'); ?></p>
          <p class="card__text"><?php echo @$account['No. KTP']; ?></p>
          <p class="card__title"><?php echo lang('NPWP'); ?></p>
          <p class="card__text"><?php echo @$account['NPWP']; ?></p>
          <p class="card__title"><?php echo lang('Phone'); ?></p>
          <p class="card__text"><a href = "tel:<?php echo @$account['Phone']; ?>"><?php echo @$account['Phone']; ?></a></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo lang('Jaringan Overview'); ?></h3>
        </div>
        <div class="panel-body">
          <p class="card__title"><?php echo lang('Total Sponsor'); ?></p>
          <p class="card__text"><?php echo money($member['total_sponsor'], true); ?></p>
          <p class="card__title"><?php echo lang('Total Downline'); ?></p>
          <p class="card__text"><?php echo lang('%s (%s Kiri, %s Kanan)', money($member['total_downline'], true), money($member['total_left'], true), money($member['total_right'], true)); ?></p>
          <p class="card__title"><?php echo lang('Kedalaman'); ?></p>
          <p class="card__text"><?php echo lang('%s Kiri, %s Kanan', money($member['depth_left'], true), money($member['depth_right'], true)); ?></p>
          <p class="card__title"><?php echo lang('Level Jaringan'); ?></p>
          <?php
          if (!empty($editbutton))
          {
            ?>
            <p class="card__text"><?php echo lang('%s Titik, %s Sponsor (dari root)', money($member['depth_upline'], true), money($member['depth_sponsor'], true)); ?></p>
            <?php
          }else{
            ?>
            <p class="card__text"><?php echo lang('%s Titik', money($member['depth_upline'] - $Bbc->member['depth_upline'], true)); ?></p>
            <?php
          }
          ?>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo lang('Rekening'); ?></h3>
        </div>
        <div class="panel-body">
          <p class="card__title"><?php echo lang('Bank'); ?></p>
          <p class="card__text"><?php echo @$account['Rekening Bank']; ?></p>
          <p class="card__title"><?php echo lang('No. Rekening'); ?></p>
          <p class="card__text"><?php echo @$account['No. Rekening']; ?></p>
        </div>
      </div>
    </div>
  </div>
</div>
