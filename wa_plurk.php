<?php
/*
Plugin Name: Wa Plurk Updater Plugin
Plugin URI: http://wordpress.org/extend/plugins/wa-plurk-updater/
Description: A wordpress plugin that posts any published post to your plurk account with an option to set what kind of text you want to be posted in Plurk.
Version: 1.0.7
Author: Diwa G. Fernandez
Author URI: http://diwafernandez.com
License: GPLv2
*/
/* Copyright 2011 Diwa Fernandez (email: axeli at gmail.com)

This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/
define('WAPLURK_VERSION', '1.0.1');
define ('WAPLURK_API_KEY', 'xtGYl72cAySSAYwyZQNWB2D86ty21l1Y'); // api key to connect to PLURK API *DO NOT DELETE*

// ADMIN PANEL - under Manage menu
function df_addWaPlurkAdminPages() {
    if (function_exists('add_management_page')) {
		 add_management_page('Plurk Updater', 'Plurk Updater', 8, __FILE__, 'df_Plurk_manage_page');
    }
 }

function df_Plurk_manage_page() {
    include(dirname(__FILE__).'/admin/admin.php');
}

require(dirname(__FILE__).'/api/plurk_api.php');

function df_doPlurkAPIPost($psay,$ptext){
global $plurk;
//check if user login details have been entered on admin page
	$thisLoginDetails = get_option('plurklogin_encrypted');
	if ($thisLoginDetails != '') {
		//$pairs = explode("&", $str)
		$plurk = new plurk_api();
		$pairs = explode("[]",base64_decode($thisLoginDetails));
		$username = $pairs[0];
		$password = $pairs[1];
		$api_key = WAPLURK_API_KEY; // api key to connect to PLURK API *DO NOT DELETE*
		$plurk->login($api_key, $username, $password);
		$disName = $profile->user_info->display_name;
		$plurk->add_plurk('en', $psay, $ptext);
		$plurk->logout();
	}
}

if (!function_exists('wa_plurk')) {
	function wa_plurk($post_ID) {
	   $thisposttitle = $_POST['post_title'];
	   $thispostlink = get_permalink($post_ID);
	   $sentence = '';
	/*if (($get_post_info->post_status == 'publish' || $_POST['publish'] == 'Publish') && ($_POST['prev_status'] == 'draft' || $_POST['original_post_status'] == 'draft' || $_POST['prev_status'] == 'pending' || $_POST['original_post_status'] == 'pending')) {*/
	//is new post
	if($_POST['action'] !== 'autosave'){

		if($get_post_info->post_status == 'publish' || $_POST['publish'] == 'Publish'){
			// publish new post
			if(get_option('newpost-published-update') == '1'){
				$sentence = get_option('newpost-published-text');
				$say = get_option('newpost-published-say');
				if(get_option('newpost-published-showlink') == '1'){
					$thisposttitle = $thispostlink . ' ( ' . $thisposttitle . ' )';
				}
				$sentence = str_replace ( '#title#', $thisposttitle, $sentence);
			}
		}else if($_POST['action'] == 'post'){
			// create new post
			if(get_option('newpost-created-update') == '1'){
				$sentence = get_option('newpost-created-text');
				$say = get_option('newpost-created-say');
				$sentence = str_replace ( '#title#', $thisposttitle, $sentence);
			}
		}else{
			// edit new post
			if(get_option('newpost-edited-update') == '1'){
				$sentence = get_option('newpost-edited-text');
				$say = get_option('newpost-edited-say');
				$sentence = str_replace ( '#title#', $thisposttitle, $sentence);
			}
		}
	}else{
		// is old post
		if(get_option('oldpost-edited-update') == '1'){
			$sentence = get_option('oldpost-edited-text');
			$say = get_option('oldpost-edited-say');
			if(get_option('oldpost-edited-showlink') == '1'){
				$thisposttitle = $thispostlink . ' ( ' . $thisposttitle . ' )';
			}
			$sentence = str_replace ( '#title#', $thisposttitle, $sentence);
		}
	}
		
		if($sentence != ''){
			df_doPlurkAPIPost($say,$sentence);
		}
	
	}
}
// Removes information when plugin is deactivated
function wa_plurk_remove () {
	delete_option('waplurkInitialised');
	delete_option('plurklogin');
	delete_option('plurklogin_encrypted');
}
//HOOKIES
add_action ( 'save_post', 'wa_plurk');

add_action('admin_menu', 'df_addWaPlurkAdminPages');

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'wa_plurk_remove' );

?>
