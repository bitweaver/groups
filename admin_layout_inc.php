<?php
// $Header: /cvsroot/bitweaver/_bit_groups/admin_layout_inc.php,v 1.1 2008/04/02 23:02:55 wjames5 Exp $

// Initialization
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

// data submitted lets handle it
if ( isset( $_REQUEST['submitcolumns'] ) ){
	foreach( $fAssign as $moduleHash ){
		$moduleHash['layout'] = $layout_name;
		// we first delete any existing assigned module, then we put back the ones we want to keep or change
		if( isset( $moduleHash['module_id'] ) ){
			$gBitThemes->unassignModule( $moduleHash['module_id'] );
			unset( $moduleHash['module_id'] );
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
	// in storage situations we reload the layout settings so we have up to date data
	$layout = $gBitThemes->getLayout( $layoutHash );
}else if ( isset( $_REQUEST['submitcenter'] ) ){
	// @TODO handle center assignments
	// in storage situations we reload the layout settings so we have up to date data
	$layout = $gBitThemes->getLayout( $layoutHash );
}

/* BONEYARD - we might use this for pos assignments and center module assignments
if( isset( $_REQUEST['module_id'] ) && !empty( $_REQUEST['move_module'] )) {
	if( isset( $_REQUEST['move_module'] )) {
		switch( $_REQUEST['move_module'] ) {
			case "unassign":
				$gBitThemes->unassignModule( $_REQUEST['module_id'] );
				break;
			case "up":
				$gBitThemes->moveModuleUp( $_REQUEST['module_id'] );
				break;
			case "down":
				$gBitThemes->moveModuleDown( $_REQUEST['module_id'] );
				break;
			case "left":
				$gBitThemes->moveModuleToArea( $_REQUEST['module_id'], 'l' );
				break;
			case "right":
				$gBitThemes->moveModuleToArea( $_REQUEST['module_id'], 'r' );
				break;
		}
	}
} elseif( !empty($processForm) && ( $processForm == 'Center' || $processForm == 'Column' )) {
	$fAssign = &$_REQUEST['fAssign'];
	$fAssign['layout'] = $layout;
	$gBitThemes->storeModule( $fAssign );
}
*/

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
				$assignedModules[$module['module_rsrc']] = array(	'module_id' => $module['module_id'], 
																	'layout_area' => $module['layout_area'],
			   														'pos' => $module['pos']	);
			}
		}
	}
}
$gBitSmarty->assign_by_ref( 'assignedModules', $assignedModules );

$gBitSmarty->assign_by_ref( 'layoutAreas', $layoutAreas );

$allModules = $gBitThemes->getAllModules();
ksort( $allModules );
$gBitSmarty->assign_by_ref( 'allModules', $allModules );

$allCenters = $gBitThemes->getAllModules( 'templates', 'center_' );
ksort( $allCenters );
$gBitSmarty->assign_by_ref( 'allCenters', $allCenters );

?>
