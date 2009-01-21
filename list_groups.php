<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/Attic/list_groups.php,v 1.7 2009/01/21 22:10:11 tekimaki_admin Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * @author Will James, Tekimaki LCC <will@tekimaki.com>
 * 
 * @package group
 * @subpackage functions
 */
 
/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );
require_once( GROUP_PKG_PATH.'BitGroup.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_group_view' );

/* mass-remove:
	the checkboxes are sent as the array $_REQUEST["checked[]"], values are the group id,
	e.g. $_REQUEST["checked"][3]="69"
	$_REQUEST["submit_mult"] holds the value of the "with selected do..."-option list
	we look if any page's checkbox is on and if remove_groups is selected.
	then we check permission to delete groups.
	if so, we call histlib's method remove_all_versions for all the checked groups.
*/

if( isset( $_REQUEST["submit_mult"] ) && isset( $_REQUEST["checked"] ) && $_REQUEST["submit_mult"] == "remove_groups" ) {

	// Now check permissions to remove the selected groups
	$gBitSystem->verifyPermission( 'p_group_remove' );

	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['delete'] = TRUE;
		$formHash['submit_mult'] = 'remove_groups';
		foreach( $_REQUEST["checked"] as $del ) {
			$tmpPage = new BitGroup( $del);
			if ( $tmpPage->load() && !empty( $tmpPage->mInfo['title'] )) {
				$info = $tmpPage->mInfo['title'];
			} else {
				$info = $del;
			}
			$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>'.$info;
		}
		$gBitSystem->confirmDialog( $formHash, array( 
				'warning' => tra('Are you sure you want to delete these groups?') . ' (' . tra('Count: ') . count( $_REQUEST["checked"] ) . ')',				
				'error' => tra('This cannot be undone!'),
			)
		);
	} else {
		foreach( $_REQUEST["checked"] as $deleteId ) {
			$tmpPage = new BitGroup( $deleteId );
			if( !$tmpPage->load() || !$tmpPage->expunge() ) {
				array_merge( $errors, array_values( $tmpPage->mErrors ) );
			}
		}
		if( !empty( $errors ) ) {
			$gBitSmarty->assign_by_ref( 'errors', $errors );
		}
	}
}

// create new group object
$group = new BitGroup();
$groupsList = $group->getList( $_REQUEST );
$gBitSmarty->assign_by_ref( 'groupsList', $groupsList );

// getList() has now placed all the pagination information in $_REQUEST['listInfo']
$gBitSmarty->assign_by_ref( 'listInfo', $_REQUEST['listInfo'] );

// Display the template
$gBitSystem->display( 'bitpackage:group/list_groups.tpl', tra( 'Group' ) , array( 'display_mode' => 'list' ));

?>
