<?php

/**
 * Retruns a list of all attached files
 * @return XML list of info pertaining to the selected gallery
*/
function attachments() { 
    global $wpdb;
 
    $sql = "SELECT * FROM ".$wpdb->posts." WHERE post_type = 'attachment'";
    $rows = $wpdb->get_results($sql); 
    foreach($rows as $row) { 
		$xml .= nodeWrap('<node id="'.$row->ID.'" date="'.$row->post_date.'" title="'.$row->post_title.'" link="'.$row->guid.'"><![CDATA['.$row->post_content.']]></node>'); 
	}
    return xmlWrap($xml);
}

/**
 * Gets a list of images from a NextGEN Gallery 
 * @params name:String the name of the gallery
 * @return XML list of info pertaining to the selected gallery
 */
function gallery() { 
	// Params
	$id = $_REQUEST['id'];
	
	// Param error handler
	if (!$id) { return xmlWrap('<node error="true" param="id" msg="id parameter required" />'); }
	
	global $wpdb;
	
	$gtable = $wpdb->prefix . 'ngg_gallery';
	$itable = $wpdb->prefix . 'ngg_pictures';
	$site = get_option('siteurl'); 
	
	$ids = explode(',', $id); 
	
	for ($i = 0; $i < count($ids); $i++) { 
		$gallery = $ids[$i];
		
		$sql = "SELECT images.pid, gallery.path,images.galleryid, images.filename, images.description, images.alttext
			FROM ".$gtable." AS gallery
			JOIN ".$itable." AS images
			ON gallery.gid = images.galleryid
			WHERE gallery.gid = '".$gallery."' AND images.exclude = 0
			ORDER BY images.sortorder ASC";
		
		$sql = $wpdb->prepare($sql); 
		
		$rows = $wpdb->get_results($sql);
		foreach($rows as $row) {
			$img = $site . '/' . $row->path . '/' . $row->filename;
			$thm = $site . '/' . $row->path . '/thumbs/thumbs_' . $row->filename;
			
			if ($row->description) { 
				$xml .= nodeWrap("<node category='".$gallery."' galleryid='".$row->galleryid."' image='".$img."' thumb='".$thm."' id='".$row->pid."'><title><![CDATA[".$row->alttext."]]></title><desc><![CDATA[".$row->description."]]></desc></node>"); 
			}
			else { // reduce unnecessary bytes by checking for the descript
				$xml .= nodeWrap("<node category='".$gallery."' galleryid='".$row->galleryid."' image='".$img."' thumb='".$thm."' id='".$row->pid."'><title><![CDATA[".$row->alttext."]]></title></node>"); 
			}
		}
	}
	
	return xmlWrap($xml);
}
/**
 * Sends mail message via PHP X-Mailer
 * @param from:String the sender of the email message
 * @param to:String the recipiant of the email message
 * @param subject:String [optional] the subject of the email message
 * @param message:String the body of the email message
 * @return XML node detailing if the mail was sent successfully or errored<br>
 * 			Success: 		<node error="false" param="sendmail" msg="message sent" /><br>
 			Error: 			<node error="true" param="sendmail" msg="unable to send message" /><br>
			Param Error:	<node error="true" param="from" msg="from parameter required" />
 */
function sendmail() {
	// Params
	$from = $_REQUEST['from'];
	$name = $_REQUEST['name'];
	$msg = $_REQUEST['message'];
	$sub = $_REQUEST['subject'];
	$to = $_REQUEST['to'];
	
	// Param Error handler 
	if (!$from) { return xmlWrap('<node error="true" param="from" msg="from parameter required" />'); }
	if (!$name) { return xmlWrap('<node error="true" param="name" msg="name parameter required" />'); } 
	if (!$to) { return xmlWrap('<node error="true" param="to" msg="to parameter required" />'); } 
	if (!$msg) { return xmlWrap('<node error="true" param="message" msg="message parameter required" />'); } 
	
	global $wpdb;
	
	$name = ucwords(strtolower($name));
	$from = strtolower($from);
	
	$msg = str_replace("\'", "'", $msg);
	$msg = str_replace('\"', '"', $msg);
	
	$headers = "From: ($name) $from\r\nReply-To: $from\r\nX-Mailer: PHP/" . phpversion();
	
	// Mail it
	$conf = mail($to, $sub, $msg, $headers);
	
	// Status message
	if ($conf) { return xmlWrap('<node error="false" param="sendmail" msg="message sent" />'); }
	else { return xmlWrap('<node error="true" param="sendmail" msg="unable to send message" />'); }
}

/**
 * Gets a list of the most recent (20) posts
 * @param limit:Number Number of posts to return
 * @return XML list of info pertaining to published posts
 */
function pages() { 
	global $wpdb;
	
	// Params
	$limit = ($_REQUEST['limit']) ? $_REQUEST['limit'] : 200;
	
	$sql = "SELECT post.post_content, post.post_excerpt, post.post_title, post.post_date, post.guid, post.id, user.user_nicename 
		FROM ".$wpdb->posts." AS post
		JOIN ".$wpdb->users." AS user
		ON post.post_author = user.ID
		WHERE post.post_type = 'page' AND post.post_status = 'publish' 
		ORDER BY post.menu_order 
		LIMIT ".$limit;
		
	$sql = $wpdb->prepare($sql); 
	
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) { 
		$xml .= nodeWrap("<node title='".$row->post_title."' id='".$row->id."' date='".$row->post_date."' link='".$row->guid."' author='".$row->user_nicename."' thumbnail=''>\r\t\t<![CDATA[".$row->post_content."]]>\r\t</node>");
	}
	return xmlWrap($xml);
}
function posts() { 
	global $wpdb;
	
	// Params
	$limit = ($_REQUEST['limit']) ? $_REQUEST['limit'] : 200;
	
	$sql = "SELECT post.post_content, post.post_excerpt, post.post_title, post.post_date, post.guid, user.user_nicename 
		FROM ".$wpdb->posts." AS post
		JOIN ".$wpdb->users." AS user
		ON post.post_author = user.ID
		WHERE post.post_type = 'post' AND post.post_status = 'publish' 
		ORDER BY post.post_date_gmt DESC
		LIMIT ".$limit;
		
	$sql = $wpdb->prepare($sql); 
	
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) { 
		$xml .= nodeWrap("<node title='".$row->post_title."' date='".$row->post_date."' link='".$row->guid."' author='".$row->user_nicename."' thumbnail=''>\r\t\t<![CDATA[".$row->post_content."]]>\r\t</node>");
	}
	return xmlWrap($xml);
}
function links() { 
    global $wpdb;
 
   // Params
	$limit = ($_REQUEST['limit']) ? $_REQUEST['limit'] : 200;
	
	$sql = "SELECT link.link_id, link.link_order, link.link_name, link.link_image, link.link_url
		FROM ".$wpdb->links." AS link
		ORDER BY link.link_order
		LIMIT ".$limit;
		
	$sql = $wpdb->prepare($sql); 
    /* ." WHERE post_type = 'attachment'"; */
    $rows = $wpdb->get_results($sql); 
    foreach($rows as $row) { 
		$xml .= nodeWrap('<node id="'.$row->link_id.'" order="'.$row->link_order.'" target="'.$row->link_order.'" name="'.$row->link_name.'" image="'.$row->link_image.'"  link="'.$row->link_url.'"><![CDATA['.$row->link_url.']]></node>'); 
	}
    return xmlWrap($xml);
}
?>