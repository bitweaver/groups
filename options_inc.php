<?php
// What options do groups support?
$formGroupOptions = array(
	"view_content_public" => array(
		'label' => 'Publicly Viewable',
		'note' => 'Is this group and its content publically viewable?',
		'default' => 'y',
	),
	"is_public" => array(
		'label' => 'Public Membership',
		'note' => 'Is anyone free to join this group? Unchecking this box means an invitation will be required to join this group',
		'default' => 'y',
	),
	"mod_msgs" => array(
		'label' => 'Moderate Messages',
		'note' => 'When checked all messages to this group will be held for moderation before being displayed.',
		'default' => 'n',
	),
	"mod_content" => array(
		'label' => 'Moderate Content',
		'note' => 'When checked any content created (like pages or blog posts) wiil be held for moderation before being displayed',
		'default' => 'n',
	),
	"admin_content_strict" => array(
		'label' => 'Admin Edits All',
		'note' => 'Can the administrator edit any content assigned to the group?',
		'default' => 'n',
	),
);
$gBitSmarty->assign('formGroupOptions', $formGroupOptions);
?>
