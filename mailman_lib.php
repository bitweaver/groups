<?php
// $Header: /cvsroot/bitweaver/_bit_groups/Attic/mailman_lib.php,v 1.1 2008/04/06 22:38:50 spiderr Exp $
// Copyright (c) bitweaver Group
// All Rights Reserved.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
* mailman lib 
* library of functions to manipulate mailman lists
*
* @date created 2008-APR-06
* @author wjames <will@tekimaki.com> spider <spider@viovio.com>
* @version 
* @class BitGroup
*/

function mailman_verify_list( $pListName ) {
	$error = NULL;
	if( $matches = preg_match( '/[^A-Za-z0-9]/', $pListName ) ) {
		$error = tra( 'Invalid mailing list name' ).': '.tra( 'List names can only contain letters and numbers' );
	} else {
		$lists = mailman_list_lists();
		if( !empty( $lists[strtolower($pListName)] ) ) {
			$error = tra( 'Invalid mailing list name' ).': '.tra( 'List already exists' );
		}
	}
	return $error;
}

function mailman_list_lists() {
	$ret = array();
	if( $output = mailman_command( 'list_lists' ) ) {
		foreach( $output as $o ) {
			if( strpos( $o, '-' ) ) {
				list( $name, $desc ) = split( '-', $o );
				$ret[strtolower( trim( $name ) )] = trim( $desc );
			}
		}
	}
	return( $ret );
}

function mailman_newlist( $pCommand ) {
bt(); die;
	$ret = FALSE;
}

function mailman_rmlist( $pCommand ) {
	$ret = FALSE;
}

function mailman_command( $pCommand, $pOptions=NULL ) {
	global $gBitSystem;
	$ret = NULL;
	$fullCommand = $gBitSystem->getConfig( 'group_email_mailman_bin' ).'/'.$pCommand;
	$fullCommand = str_replace( '//', '/', $fullCommand );
	if( file_exists( $fullCommand ) ) {
		exec( $fullCommand, $ret );
	} else {
		bit_log_error( tra( 'Groups mailman command failed.' ).tra( 'File not found' ).': '.$fullCommand );
	}
	return $ret;
}

?>
