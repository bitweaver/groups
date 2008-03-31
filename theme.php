<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin to edit an existing group 
if( $gContent->isValid() ) {
	$gContent->verifyEditPermission();
}else{
	$gBitSystem->fatalError( tra( 'The Group, whose theme you are attempting to administrate, does not exist' ));
}

// apply the site style
if( !empty( $_REQUEST["group_style"] ) ) {
	if( !empty( $_REQUEST['approved'] ) ) {
		$gContent->storePreference( 'theme', $_REQUEST["group_style"] );
		$gContent->storePreference( 'theme_variation', !empty( $_REQUEST["style_variation"] ) ? $_REQUEST["style_variation"] : '' );
		$gBitSystem->setConfig( 'style_variation', !empty( $_REQUEST["style_variation"] ) ? $_REQUEST["style_variation"] : '', GROUP_PKG_NAME );
		$gBitThemes->setStyle( $_REQUEST["group_style"] );
	} else {
		$gBitSystem->setConfig( 'style_variation', !empty( $_REQUEST["style_variation"] ) ? $_REQUEST["style_variation"] : '', GROUP_PKG_NAME );
		$gBitSmarty->assign( 'approve', TRUE );
		$gBitThemes->setStyle( $_REQUEST["group_style"] );
	}
}

// Get list of available styles
$styles = $gBitThemes->getStyles( NULL, TRUE );
$gBitSmarty->assign_by_ref( "styles", $styles );

$subDirs = array( 'style_info', 'alternate' );
$stylesList = $gBitThemes->getStylesList( NULL, NULL, $subDirs );
$gBitSmarty->assign_by_ref( "stylesList", $stylesList );

// display
$gBitSystem->setBrowserTitle( tra( 'Select theme for group'." ".$gContent->getTitle() ) );
$gBitSystem->display( "bitpackage:group/edit_group_theme.tpl" ); ?>
