<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'bin');
$form->initSearch();
if (config('plan_a', 'serial_use')=='1')
{
	$form->search->addInput('serial_type_id','selecttable');
	$form->search->input->serial_type_id->addOption('--pilih type--', '');
	$form->search->input->serial_type_id->setReferenceTable('`bin_serial_type` ORDER BY `id` ASC');
	$form->search->input->serial_type_id->setReferenceField( 'name', 'id' );
}

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('username, location_name, name', false);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form->initRoll($add_sql.' ORDER BY id DESC', 'id');

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Data Member');

$form->roll->addInput('member','multiinput');
$form->roll->input->member->setTitle('Member');
$form->roll->input->member->addInput('member1', 'editlinks');
$form->roll->input->member->addInput('member2', 'sqllinks');

$form->roll->input->member1->setCaption( '' );
$form->roll->input->member1->setModal();
$form->roll->input->member1->setFieldName( 'id AS member1' );
$form->roll->input->member1->setLinks(array(
	$Bbc->mod['circuit'].'.genealogy'        => icon('fa-sitemap').' Genealogy',
	$Bbc->mod['circuit'].'.bonus'            => icon('fa-usd').' Bonus',
	$Bbc->mod['circuit'].'.reward'           => icon('fa-trophy').' Reward',
	$Bbc->mod['circuit'].'.transfer_history' => icon('fa-money').' Transfer',
	$Bbc->mod['circuit'].'.list_signin'      => icon('fa-sign-in').' Login'
	));

$form->roll->input->member2->setModal();
$form->roll->input->member2->setFieldName( 'username AS member2' );
$form->roll->input->member2->setLinks($Bbc->mod['circuit'].'.list_detail');

$form->roll->addInput('name','sqlplaintext');
$form->roll->input->name->setTitle('nama');

$form->roll->addInput('total_sponsor','sqlplaintext');
$form->roll->input->total_sponsor->setTitle('Total Sponsor');
$form->roll->input->total_sponsor->setNumberFormat();
$form->roll->input->total_sponsor->setDisplayColumn(false);

$form->roll->addInput('total_downline','sqlplaintext');
$form->roll->input->total_downline->setTitle('Total Downline');
$form->roll->input->total_downline->setNumberFormat();
$form->roll->input->total_downline->setDisplayColumn(false);

$form->roll->addInput('total_left','sqlplaintext');
$form->roll->input->total_left->setTitle('Total Kiri');
$form->roll->input->total_left->setNumberFormat();
$form->roll->input->total_left->setDisplayColumn(false);

$form->roll->addInput('total_right','sqlplaintext');
$form->roll->input->total_right->setTitle('Total Kanan');
$form->roll->input->total_right->setNumberFormat();
$form->roll->input->total_right->setDisplayColumn(false);

$form->roll->addInput('depth_sponsor','sqlplaintext');
$form->roll->input->depth_sponsor->setTitle('Kdlmn Sponsor');
$form->roll->input->depth_sponsor->setCaption('Kedalaman Sponsor');
$form->roll->input->depth_sponsor->setNumberFormat();
$form->roll->input->depth_sponsor->setDisplayColumn(false);

$form->roll->addInput('depth_upline','sqlplaintext');
$form->roll->input->depth_upline->setTitle('Kdlmn Upline');
$form->roll->input->depth_upline->setCaption('Kedalaman Upline');
$form->roll->input->depth_upline->setNumberFormat();
$form->roll->input->depth_upline->setDisplayColumn(false);

$form->roll->addInput('position','select');
$form->roll->input->position->setTitle('Posisi');
$form->roll->input->position->addOption('Kanan', '1');
$form->roll->input->position->addOption('Kiri', '0');
$form->roll->input->position->setPlaintext(true);
$form->roll->input->position->setDisplayColumn(false);

$form->roll->addInput('balance','sqlplaintext');
$form->roll->input->balance->setTitle('Balance');
$form->roll->input->balance->setNumberFormat();
$form->roll->input->balance->setDisplayColumn(true);

