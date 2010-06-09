<?php
/**
 * @version $Header$
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * @author Will James, Tekimaki LCC <will@tekimaki.com>
 * 
 * @package groups
 * @subpackage functions
 */

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
