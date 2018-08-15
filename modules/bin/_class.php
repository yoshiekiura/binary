<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');
/*
Class ini digunakan untuk menampilkan papan genealogy
Contoh:
echo _class('bin')->show($bin_id);
*/
class bin_class
{
  private $maxlevel = 4;
  private $format   = 'd/m/Y';
  private $is_ajax  = 0;

  function __construct($db = 'db')
  {
    $this->db  = $GLOBALS[$db];
    $this->is_ajax = !empty($_GET['is_ajax']) ? 1 : 0;
  }
  public function setMaxlevel($level)
  {
  	$this->maxlevel = $level;
  }
  public function fetch($bin_id, $level=0)
	{
		if ($level < $this->maxlevel)
		{
			_func('bin');
			$data = bin_fetch_id($bin_id);
			if (!empty($data))
			{
				$level++;
				$downline = array(array(), array());
				$r_down   = $this->db->getAll("SELECT `id`, `position` FROM `bin` WHERE `upline_id`={$bin_id} AND `id` != {$bin_id} ORDER BY `position` ASC");
				foreach ($r_down as $down)
				{
					if (empty($downline[$down['position']]))
					{
						$downline[$down['position']] = call_user_func(array($this, __METHOD__), $down['id'], $level);
					}
				}
				return array(
					'current'  => $data,
					'upline'   => bin_fetch_id($data['upline_id']),
					'downline' => $downline
					);
			}
		}
		return array();
	}

  public function show($bin_id, $maxlevel=0)
	{
		global $Bbc, $db, $user, $sys;
	  $bin_id = intval($bin_id);
	  if (!empty($maxlevel) && $maxlevel > 0)
	  {
	  	$this->maxlevel = $maxlevel;
	  }
	  $data = $this->fetch($bin_id);
	  if (!empty($data))
	  {
		  ob_start();
		  link_css(__DIR__.'/_class.css', false);
		  link_js(__DIR__.'/_class.js', false);
		  ?>
			<center class="bin-board">
			  <table class="header">
			  	<?php
			  	if ($data['upline']!=$data['current'])
			  	{
			  		?>
				    <tr>
				      <td>
				        <span></span>
			          <?php echo $this->tpl($data['upline']); ?>
				      </td>
				    </tr>
			  		<?php
			  	}
			  	?>
			    <tr>
			      <td>
			        <span></span>
		        	<?php echo $this->tpl($data['current'], $data['upline']); ?>
			        <?php
			        echo $this->child($data['downline'], $data['current']);?>
			      </td>
			    </tr>
			  </table>
			</center>
			<div class="hidden" id="completeudt"><?php echo lang('Complete your data before cloning!!'); ?></div>
		  <?php
		  if (!$this->is_ajax)
		  {
		  	?>
				<div class="modal fade" tabindex="-1" role="dialog" id="bin-modal">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title"></h4>
				      </div>
				      <div class="modal-body"></div>
				    </div>
				  </div>
				</div>
				<div class="modal fade" tabindex="-1" role="dialog" id="bin-create">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-body">
					      	<div class="output"></div>
					      	<?php
					      	include _ROOT.'modules/bin/register.php';
					      	?>
					      </div>
					    </div>
					  </div>
				</div>
		  	<?php
		  }
		  $output = ob_get_contents();
		  ob_end_clean();
		  return $output;
		}else{
			$output = msg(lang('Maaf data tidak ditemukan.'),'warning');
		}
		echo $output;
	}
	public function child($downline, $upline, $level=0)
	{
		$level++;
		if ($level >= $this->maxlevel)
		{
			return '';
		}
		ob_start();
    ?>
    <table>
      <tr>
        <?php
        foreach ($downline as $position => $data)
        {
        	?>
        	<td>
        		<span></span>
      			<?php
      			if (!empty($data['current']))
      			{
      				echo $this->tpl($data['current'], $data['upline'], $position, $level);
      			}else{
      				echo $this->tpl(array(), $upline, $position, $level);
      			}
      			?>
      			<?php
      			if (!empty($data['downline']))
      			{
      				echo call_user_func(array($this, __METHOD__), $data['downline'], $data['current'], $level);
      			}
      			?>
        	</td>
        	<?php
        }
        ?>
      </tr>
    </table>
    <?php
	  $output = ob_get_contents();
	  ob_end_clean();
	  return $output;
	}

