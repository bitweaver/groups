<?php
// $Header: /cvsroot/bitweaver/_bit_groups/admin/admin_group_inc.php,v 1.3 2008/02/13 15:07:50 wjames5 Exp $
// Copyright (c) 2008 bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// is this used?
//if (isset($_REQUEST["groupset"]) && isset($_REQUEST["homeGroup"])) {
//	$gBitSystem->storeConfig("home_group", $_REQUEST["homeGroup"]);
//	$gBitSmarty->assign('home_group', $_REQUEST["homeGroup"]);
//}

require_once( GROUP_PKG_PATH.'BitGroup.php' );

$formGroupLists = array(
	"group_list_group_id" => array(
		'label' => 'Id',
		'note' => 'Display the group id.',
	),
	"group_list_title" => array(
		'label' => 'Title',
		'note' => 'Display the title.',
	),
	"group_list_description" => array(
		'label' => 'Description',
		'note' => 'Display the description.',
	),
	"group_list_data" => array(
		'label' => 'Text',
		'note' => 'Display the text.',
	),
);
$gBitSmarty->assign( 'formGroupLists',$formGroupLists );

$processForm = set_tab();

if( $processForm ) {
	$groupToggles = array_merge( $formGroupLists );
	foreach( $groupToggles as $item => $data ) {
		simple_set_toggle( $item, 'group' );
	}

}

$group = new BitGroup();
$groups = $group->getList( $_REQUEST );
$gBitSmarty->assign_by_ref('groups', $groups['data']);
?>
