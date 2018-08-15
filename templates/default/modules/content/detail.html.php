<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

?>
<div class="theme-inner-banner">
	<div class="opacity">
		<h2><?php echo $data['title'];?></h2>
	</div>
</div>
<div class="theme-blog-large-sideOne blog-details-content col-lg-9 col-md-8 col-xs-12">
	<div class="blog-post">
		<?php
		include tpl('detail_header'.$data['kind_id'].'.html.php');
		echo ($config['author']) ? '<span>'.lang('author').$data['created_by_alias'].'</span>' : '';
		echo ($config['created']) ? '<span>'.lang('created').content_date($data['created']).'</span>' : '';
		echo !empty($config['title']) ? '<h2>'.$data['title'].'</h2>' : '';
		echo $data['content'];
		if(empty($data['revised']))
		{
			$config['modified'] = 0;
		}
		echo @$config['modified'] ? '<span class="text text-muted pull-right">'.lang('Last modified').content_date($data['modified']).'</span>' : '';
		?>
	</div> <!-- /.blog-post -->

	<div class="share-option clearfix">
		<input type="hidden" value="<?php echo $data['id'];?>" id="content_value_id">
		<?php
		if( !empty($config['tag']) )
		{
			$r = content_category($data['id'], $config['tag_link']);
			if(!empty($r))
			{
				?>
				<ul class="float-left">
					<li><?php echo lang('Tags'); ?></li>
					<?php
					foreach ($r as $t)
					{						?>
						<li><?php echo $t; ?></li>
						<?php
					}
					?>
				</ul>
				<?php
			}
		}
		if(!empty($config['share']) || !empty($edit_data))
		{
			$sys->meta_add('<link rel="image_src" href="'.content_src($data['image'], false, true).'" />
			<meta property="og:title" content="'.$data['title'].'" />
			<meta property="og:type" content="article" />
			<meta property="og:url" content="'.content_link($data['id'], $data['title']).'" />
			<meta property="og:image" content="'.content_src($data['image'], false, true).'" />
			<meta property="og:site_name" content="'.config('site', 'url').'"/>
			<meta property="og:description" content="'.$data['description'].'" />');
			?>
			<ul class="float-right">
				<?php
				if (!empty($config['share']))
				{
					?>
					<li class="addthis_inline_share_toolbox"></li>
					<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5a944bca7be5c8a0"></script>
					<?php
				}
				if (!empty($edit_data))
				{
					?>
					<li><a href="<?php echo $Bbc->mod['circuit'].'.posted_form&id='.$data['id'];?>" class="tran3s round-border" title="<?php echo lang('edit content'); ?>"><i class="fa fa-edit" aria-hidden="true"></i></a></li>
					<?php
				}
				?>
			</ul>
			<?php
		}
		?>
	</div><!--  /.share-option -->
	<?php
	if( !empty($config['rating'])
		|| !empty($config['rating_vote'])
		|| !empty($config['print'])
		|| !empty($config['email'])
		|| !empty($config['pdf'])  )
	{
		$sys->link_js('detail.js', false);
		$tbl = $config['rating_vote'] ? 'bbc_content' : '';
		$tbl_id = $config['rating_vote'] ? $data['id'] : '';
		?>
		<div class="col-md-6 no-both">
			<?php echo $config['rating'] ? rating($data['rating'], $tbl, $tbl_id) : ''; ?>
		</div>
		<div class="col-md-6 no-left text-right">
			<div class="btn-group">
				<?php
				if (!empty($config['pdf']))
				{
					?>
					<a class="btn btn-default btn-sm" id="icon_pdf">
						<?php echo icon('fa-file-pdf-o',lang('convert to pdf')); ?>
					</a>
					<?php
				}
				if (!empty($config['email']))
				{
					?>
					<a class="btn btn-default btn-sm" id="icon_mail">
						<?php echo icon('fa-envelope',lang('tell friend')); ?>
					</a>
					<?php
				}
				if (!empty($config['print']))
				{
					?>
					<a class="btn btn-default btn-sm" id="icon_print">
						<?php echo icon('fa-print',lang('print preview')); ?>
					</a>
					<?php
				}
				?>
				<div class="clearfix"></div>
			</div>
		</div>
		<?php
	}
	$cfg = array(
		'table'    => 'bbc_content_comment',
		'field'    => 'content',
		'id'       => $data['id'],
		'type'     => $config['comment'],
		'list'     => $config['comment_list'],
		'link'     => content_link($data['id'], $data['title']),
		'form'     => $config['comment_form'],
		'emoticon' => $config['comment_emoticons'],
		'captcha'  => $config['comment_spam'],
		'approve'  => $config['comment_auto'],
		'alert'    => $config['comment_email'],
		'admin'    => $edit_data ? 1 : 0
		);
	echo _class('comment', $cfg)->show();
	?>
</div> <!-- /.blog-large-side -->
<?php
include tpl('rightblock.html.php');