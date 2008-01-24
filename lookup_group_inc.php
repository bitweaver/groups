<?php
global $gContent;
require_once( GROUPS_PKG_PATH.'BitGroup.php');
require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	// if group_id supplied, use that
	if( @BitBase::verifyId( $_REQUEST['group_id'] ) ) {
		$gContent = new BitGroup( $_REQUEST['group_id'] );

	// if content_id supplied, use that
	} elseif( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
		$gContent = new BitGroup( NULL, $_REQUEST['content_id'] );

	} elseif (@BitBase::verifyId( $_REQUEST['group']['group_id'] ) ) {
		$gContent = new BitGroup( $_REQUEST['group']['group_id'] );

	// otherwise create new object
	} else {
		$gContent = new BitGroup();
	}

	$gContent->load();
	$gBitSmarty->assign_by_ref( "gContent", $gContent );
}
?>
