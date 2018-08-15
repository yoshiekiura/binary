<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id  = @intval($_GET['id']);
$mlm = _class('bin');
$sys->nav_add(lang('Send Message'));
// // ALLIDS MEMBERS
// if(empty($_SESSION[bbcAuth]['allids']))
// {
//   $allids = $mlm->allids($member['id']);
//   $_SESSION[bbcAuth]['allids'] = $allids;
// }else{
//   $allids = $_SESSION[bbcAuth]['allids'];
// }
// if(true)  // in_array($id, $allids)) # ini kalo dilarang kirim message yang bukan membernya
// {
  pr($mlm);
  if(!empty($_POST['title']) && !empty($_POST['detail']))
  {
    $out    = $mlm->message($id, $_POST);
    $msg    = $out ? 'Message has been sent' : 'Message is not sent';
    $type   = $out ? 'success' : 'danger';
    $output = msg(lang($msg), $type);
  }else $output = '';
  $usr = $mlm->data($id);
  echo $output;
  ?>
  <form action="" method="POST" role="form">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><?php echo lang('Send Message'); ?></h3>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label><?php echo lang('Send To');?></label>
          <div class="form-control-static">
            <?php echo $usr['name'];?>
          </div>
        </div>
        <div class="form-group">
          <label><?php echo lang('Subject');?></label>
          <input type="text" name="title" class="form-control" placeholder="<?php echo lang('Subject');?>" />
        </div>
        <div class="form-group">
          <label><?php echo lang('Message');?></label>
          <textarea name="detail" class="form-control" placeholder="<?php echo lang('Message');?>"></textarea>
        </div>
      </div>
      <div class="panel-footer">
        <button type="submit" name="send_mail" value="Send Message" class="btn btn-primary btn-sm"><?php echo icon('send').' '.lang('Send Message') ?></button>
      </div>
    </div>
  </form>
  <?php
// }else{
//   echo msg('Not Allowed to send message');
// }
