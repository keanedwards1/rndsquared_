<?php
/*
	Plugin Name: Flash API
	Description: This plugin serves as a faux webservice that outputs data from the WP Database to a flash application
	Version: 1.0.3
	Author: Cameron Tullos - Illumifi Interactive
	Author URI: http://illumifi.net/
*/
/*  
	Copyright 2010  Illumifi Interactive  (email: c.tullos at illumifi dot net)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
	
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

	// Definitions
	define('FAPI_PLUGIN_URL', WP_PLUGIN_URL . '/flash-api');
	define('FAPI_PLUGIN_DIR', WP_PLUGIN_DIR . '/flash-api');
	define('SITE', get_bloginfo('wpurl') . '/'); 
	define('FAPI_DEFAULT_KEY', '1e7abba6c6b8b6aead1d57ffcf4c9943'); 
	define('FAPI_DEFAULT_TAG', 'Flash API'); 
	
	// Include jquery
	wp_enqueue_script('jquery');

	// Init
	add_action('plugins_loaded', 'flash_api_init');
	
	function flash_api_init() {
		$apiKey = get_option('flash_api_key');

		$codexTag = get_option('flash_api_tag'); 

		if (!$apiKey) { $apiKey = add_option('flash_api_key', FAPI_DEFAULT_KEY); }
		if (!$codexTag || strlen($codexTag) < 3) { 
			$codexTag = FAPI_DEFAULT_TAG;
			add_option('flash_api_tag', FAPI_DEFAULT_TAG); 
		}

		define('FAPI_KEY', $apiKey);
		define('FAPI_TAG', $codexTag);
				
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->terms." WHERE name = '".$codexTag."'");
		$row = $wpdb->get_row($sql);
		$id = $row->term_id;

		if (!$id) { 
			$sql = $wpdb->prepare("INSERT INTO ".$wpdb->terms." (name, slug, term_group) VALUES ('".$codexTag."', '".$codexTag."', 0)");
			$wpdb->query($sql);
			$id = $wpdb->insert_id;
			$sql = $wpdb->prepare("INSERT INTO ".$wpdb->term_taxonomy." (term_id, taxonomy) VALUES (".$id.", 'category')");
			$wpdb->query($sql);
		}

		$style = FAPI_PLUGIN_URL . '/style.css';
		wp_register_style('flashapiStyleSheet', $style);
	}

	// Menu
	add_action('admin_menu', 'flash_api_menu');
	function flash_api_menu() {
		$opt_plugin = add_options_page('Flash API', 'Flash API', 'administrator', __FILE__, 'flash_api_form');
		add_action('admin_print_scripts-'.$opt_plugin, 'flash_api_scripts'); 
		add_action('admin_print_styles-' . $opt_plugin, 'flash_api_style');
	}

	// load scripts
	function flash_api_scripts() { 
		wp_enqueue_script('jquery');
		wp_enqueue_script('md5', FAPI_PLUGIN_URL . '/js/MD5.js');
		wp_register_script('md5', FAPI_PLUGIN_URL . '/js/MD5.js');
		wp_enqueue_script('flash_api', FAPI_PLUGIN_URL . '/js/flash_api.js', array('jquery', 'md5'));
		wp_register_script('flash_api', FAPI_PLUGIN_URL . '/js/flash_api.js');
	}
	
	// load styles
	function flash_api_style() { 
		wp_enqueue_style('flashapiStyleSheet'); 
	}
	
	// draw the admin form
	function flash_api_form() { 
		$path = FAPI_PLUGIN_DIR . '/flash_api_admin_form.html';
		$func = FAPI_PLUGIN_URL . '/wsrv.php';
		$html = implode('', file($path));		
		$api_key = get_option('flash_api_key');
		$docs = flash_api_docs(); 
		$help = flash_api_help(0);
		$cdx = ($docs) ? __('Codex') : '';
		$warning = (FAPI_KEY == FAPI_DEFAULT_KEY) ? flash_api_help(1) : ''; 
		$tag = FAPI_TAG;

		$html = str_replace('[API_KEY]', __('Application key'), $html);
		$html = str_replace('[API_KEY_VALUE]', $api_key, $html);
		$html = str_replace('[API_TAG]', __($tag), $html);
		$html = str_replace('[CODEX]', $cdx, $html);
		$html = str_replace('[DOCS]', $docs, $html);
		$html = str_replace('[FUNCTION_FILE]', $func, $html);
		$html = str_replace('[GENERATE]', __('Generate'), $html);
		$html = str_replace('[HELP]', __($help), $html);
		$html = str_replace('[REQUEST_URI]', 'options.php', $html); 
		$html = str_replace('[TITLE]', __('Flash API Settings'), $html); 
		$html = str_replace('[UPDATE]', __('Update'), $html); 
		$html = str_replace('[WARNING]', __($warning), $html);
		
		$html = explode('[WPNOUNCE]', $html);

		echo $html[0];

		wp_nonce_field('update-options');

		echo $html[1];		
	}

	
	// help information
	function flash_api_help($id) { 
		$help = array();
		array_push($help, "Create an Application Key below. This key can be sent from your flash appliation via <b>GET</b> or <b>POST</b>. In order to send or receive data from your web service use the <b>apiKey</b> and <b>service</b> url variable; plus any other variables required by the service.");
		array_push($help, "<br><small>It's highly recommended that you change your API Key.</small>");
		array_push($help, "<div>Add documentation to your API functions by creating a new <a href='post-new.php'>post</a> and adding it to the '<b><span id='cdx_cat'>".FAPI_TAG."</span></b>' category.<br>Your post will then show up in the section below.</div><br><hr><br><br>"); 

		return __($help[$id]);
	}
	
	// documentation
	function flash_api_docs() { 
		global $wpdb;
		$help = flash_api_help(2);
		$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->terms." WHERE slug = 'flash_api'");
		$row = $wpdb->get_row($sql);
		
		$id = $row->term_id;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->term_taxonomy." WHERE term_id = ".$id); 
		$row = $wpdb->get_row($sql);

		$id = $row->term_taxonomy_id;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->term_relationships." WHERE term_taxonomy_id = ".$id);
		$rows = $wpdb->get_results($sql);

		$ids = array();

		foreach($rows as $row) { array_push($ids, $row->object_id); }

		if (count($ids) < 1) { return $help; }

		$toc = '<div id="toc"><h4>'.__("Index").'</h4><ul>';
		$doc = '<div id="docs">';
		$cnt = 0; 

		$names = array(); 

		foreach($ids as $id) { 
			$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->posts." WHERE ID = ".$id." AND post_status = 'publish' AND post_type ='post'"); 
			$row = $wpdb->get_row($sql);
			array_push($names, $row->post_name); 
		}

		if (count($name) < 1) { return $help; } 
		sort($names);

		foreach($names as $name) { 
			$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->posts." WHERE post_name = '".$name."'");
			$row = $wpdb->get_row($sql);
			
			$toc .= '<li><a href="#'.$row->post_name.'">'.$row->post_title.'</a></li>';
			
			$doc .= ($cnt > 0) ? '<br /><hr><br />' : '';
			$doc .= '<h3><a name="'.$row->post_name.'" /></a>'.$row->post_title.'</h3>';
			$doc .= $row->post_content;
			$doc .= "<br><a href='".SITE.'wp-admin/post.php?action=edit&post='.$row->ID."'>".__("edit")."</a>";

			if ($cnt != 0) { $doc .= " | <a href='#top'>".__("back to top")."</a><br>"; }

			else { $doc .= '<br>'; }

			$cnt++;
		}

		$toc .= '</ul></div>';
		$doc .= '</div>';

		$html = $help . $toc . $doc;
		return $html;
	}

	// User profile hook
	add_action('show_user_profile', 'fapi_user_profile_hook');
	add_action('edit_user_profile', 'fapi_user_profile_hook');
	
	function fapi_user_profile_hook($user) {
		$apiKey = get_user_meta($user->ID, 'apiKey', true);	
		$perm = current_user_can('add_users'); 
		$readOnly = (!$perm) ? 'readonly="readonly"' : '';
		
		echo '<h3>Flash API</h3>
		<script src="'.FAPI_PLUGIN_URL.'/js/MD5.js" type="text/javascript"></script>
		<script src="'.FAPI_PLUGIN_URL.'/js/flash_api.js" type="text/javascript"></script>
		<table class="form-table">
			<tr>
				<th><label for="apilabel">API Key</label></th>
				<td><input type="text" name="flash_api_key" id="flash_api_key" value="'.$apiKey.'" class="regular-text" '.$readOnly.' />';
	            if ($perm) { echo '<input type="button" class="button-secondary" name="generate" id="generate" value="'.__("Generate").'" /></td>'; }
			echo '</tr>
		</table>';
	}
	
	// User profile update
	add_action('personal_options_update', 'fapi_apiKey_save');
	add_action('edit_user_profile_update', 'fapi_apiKey_save');
	
	function fapi_apiKey_save($user_id) {
		if (!current_user_can('edit_user', $user_id )) { return false; }	
		update_usermeta($user_id, 'apiKey', $_POST['flash_api_key']);
	}
?>