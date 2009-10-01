<?php
/**
 * @version $Header:
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * 
 * @package groups
 * @subpackage functions
 */

/**
 * What options do groups support?
 */ 

// boards / mailman mailing list crap - need a place to display it for the user, this seems as fine a place as any for now
if( $gContent->isValid() 
	&& ( $gContent->hasUpdatePermission() || $gContent->hasUserPermission( 'p_group_group_msgs_admin' ) ) 
	&& $gBitSystem->getConfig( 'boards_sync_mail_server' ) 
	&& ( $board = $gContent->getBoard() ) ){
	$gBitSmarty->assign( 'mailinglist_pwd', $board->getPreference( 'boards_mailing_list_password' ) );
}

$formGroupOptions = array(
	"view_content_public" => array(
		'label' => 'Publicly Viewable',
		'note' => 'When checked, this group and its content is publically viewable.',
		'default' => 'y',
	),
	"is_public" => array(
		'label' => 'Public Membership',
		'note' => 'When checked, anyone is free to join this group. When not checked, an invitation will be required to join this group.',
		'default' => 'y',
	),
	"mod_msgs" => array(
		'label' => 'Moderate Messages',
		'note' => 'When checked, all messages to this group will be held for moderation before being displayed.',
		'default' => 'n',
	),
	"mod_content" => array(
		'label' => 'Moderate Content',
		'note' => 'When checked, all content created (like pages or blog posts) will be held for moderation before being displayed.',
		'default' => 'n',
	),
);

if ( $gBitSystem->isFeatureActive( 'group_admin_content' ) ){
	$formGroupOptions['admin_content_strict'] = array(
		'label' => 'Assert Administrative Control of Joined Content',
		'note' => 'When checked, group administrators can assert administrative control over joined content. When not checked, default content administration permissions apply.',
		'default' => 'n',
	);
}
	
$gBitSmarty->assign('formGroupOptions', $formGroupOptions);
?>
