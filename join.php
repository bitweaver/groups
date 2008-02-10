<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
$gContent->verifyViewPermission();

// verify this group is public

// make sure the user is registered
if ( !$gBitUser->isRegistered() ){
	$gBitSystem->fatalError( tra( 'You must be registed to join groups.' ));	
}

// if join is confirmed then go for it
if( !empty( $_REQUEST["join_group"] ) ) {
	// @TODO join process
}

// display
$gBitSystem->display( 'bitpackage:group/join_group.tpl', tra('Join')." ".$gContent->getTitle() );
?>