$form->roll->addInput('serial_pin','sqlplaintext');
$form->roll->input->serial_pin->setTitle('Serial PIN');
$form->roll->input->serial_pin->setDisplayColumn(false);

$form->roll->addInput('location_name','sqlplaintext');
$form->roll->input->location_name->setTitle('Lokasi');
$form->roll->input->location_name->setDisplayColumn(false);

$form->roll->addInput('location_address','sqllinks');
$form->roll->input->location_address->setTitle('Alamat');
$form->roll->input->location_address->setLinks($Bbc->mod['circuit'].'.list&act=latlng');
$form->roll->input->location_address->setExtra('target="_blank"');
$form->roll->input->location_address->setDisplayColumn(false);

$form->roll->addInput('sponsor','multiinput');
$form->roll->input->sponsor->setTitle('Sponsor');
$form->roll->input->sponsor->addInput('sponsor1', 'editlinks');
$form->roll->input->sponsor->addInput('sponsor2', 'selecttable');

$form->roll->input->sponsor1->setCaption( '' );
$form->roll->input->sponsor1->setModal();
$form->roll->input->sponsor1->setFieldName( 'sponsor_id AS sponsor1' );
$form->roll->input->sponsor1->setLinks(array(
	$Bbc->mod['circuit'].'.genealogy'        => icon('fa-sitemap').' Genealogy',
	$Bbc->mod['circuit'].'.bonus'            => icon('fa-usd').' Bonus',
	$Bbc->mod['circuit'].'.reward'           => icon('fa-trophy').' Reward',
	$Bbc->mod['circuit'].'.transfer_history' => icon('fa-money').' Transfer'
	));

$form->roll->input->sponsor2->setReferenceTable('bin');
$form->roll->input->sponsor2->setReferenceField('username', 'id');
$form->roll->input->sponsor2->setFieldName( 'sponsor_id AS sponsor2' );
$form->roll->input->sponsor2->setLinks($Bbc->mod['circuit'].'.list_detail');
$form->roll->input->sponsor2->setExtra('rel="editlinksmodal"');
$form->roll->input->sponsor2->setPlaintext(true);
$form->roll->input->sponsor->setDisplayColumn(true);

$form->roll->addInput('upline','multiinput');
$form->roll->input->upline->setTitle('Upline');
$form->roll->input->upline->addInput('upline1', 'editlinks');
$form->roll->input->upline->addInput('upline2', 'selecttable');

$form->roll->input->upline1->setCaption( '' );
$form->roll->input->upline1->setModal();
$form->roll->input->upline1->setFieldName( 'upline_id AS upline1' );
$form->roll->input->upline1->setLinks(array(
	$Bbc->mod['circuit'].'.genealogy'        => icon('fa-sitemap').' Genealogy',
	$Bbc->mod['circuit'].'.bonus'            => icon('fa-usd').' Bonus',
	$Bbc->mod['circuit'].'.reward'           => icon('fa-trophy').' Reward',
	$Bbc->mod['circuit'].'.transfer_history' => icon('fa-money').' Transfer'
	));

$form->roll->input->upline2->setReferenceTable('bin');
$form->roll->input->upline2->setReferenceField('username', 'id');
$form->roll->input->upline2->setFieldName( 'upline_id AS upline2' );
$form->roll->input->upline2->setLinks($Bbc->mod['circuit'].'.list_detail');
$form->roll->input->upline2->setExtra('rel="editlinksmodal"');
$form->roll->input->upline2->setPlaintext(true);
$form->roll->input->upline->setDisplayColumn(false);

$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Gabung');
$form->roll->input->created->setDateFormat();
$form->roll->input->created->setDisplayColumn(true);


$form->roll->addInput('active','checkbox');
$form->roll->input->active->setTitle('Bonus');
$form->roll->input->active->setCaption('Active');

// $form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

echo $form->roll->getForm();
?>
<script type="text/javascript">
	_Bbc(function($){
		$(".fa-sign-in").each(function(){
			var a = $(this).parent();
			a.unbind("click");
			a.removeAttr("rel").attr("target", "_blank");
		});
	});
</script>