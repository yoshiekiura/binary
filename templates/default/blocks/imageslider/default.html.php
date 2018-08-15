<?php if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$count = count($output['images']);


if ($count > 0)
{
  ?>
  <div id="banner">
    <div class="rev_slider_wrapper hidden-xs" style="margin-bottom: 20px">
      <div id="finance-main-banner" class="rev_slider" data-version="5.0.7">
        <ul>
          <?php
          foreach ($output['images'] as $key => $dt)
          {
            ?>
            <li data-index="rs-280" data-transition="zoomout" data-slotamount="default" data-easein="Power4.easeInOut" data-easeout="Power4.easeInOut" data-masterspeed="2000"  data-rotate="0" data-saveperformance="off" data-title="<?php echo $dt['title'] ?>" data-description="">
              <img src="<?php echo $dt['image']; ?>" alt="<?php echo $dt['title'] ?>" title="<?php echo $dt['title'] ?>" class="rev-slidebg" data-bgparallax="3" data-bgposition="center center" data-duration="20000" data-ease="Linear.easeNone" data-kenburns="on" data-no-retina="" data-offsetend="0 0" data-offsetstart="0 0" data-rotateend="0" data-rotatestart="0" data-scaleend="100" data-scalestart="140" />
              <?php 
              if (!empty($output['config']['caption']))
              {
                ?>
                <div class="tp-caption"
                  data-x="['left','left','left','center']" data-hoffset="['0','0','0','10']"
                  data-y="['middle','middle','middle','middle']" data-voffset="['-100','-100','-100','-160']"
                  data-width="full"
                  data-height="none"
                  data-whitespace="normal"
                  data-transform_idle="o:1;"
                  data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;"
                  data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                  data-mask_in="x:0px;y:[100%];"
                  data-mask_out="x:inherit;y:inherit;"
                  data-start="500"
                  data-splitin="none"
                  data-splitout="none"
                  data-responsive_offset="on"
                  style="z-index: 6; white-space: nowrap;">
                  <h1>Provide Best <br>Financial Service!!</h1>
                </div>
                <?php
              }

              if (!empty($dt['link']))
              {
                ?>
                <div class="tp-caption"
                  data-x="['left','left','left','left']" data-hoffset="['187','187','187','10']"
                  data-y="['middle','middle','middle','middle']" data-voffset="['130','130','130','185']"
                  data-transform_idle="o:1;"
                  data-transform_hover="o:1;rX:0;rY:0;rZ:0;z:0;s:300;e:Power1.easeInOut;"
                  data-transform_in="x:[100%];z:0;rX:0deg;rY:0deg;rZ:0deg;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2500;e:Power3.easeInOut;"
                  data-transform_out="auto:auto;s:1000;e:Power2.easeInOut;"
                  data-start="2500"
                  data-splitin="none"
                  data-splitout="none"
                  data-responsive_offset="on">
                  <a href="<?php echo $dt['link'] ?>" class="read-button button-one f-p-bg-color"><?php echo lang('Kunjungi Halaman') ?></a>
                </div>
                <?php
              }
              ?>
            </li>
            <?php
          }
          ?>
        </ul>
      </div>
    </div>

    <div class="visible-xs" style="margin-bottom: 20px">
      <?php
        if ($count > 0)
        {
          $style = !empty($output['config']['fixsize']) ? ' style="width:'.@$output['cat']['width'].'px;height:'.@$output['cat']['height'].'px;overflow:hidden;"' : '';
          ?>
          <div id="imageslider<?php echo $block->id; ?>"<?php /*echo $style;*/ ?> class="carousel slide fullscreen" data-ride="carousel">
          <?php
          if (!empty($output['config']['indicator']) && $count > 1)
          {
            echo '<ol class="carousel-indicators">';
            foreach ($output['images'] as $key => $value)
            {
              $cls = $key ? '' : ' class="active"';
              echo '<li data-target="#imageslider'.$block->id.'" data-slide-to="'.$key.'"'.$cls.'></li>';
            }
            echo '</ol>';
          }
          echo '<div class="carousel-inner">';
          foreach ($output['images'] as $key => $dt)
          {
            $cls = $key ? '' : ' active';
            ?>
            <div class="item<?php echo $cls; ?>">
              <?php echo !empty($dt['link']) ? '<a href="'.$dt['link'].'" title="'.$dt['title'].'">' : ''; ?>
              <img src="<?php echo $dt['image']; ?>" alt="<?php echo $dt['title'] ?>" title="<?php echo $dt['title'] ?>" />
              <?php echo !empty($output['config']['caption']) ? '<div class="carousel-caption"><h3>'.$dt['title'].'</h3></div>' : ''; ?>
              <?php echo !empty($dt['link']) ? '</a>' : ''; ?>
            </div>
            <?php
          }
          echo '</div>';
          if (!empty($output['config']['control']) && $count > 1)
          {
            ?>
            <a class="left carousel-control" href="#imageslider<?php echo $block->id; ?>" role="button" data-slide="prev">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a class="right carousel-control" href="#imageslider<?php echo $block->id; ?>" role="button" data-slide="next">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            <?php
          }
          ?>
          </div>
          <?php
        }
      ?>
    </div>
  </div>
  <?php
}