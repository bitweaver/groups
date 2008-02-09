<?php
global $gBitSystem;

$registerHash = array(
	'package_name' => 'group',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'group' ) ) {
	$menuHash = array(
		'package_name'  => GROUP_PKG_NAME,
		'index_url'     => GROUP_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:group/menu_group.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );
}
?>