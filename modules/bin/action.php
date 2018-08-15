<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$output = array(
  'success' => 0,
  'message' => lang('Failed to update data')
  );
$msg = '';
if(!empty($_GET['id']))
{
  switch($_GET['id'])
  {
    case 'message':
      if(empty($_POST['username']))
      {
        $msg = lang('Empty Username of Message');
      }else
      if(empty($_POST['title']))
      {
        $msg = lang('Empty subject of Message');
      }else
      if(empty($_POST['detail']))
      {
        $msg = lang('Empty Content of Message');
      }else{
        $msg = _class('bin')->message($_POST['username'], $_POST) ? lang('Message has been sent') : lang('Message not sent');
      }
      
      $type = $msg==lang('Message has been sent') ? 'success' : 'danger';
      $output = array(
        'success' => $type=='success' ? 1 : 0,
        'message' => msg($msg, $type)
        );
    break;
    case 'open':
      $r       = explode('_', @$_GET['msg']);
      $main_id = @intval($r[0]);
      $msg_id  = @intval($r[1]);
      if($main_id > 0)
      {
        $q = "SELECT * FROM bin_message WHERE main_id={$main_id} ORDER BY created ASC";
      }else{
        $q = "SELECT * FROM bin_message WHERE id={$msg_id} ORDER BY created ASC";
      }
      $arr    = $db->getAll($q);
      $output = array(
        'success' => 1,
        'message' => matrix_msg_expand($arr)
        );
    break;
    case 'compose':
      if(!empty($_POST['par_id']) && !empty($_POST['detail']))
      {
        $_POST['par_id'] = intval($_POST['par_id']);
        $d = $db->getRow("SELECT * FROM bin_message WHERE id=".$_POST['par_id']);
        if(!empty($d))
        {
          $_GET['id']       = $d['from_id'];
          $_POST['main_id'] = $d['main_id'];
          $_POST['title']   = $d['title'];
        }else{
          $output['message'] = lang('failed to send message');
        }
      }
      if (!empty($_POST['detail']) && !empty($_POST['title']))
      {
        $output['success'] = _class('bin')->message($_GET['id'], $_POST) ? 1 : 0;
        if ($output['success'])
        {
          $output['message'] = '
          <div class="well well-sm">
            <i>Me : <span class="date">Just Now</span></i>
            <div>'.stripslashes($_POST['detail']).'</div>
          </div>
          ';
        }
      }
    break;
  }
}
$sys->stop();
header('content-type: application/json; charset: UTF-8');
header('cache-control: must-revalidate');
header('expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
if (empty($output)) {
  echo '{}';
}else
if (defined('JSON_PRETTY_PRINT')) {
  echo json_encode($output, JSON_PRETTY_PRINT);
}else{
  echo json_encode($output);
}
function matrix_msg_expand($arr, $par_id = 0)
{
  _func('date');
  global $db, $main_id, $msg_id, $user;
  $member = $db->getRow('SELECT * FROM bin WHERE `user_id`='.$user->id);
  $out = array();
  foreach($arr AS $data)
  {
    if($data['par_id'] == $par_id)
    {
      $children = call_user_func(__FUNCTION__, $arr, $data['id']);
      $class    = '';
      $title    = ($member['id'] == $data['from_id']) ? 'Me' : $data['from_name'];
      if(empty($data['readed']) && $member['id']==$data['to_id'])
      {
        $db->Execute("UPDATE bin_message SET readed=1 WHERE id=".$data['id']);
      }else
      if(!empty($children))
      {
        $class = ' style="display: none;"';
      }
      if(empty($children) && $member['id'] == $data['to_id'])
      {
        $children = '<input class="reply" type="text" placeholder="'.lang('Reply Message...').'" />';
      }
      $out[] = '
<div class="well well-sm" id="par_id'.$data['id'].'">
  <i>'.$title.'<small class="date">'.timespan($data['created']).'</small></i>
  <div>'.$data['detail'].'</div>
  '.$children.'
</div>';
    }
  }
  if(!empty($out))
  {
    return implode('', $out);
  }else{
    return '';
  }
}
