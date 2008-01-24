<?php
$tables = array(
	'groups' => "
		group_id I4 AUTO PRIMARY,
		content_id I4 NOTNULL,
		description C(160)
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( GROUPS_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( GROUPS_PKG_NAME, array(
	'description' => "Group package to demonstrate how to build a bitweaver package.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Indexes
$indices = array(
	'bit_groups_group_id_idx' => array('table' => 'groups', 'cols' => 'group_id', 'opts' => NULL ),
);
$gBitInstaller->registerSchemaIndexes( GROUPS_PKG_NAME, $indices );

/*// ### Sequences
$sequences = array (
	'bit_group_id_seq' => array( 'start' => 1 )
);
$gBitInstaller->registerSchemaSequences( GROUPS_PKG_NAME, $sequences );
*/


$gBitInstaller->registerSchemaDefault( GROUPS_PKG_NAME, array(
	//      "INSERT INTO `".BIT_DB_PREFIX."bit_group_types` (`type`) VALUES ('Group')",
) );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( GROUPS_PKG_NAME, array(
	array( 'p_group_admin', 'Can admin group', 'admin', GROUPS_PKG_NAME ),
	array( 'p_group_create', 'Can create a group', 'registered', GROUPS_PKG_NAME ),
	array( 'p_group_edit', 'Can edit any group', 'editors', GROUPS_PKG_NAME ),
	array( 'p_group_view', 'Can view group', 'basic',  GROUPS_PKG_NAME ),
	array( 'p_group_remove', 'Can delete group', 'admin',  GROUPS_PKG_NAME ),
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( GROUPS_PKG_NAME, array(
	array( GROUPS_PKG_NAME, 'group_default_ordering', 'group_id_desc' ),
	array( GROUPS_PKG_NAME, 'group_list_group_id', 'y' ),
	array( GROUPS_PKG_NAME, 'group_list_title', 'y' ),
	array( GROUPS_PKG_NAME, 'group_list_description', 'y' ),
	array( GROUPS_PKG_NAME, 'group_list_groups', 'y' ),
) );
?>
