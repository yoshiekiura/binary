<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->link_js('message.js', false);
$sys->link_css('message.css');
$tabkey = array(lang('Inbox'), lang('Sent'), lang('Compose'));
$mytabs = array_flip($tabkey);
$member = bin_fetch_id($user->id);
_func('date');

/* INBOX */
ob_start();
  $id = 'inbox';
  $page = @intval($_GET[$id]);
  $show = 30;
  $q = "SELECT SQL_CALC_FOUND_ROWS * FROM `bin_message` WHERE to_id=".$member['id']."
  GROUP BY main_id ORDER BY readed ASC, updated DESC LIMIT {$page} , {$show}";
  $r_msg = $db->getAll($q);
  $found =  $db->getOne('SELECT FOUND_ROWS()');
  if(empty($r_msg))
  {
    echo msg(lang('Message is empty'));
  }else{
    foreach($r_msg AS $msg)
    {
      echo bin_msg_show($msg, $type='inbox');
    }
    echo page_list($found, $show, $page, $id);
  }
$mytabs[$tabkey[0]] = ob_get_contents();
ob_end_clean();

/* SENT */
ob_start();
  $id = 'outbox';
  $page = @intval($_GET[$id]);
  $q = "SELECT SQL_CALC_FOUND_ROWS * FROM `bin_message` WHERE from_id=".$member['id']." ORDER BY created DESC LIMIT {$page} , {$show}";
  $r_msg = $db->getAll($q);
  $found = $db->getOne('SELECT FOUND_ROWS()');
  if(empty($r_msg))
  {
    echo msg(lang('Never send message'));
  }else{
    foreach($r_msg AS $msg)
    {
      echo bin_msg_show($msg);
    }
    echo page_list($found, $show, $page, $id);
  }
$mytabs[$tabkey[1]] = ob_get_contents();
ob_end_clean();

/* COMPOSE */
// pr($_POST, __FILE__.':'.__LINE__);
ob_start();
?>
<div id="mail_output"></div>
<form action="" method="post" id="form_mail" enctype="multipart/form-data" role="form">
  <div class="panel panel-default" id="i1">
    <div class="panel-body">
      <div class="form-group">
        <label><?php echo lang('Send To');?></label>
        <input type="text" name="username" value="" class="form-control" placeholder="<?php echo lang('Insert Username or Member ID');?>" />
      </div>
      <div class="form-group">
        <label><?php echo lang('Subject');?></label>
        <input type="text" name="title" value="" class="form-control" placeholder="<?php echo lang('Subject');?>" />
      </div>
      <div class="form-group">
        <label><?php echo lang('Message');?></label>
        <textarea name="detail" class="form-control" placeholder="<?php echo lang('Message');?>"></textarea>
      </div>
    </div>
    <div class="panel-footer">
      <input type="hidden" value="0" name="par_id">
      <button type="submit" name="send_mail" value="Send Message" class="btn btn-primary btn-sm">
        <span class="glyphicon glyphicon-send"></span>
        Send Message
      </button>
      <button type="reset" class="btn btn-warning btn-sm">
        <span class="glyphicon glyphicon-repeat"></span>
        Reset Form
      </button>
    </div>
  </div>
</form>
<?php
$mytabs[$tabkey[2]] = ob_get_contents();
ob_end_clean();

echo tabs($mytabs);
?>
<div id="loading">Loading...</div>
<div id="mail_form" class="hidden">
  <form class="form" role="form">
    <div class="form-group">
      <textarea class="form-control"></textarea>
      <input type="submit" class="btn btn-default btn-sm" value="<?php echo lang('Reply Message');?>" onclick="return reply(this);">
    </div>
  </form>
</div>
<?php
function bin_msg_show($data, $type='')
{
  global $db, $member;
  if(!is_array($data))
  {
    return msg($data);
  }
  $title = $data['title'];
  if($type == 'inbox')
  {
    if($data['main_id'])
    {
      $data['child'] = $db->getOne("SELECT child FROM bin_message WHERE id=".$data['main_id']);
    }
    if($data['child'] > 1)
    {
      $title .= ' (<span class="child">'.money($data['child']).'</span>)';
    }
    $date = timespan($data['updated']);
    $add = '';
  }else{
    $date = timespan($data['created']);
    $stat= empty($data['readed']) ? '( Unread )' : '( '.lang('Last Activity').' '.timespan($data['updated']).')';
    $add = '
  <div style="display: none;">
    <i>To : '.$data['to_name'].'<span class="date">'.$stat.'</span></i>
    <div>'.$data['detail'].'</div>
  </div>';
  }
  return '
<div class="well well-sm parent" id="'.$data['main_id'].'_'.$data['id'].'">
  <h3>'.$title.'<small class="date">'.$date.'</small></h3>
  '.$add.'
</div>
';
}