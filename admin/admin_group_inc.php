<?php
// $Header: /cvsroot/bitweaver/_bit_groups/admin/admin_group_inc.php,v 1.12 2008/04/07 18:50:39 wjames5 Exp $
// Copyright (c) 2008 bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// is this used?
//if (isset($_REQUEST["groupset"]) && isset($_REQUEST["homeGroup"])) {
//	$gBitSystem->storeConfig("home_group", $_REQUEST["homeGroup"]);
//	$gBitSmarty->assign('home_group', $_REQUEST["homeGroup"]);
//}

require_once( GROUP_PKG_PATH.'BitGroup.php' );

$formGroupFeatures = array(
	"group_themes" => array(
		'label' => 'Group Theming',
		'note' => 'Enable groups to custom theme their group',
	),
	"group_layouts" => array(
		'label' => 'Group Layouts',
		'note' => 'Enable groups to customize their layout',
	),
	"group_admin_content" => array(
		'label' => 'Allow Linked Content Administration',
		'note' => 'Enable groups to have administrative control over content that has been linked to them. This will limit content to be joinable to only one group.',
	),
);
$gBitSmarty->assign( 'formGroupFeatures',$formGroupFeatures );

$formGroupEmailList = array(
	"group_email_list" => array(
		'label' => 'Group Email List',
		'note' => 'Enable groups to have an associated email list',
	),
);
$gBitSmarty->assign( 'formGroupEmailList',$formGroupEmailList );
$formGroupEmailText = array( 'group_email_host', 'group_email_admin', 'group_email_mailman_bin' );

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

// get data for module preferences
$allModules = $gBitThemes->getAllModules();
ksort( $allModules );
foreach( $allModules as $package=>$modules ){
	foreach( $modules as $tpl=>$desc ){
		$ref = 'group_mod_'.strtolower($package)."_".( str_replace( ' ', '_', $desc ) );
		$formGroupModules[$package][$ref] = $desc;
	}
}
$gBitSmarty->assign_by_ref( 'formGroupModules', $formGroupModules );

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

	$groupToggles = array_merge( $formGroupLists, $formGroupFeatures,$formGroupEmailList );
	foreach( $groupToggles as $item => $data ) {
		simple_set_toggle( $item, GROUP_PKG_NAME );
	}

	foreach( $formGroupEmailText as $text ) {
		$gBitSystem->storeConfig( $text, ( !empty( $_REQUEST[$text] ) ? trim( $_REQUEST[$text] ) : NULL ), GROUP_PKG_NAME );
	}
	
	foreach( array_keys( $formGroupContent['guids'] ) as $type ) {
		$gBitSystem->storeConfig( $type, ( ( !empty( $_REQUEST['group_content'] ) && in_array( $type, $_REQUEST['group_content'] ) ) ? 'y' : NULL ), GROUP_PKG_NAME );
	}
	
	foreach( $formGroupModules as $package ){
		foreach( $package as $conf => $desc ){
			$gBitSystem->storeConfig( $conf, ( ( !empty( $_REQUEST['group_modules'] ) && in_array( $conf, $_REQUEST['group_modules'] ) ) ? 'y' : NULL ), GROUP_PKG_NAME );
		}
	}

}

// check the correct packages in the package selection
foreach( $gLibertySystem->mContentTypes as $cType ) {
	if( $gBitSystem->getConfig( 'group_content_'.$cType['content_type_guid'] ) ) {
		$formGroupContent['checked'][] = 'group_content_'.$cType['content_type_guid'];
	}
}
$gBitSmarty->assign( 'formGroupContent', $formGroupContent );

// check allowed modules
foreach( $formGroupModules as $package ){
	foreach( $package as $conf=>$desc ){
		if ( $gBitSystem->getConfig( $conf ) ){
			$formGroupModules['checked'][] = $conf;
		}
	}
}

/* Seems like this is here for no reason 
$group = new BitGroup();
$groups = $group->getList( $_REQUEST );
$gBitSmarty->assign_by_ref('groups', $groups['data']);
 */

?>
