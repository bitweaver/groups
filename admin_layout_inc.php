<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/admin_layout_inc.php,v 1.4 2008/06/18 13:18:20 lsces Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * 
 * $Id: admin_layout_inc.php,v 1.4 2008/06/18 13:18:20 lsces Exp $
 * @package groups
 * @subpackage functions
 */
 
/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

$gBitSmarty->assign_by_ref( 'feedback', $feedback = array() );

$layout_name = "content_id.".$gContent->mContentId;
$layoutHash = array(
	'layout' => $layout_name,
	'fallback' => "group",
);
$layout = $gBitThemes->getLayout( $layoutHash );

// This came over from Themes layouts - breaks, not sure if its important
// $gBitSystem->verifyInstalledPackages();

if ( !empty( $_REQUEST['fAssign'] ) ){
	$fAssign = &$_REQUEST['fAssign'];
}


$groupParam = "connect_group_content_id=".$gContent->mContentId; 
// data submitted lets handle it
if ( isset( $_REQUEST['submitcolumns'] ) ){
	foreach( $fAssign as $moduleHash ){
		$moduleHash['layout'] = $layout_name;
		// we first delete any existing assigned module, then we put back the ones we want to keep or change
		if( isset( $moduleHash['module_id'] ) ){
			$gBitThemes->unassignModule( $moduleHash['module_id'] );
			unset( $moduleHash['module_id'] );
		}
		// force in connect_group_content_id so that we're always joining to group stuff.
		if( !strpos( $moduleHash['params'], $groupParam ) ){
			$moduleHash['params'] .= $groupParam;
		}
		switch ( $moduleHash['layout_area'] ){
			case "unassign":
				break;
			case "l":
			case "r":
				$gBitThemes->storeModule( $moduleHash );
				break;
		}
	}
	$reload = TRUE;
}else if ( isset( $_REQUEST['submitcenter'] ) ){
	// handle center assignments
	$moduleHash = $fAssign;
	$moduleHash['layout'] = $layout_name;
	// force in connect_group_content_id so that we're always joining to group stuff.
	if( !strpos( $moduleHash['params'], $groupParam ) ){
		$moduleHash['params'] .= $groupParam;
	}
	$gBitThemes->storeModule( $moduleHash );
	$reload = TRUE;
}else if ( isset( $_REQUEST['changecenter'] ) ){
	foreach( $fAssign as $moduleHash ){
		$moduleHash['layout'] = $layout_name;
		// force in connect_group_content_id so that we're always joining to group stuff.
		if( !strpos( $moduleHash['params'], $groupParam ) ){
			$moduleHash['params'] .= $groupParam;
		}
		$gBitThemes->storeModule( $moduleHash );
	}
	$reload = TRUE;
}else if ( isset( $_REQUEST['unassigncenter'] ) ){
	$gBitUser->verifyTicket();
	if( isset( $_REQUEST['module_id'] ) ){
		$gBitThemes->unassignModule( $_REQUEST['module_id'] );
		$reload = TRUE;
	}
}


if ( isset( $reload ) ){
	// after edit changes we need to relaod the layout so we have them 
	$layout = $gBitThemes->getLayout( $layoutHash );
}

$gBitThemes->generateModuleNames( $layout );
$gBitSmarty->assign_by_ref( 'layout', $layout );

/**
 * this is ugly, but it works for the purpose of only allowing 
 * assignment of one instance of each module - which is 
 * much easier for lay people to understand.
 * we need to know which of all the possible modules have 
 * been assigned so that we can get a module_id while still 
 * using a table format to edit them all at once.
 * 
 * if you have a better idea by all means suggest it -wjames5
 */
foreach( $layout as $area ){
	if ( !empty( $area ) ){
		foreach( $area as $module ){
			if ( $module['layout'] == $layout_name ){
			   	if( $module['layout_area'] != "c" ){
					$assignedModules[$module['module_rsrc']] = $module; 
				}else{
					$centerAssignedModules[$module['module_rsrc']] = $module;
				}
			}
		}
	}
}

$gBitSmarty->assign_by_ref( 'assignedModules', $assignedModules );
$gBitSmarty->assign_by_ref( 'centerAssignedModules', $centerAssignedModules );

$gBitSmarty->assign_by_ref( 'layoutAreas', $layoutAreas );

$allModules = $gBitThemes->getAllModules();
ksort( $allModules );
$gBitSmarty->assign_by_ref( 'allModules', $allModules );

// we only allow the modules that the site admin has allowed for groups
foreach( $allModules as $package=>$modules ){
	foreach( $modules as $tpl=>$desc ){
		$conf = 'group_mod_'.strtolower($package)."_".( str_replace( ' ', '_', $desc ) );
		if ( $gBitSystem->getConfig( $conf ) ){
			$allowedModules[$package][$tpl] = $desc;
		}
	}
}
$gBitSmarty->assign_by_ref( 'allowedModules', $allowedModules );

$allCenters = $gBitThemes->getAllModules( 'templates', 'center_' );
ksort( $allCenters );
$gBitSmarty->assign_by_ref( 'allCenters', $allCenters );

?>