	public function tpl($data, $upline=array(), $position=0, $level=0)
	{
		ob_start();
		if (!empty($data))
		{
			$level++;
			$icon = ($level >= $this->maxlevel) ? 'down' : 'up';
			$cls  = $data['active'] ? 'success' : 'danger';
			?>
			<div class="alert-<?php echo $cls; ?>" title="<?php echo $data['username'].' ('.$data['name'].')' ?>">
				<?php
				if (strlen($data['username']) > 9)
				{
					?>
					<b class="first"><?php echo $data['username']; ?></b>
					<b class="end"><?php echo $data['username']; ?></b>
					<?php
				}else{
					?>
					<b><?php echo $data['username']; ?></b>
					<?php
				}
				?>
				<small><?php echo $data['name'] ?></small>
				<small><?php echo $this->format($data['created']); ?></small>
				<!-- <small><?php echo money($data['depth_left'], true).' | '.money($data['depth_right'], true); ?></small><br /> -->
				<small class="expander" data-id="<?php echo $data['id']; ?>"><?php echo money($data['total_left'], true); ?> <i class="glyphicon glyphicon-collapse-<?php echo $icon; ?>"></i> <?php echo money($data['total_right'], true); ?></small>
			</div>
			<?php
			if (!$this->is_ajax)
			{
				?>
				<div class="hidden">
					<?php
	        $_GET['id'] = $data['id'];
	        include _ROOT.'modules/bin/admin/list_detail.php';
					?>
				</div>
				<?php
			}
		}else{
			$icon = $this->is_ajax ? 'user' : 'plus';
			?>
			<div>
				<h1 data-upline="<?php echo $upline['username']; ?>" data-position="<?php echo $position; ?>"><span class="glyphicon glyphicon-<?php echo $icon; ?> member-add"></span></h1>
			</div>
			<?php
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	private function format($date)
	{
		return date($this->format, strtotime($date));
	}


	/*TAMBAHAN COPY FROM MSUPPO*/
	function message($to, $post)
  {
  	global $user;
    $member = $this->db->getRow('SELECT * FROM bin WHERE `user_id`='.$user->id);
    if(is_numeric($to))
    {
      $recipient = array($this->data($to));
    }else{
      $q = "SELECT * FROM bin WHERE username='{$to}'";
      $recipient = $this->db->getAll($q);
    }

    $output = false;
    if(!empty($member['id']) && !empty($post['title']) && !empty($post['detail']))
    {
      $par_id = @intval($post['par_id']);
      $main_id = @intval($post['main_id']);
      foreach((array)$recipient AS $to)
      {
        $q = "INSERT INTO bin_message
        SET par_id  =".$par_id."
        , main_id   = ".$main_id."
        , from_id   =".$member['id']."
        , from_name ='".addslashes($member['name'])."'
        , to_id     =".$to['id']."
        , to_name   ='".addslashes($to['name'])."'
        , title     ='".addslashes($post['title'])."'
        , detail    ='".addslashes($post['detail'])."'
        , child     = 1
        , created   = NOW()
        , updated   = NOW()
        , readed    = 0";
        $output = $this->db->Execute($q);
        if(!$output)
        {
          break;
        }else{
          if (empty($main_id) && empty($par_id))
          {
            $i = $this->db->Insert_ID();
            $this->db->Execute("UPDATE bin_message SET main_id={$i} WHERE id={$i}");
          }
          if($par_id)
          {
            $this->message_update($par_id);
          }
        }
      }
    }
    return $output;
  }
  function data($id)
  {
    $id = intval($id);
    if(empty($this->output['data'][$id]))
    {
      $q = "SELECT * FROM bin WHERE id=".@intval($id);
      $this->output['data'][$id] = $this->db->getRow($q);
    }
    return $this->output['data'][$id];
  }
  private function message_update($par_id)
  {
    if($par_id > 0)
    {
      $dt = $this->db->getRow("SELECT id, par_id FROM bin_message WHERE id={$par_id}");
      $ad = $dt['par_id'] == 0 ? ', readed=0, updated=NOW()' : '';
      $this->db->Execute("UPDATE bin_message SET child=(child+1) {$ad} WHERE id={$par_id}");
      if(!empty($dt['par_id']))
      {
        $this->message_update($dt['par_id']);
      }
    }
  }
}