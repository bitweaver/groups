<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/moderation_inc.php,v 1.4 2009/10/01 13:45:40 wjames5 Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * 
 * @package groups
 * @subpackage functions
 */
 
/**
 * load up moderation
 * we need to include its bit_setup_inc incase groups gets loaded first
 */
if ( is_file( BIT_ROOT_PATH.'moderation/bit_setup_inc.php' ) ){
	require_once( BIT_ROOT_PATH.'moderation/bit_setup_inc.php' );
}

if( $gBitSystem->isPackageActive('moderation') &&
	!defined('groups_moderation_callback') ) {
	global $gModerationSystem;

	require_once(MODERATION_PKG_PATH.'ModerationSystem.php');

	// What are our transitions
	$groupTransitions = array( "join" =>
							   array (MODERATION_PENDING =>
									  array(MODERATION_APPROVED,
											MODERATION_REJECTED),
									  MODERATION_REJECTED => MODERATION_DELETE,
									  MODERATION_APPROVED => MODERATION_DELETE,
									  ),
							   "invite" =>
							   array (MODERATION_PENDING =>
									  array(MODERATION_APPROVED,
											MODERATION_REJECTED),
									  MODERATION_REJECTED => MODERATION_DELETE,
									  MODERATION_APPROVED => MODERATION_DELETE,
									  ),
							   "add_content" =>
							   array (MODERATION_PENDING =>
									  array(MODERATION_APPROVED,
											MODERATION_REJECTED),
									  MODERATION_REJECTED => MODERATION_DELETE,
									  MODERATION_APPROVED => MODERATION_DELETE,
									  ),
							   );

	function groups_moderation_callback(&$pModeration) {
		global $gBitUser, $gBitSystem;

		if ($pModeration['type'] == 'join') {
			if ($pModeration['status'] == MODERATION_APPROVED) {
				// Add the user to the group
				$gBitUser->addUserToGroup( $pModeration['source_user_id'], $pModeration['moderator_group_id'] );
				// Store the users notification preference
				if ($gBitSystem->isPackageActive('switchboard') &&
					!empty($pModeration['data']['notice'])) {
					if ($pModeration['data']['notice'] == 'email' ||
						$pModeration['data']['notice'] == 'digest') {
						global $gSwitchboardSystem;
						$gSwitchboardSystem->storeUserPref($pModeration['source_user_id'], 'group', 'message', $pModeration['content_id'], $pModeration['data']['notice']);
					}
				}
			}
		}
		else if ($pModeration['type'] == 'invite') {
			if ($pModeration['status'] == MODERATION_APPROVED) {
				// Add the user to the group
				$group = new BitGroup(NULL, $pModeration['content_id']);
				$group->load();
				$gBitUser->addUserToGroup( $pModeration['moderator_id'], $group->mGroupId);
			}
		}
		else if ($pModeration['type'] == 'add_content') {
			if ($pModeration['status'] == MODERATION_APPROVED) {
				if ( !empty( $pModeration['data']['map_content_id'] ) ){
					$group = new BitGroup(NULL, $pModeration['content_id']);
					$group->load();
					$group->linkContent( array( "content_id" => $pModeration['data']['map_content_id'] ) );
				}
				// @TODO would be nice to be able to kick an error or msg back to the moderation system
			}
		}

		return TRUE;
	}

	// Register our moderation transitions
	$gModerationSystem->registerModerationListener('group',
												   'groups_moderation_callback',
												   $groupTransitions);
}

?>
