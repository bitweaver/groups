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

	$gLibertySystem->registerService( LIBERTY_SERVICE_GROUP, GROUP_PKG_NAME, array(
		'module_display_function'  => 'group_module_display',
		'content_list_sql_function' => 'group_content_list_sql',
		'content_preview_function'  => 'group_content_preview',
		'content_store_function'  => 'group_content_store',
		'content_expunge_function'  => 'group_content_expunge',
		'content_user_perms_function' => 'group_content_user_perms',
	) );

	require_once( 'BitGroup.php' );
}
?>
