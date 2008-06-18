<?php
/**
 * @version $Header:
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * 
 * @package groups
 * @subpackage functions
 */

/**
 * What options do groups support?
 */ 
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
