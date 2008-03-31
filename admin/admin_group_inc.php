<?php
// $Header: /cvsroot/bitweaver/_bit_groups/admin/admin_group_inc.php,v 1.5 2008/03/31 15:52:41 wjames5 Exp $
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


// get installed content types
foreach( $gLibertySystem->mContentTypes as $cType ) {
	$formGroupContent['guids']['group_content_'.$cType['content_type_guid']]  = $cType['content_description'];
}

// where to display content permalinks to mapped-group
$formGroupServiceDisplayOptions = array(
	"group_in_nav" => array(
		'label' => 'Navigation Link',
		'note' => 'Shows a link to the group at the top of a page. Only visible when the full content page is loaded.',
		'type' => 'toggle',
	),
	"group_in_body" => array(
		'label' => 'In Body Area',
		'note' => 'Shows a link to the group above the body text of content. Visible both in listings and when the full content page is loaded.',
		'type' => 'toggle',
	),
	"group_in_view" => array(
		'label' => 'Bottom of Page',
		'note' => 'Shows a link to the group below the body text. Only visible when the full content page is loaded.',
		'type' => 'toggle',
	),
);
$gBitSmarty->assign( 'formGroupServiceDisplayOptions', $formGroupServiceDisplayOptions );

// store the prefs
if( !empty( $_REQUEST['group_preferences'] ) ) {
	foreach( $formGroupServiceDisplayOptions as $item => $data ) {
		if( $data['type'] == 'numeric' ) {
			simple_set_int( $item, GROUP_PKG_NAME );
		} elseif( $data['type'] == 'toggle' ) {
			simple_set_toggle( $item, GROUP_PKG_NAME );
		} elseif( $data['type'] == 'input' ) {
			simple_set_value( $item, GROUP_PKG_NAME );
		}
	}

	$groupToggles = array_merge( $formGroupLists );
	foreach( $groupToggles as $item => $data ) {
		simple_set_toggle( $item, GROUP_PKG_NAME );
	}

	foreach( array_keys( $formGroupContent['guids'] ) as $types ) {
		$gBitSystem->storeConfig( $types, ( ( !empty( $_REQUEST['group_content'] ) && in_array( $types, $_REQUEST['group_content'] ) ) ? 'y' : NULL ), GROUP_PKG_NAME );
	}
}

// check the correct packages in the package selection
foreach( $gLibertySystem->mContentTypes as $cType ) {
	if( $gBitSystem->getConfig( 'group_content_'.$cType['content_type_guid'] ) ) {
		$formGroupContent['checked'][] = 'group_content_'.$cType['content_type_guid'];
	}
}
$gBitSmarty->assign( 'formGroupContent', $formGroupContent );

$group = new BitGroup();
$groups = $group->getList( $_REQUEST );
$gBitSmarty->assign_by_ref('groups', $groups['data']);
?>
