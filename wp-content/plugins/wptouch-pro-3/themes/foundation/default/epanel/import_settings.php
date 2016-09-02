<?php 
add_action( 'admin_enqueue_scripts', 'import_epanel_javascript' );
function import_epanel_javascript( $hook_suffix ) {
	if ( 'admin.php' == $hook_suffix && isset( $_GET['import'] ) && isset( $_GET['step'] ) && 'wordpress' == $_GET['import'] && '1' == $_GET['step'] )
		add_action( 'admin_head', 'admin_headhook' );
}

function admin_headhook(){ ?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$("p.submit").before("<p><input type='checkbox' id='importepanel' name='importepanel' value='1' style='margin-right: 5px;'><label for='importepanel'>Replace ePanel settings with sample data values</label></p>");
		});
	</script>
<?php }

add_action('import_end','importend');
function importend(){
	global $wpdb, $shortname;
	
	#make custom fields image paths point to sampledata/sample_images folder
	$sample_images_postmeta = $wpdb->get_results("SELECT meta_id, meta_value FROM $wpdb->postmeta WHERE meta_value REGEXP 'http://et_sample_images.com'");
	if ( $sample_images_postmeta ) {
		foreach ( $sample_images_postmeta as $postmeta ){
			$template_dir = get_template_directory_uri();
			if ( is_multisite() ){
				switch_to_blog(1);
				$main_siteurl = site_url();
				restore_current_blog();
				
				$template_dir = $main_siteurl . '/wp-content/themes/' . get_template();
			}
			preg_match( '/http:\/\/et_sample_images.com\/([^.]+).jpg/', $postmeta->meta_value, $matches );
			$image_path = $matches[1];
			
			$local_image = preg_replace( '/http:\/\/et_sample_images.com\/([^.]+).jpg/', $template_dir . '/sampledata/sample_images/$1.jpg', $postmeta->meta_value );
			
			$local_image = preg_replace( '/s:55:/', 's:' . strlen( $template_dir . '/sampledata/sample_images/' . $image_path . '.jpg' ) . ':', $local_image );
			
			$wpdb->update( $wpdb->postmeta, array( 'meta_value' => $local_image ), array( 'meta_id' => $postmeta->meta_id ), array( '%s' ) );
		}
	}

	if ( !isset($_POST['importepanel']) )
		return;
	
	$importOptions = 'YToxMDY6e3M6MDoiIjtOO3M6MTA6ImVuZXdzX2xvZ28iO3M6MDoiIjtzOjEzOiJlbmV3c19mYXZpY29uIjtzOjA6IiI7czoxODoiZW5ld3NfY29sb3Jfc2NoZW1lIjtzOjQ6IkJsdWUiO3M6MTY6ImVuZXdzX2Jsb2dfc3R5bGUiO047czoxNjoiZW5ld3NfZmVlZGJ1cm5lciI7TjtzOjE4OiJlbmV3c19jYXRudW1fcG9zdHMiO3M6MToiNiI7czoyMjoiZW5ld3NfYXJjaGl2ZW51bV9wb3N0cyI7czoxOiI1IjtzOjIxOiJlbmV3c19zZWFyY2hudW1fcG9zdHMiO3M6MToiNSI7czoxODoiZW5ld3NfdGFnbnVtX3Bvc3RzIjtzOjE6IjUiO3M6MjA6ImVuZXdzX2ZlZWRidXJuZXJfcnNzIjtzOjA6IiI7czoyNToiZW5ld3NfZmVlZGJ1cm5lcl9jb21tZW50cyI7czowOiIiO3M6MjI6ImVuZXdzX2ZlZWRidXJuZXJfZW1haWwiO3M6MDoiIjtzOjE3OiJlbmV3c19kYXRlX2Zvcm1hdCI7czo2OiJNIGosIFkiO3M6MTY6ImVuZXdzX2dyYWJfaW1hZ2UiO047czoxNzoiZW5ld3NfdXNlX2V4Y2VycHQiO047czoxNzoiZW5ld3NfcmVjZW50X2NhdDEiO3M6NDoiQmxvZyI7czoxNzoiZW5ld3NfcmVjZW50X2NhdDIiO3M6ODoiRmVhdHVyZWQiO3M6MTc6ImVuZXdzX3JlY2VudF9jYXQzIjtzOjk6IlBvcnRmb2xpbyI7czoyMDoiZW5ld3NfaG9tZXBhZ2VfcG9zdHMiO3M6MToiNyI7czoyMDoiZW5ld3NfZXhsY2F0c19yZWNlbnQiO047czoxNDoiZW5ld3NfZmVhdHVyZWQiO3M6Mjoib24iO3M6MTU6ImVuZXdzX2R1cGxpY2F0ZSI7TjtzOjE3OiJlbmV3c19zbGlkZXJfYXV0byI7TjtzOjE3OiJlbmV3c19wYXVzZV9ob3ZlciI7TjtzOjE0OiJlbmV3c19mZWF0X2NhdCI7czo4OiJGZWF0dXJlZCI7czoyMzoiZW5ld3NfaG9tZXBhZ2VfZmVhdHVyZWQiO3M6MToiMyI7czoyMjoiZW5ld3Nfc2xpZGVyX2F1dG9zcGVlZCI7czo0OiI3MDAwIjtzOjE5OiJlbmV3c19zbGlkZXJfZWZmZWN0IjtzOjQ6ImZhZGUiO3M6MTU6ImVuZXdzX21lbnVwYWdlcyI7TjtzOjIyOiJlbmV3c19lbmFibGVfZHJvcGRvd25zIjtzOjI6Im9uIjtzOjE1OiJlbmV3c19ob21lX2xpbmsiO3M6Mjoib24iO3M6MTY6ImVuZXdzX3NvcnRfcGFnZXMiO3M6MTA6InBvc3RfdGl0bGUiO3M6MTY6ImVuZXdzX29yZGVyX3BhZ2UiO3M6MzoiYXNjIjtzOjIzOiJlbmV3c190aWVyc19zaG93bl9wYWdlcyI7czoxOiIzIjtzOjE0OiJlbmV3c19tZW51Y2F0cyI7TjtzOjMzOiJlbmV3c19lbmFibGVfZHJvcGRvd25zX2NhdGVnb3JpZXMiO3M6Mjoib24iO3M6MjI6ImVuZXdzX2NhdGVnb3JpZXNfZW1wdHkiO3M6Mjoib24iO3M6Mjg6ImVuZXdzX3RpZXJzX3Nob3duX2NhdGVnb3JpZXMiO3M6MToiMyI7czoxNDoiZW5ld3Nfc29ydF9jYXQiO3M6NDoibmFtZSI7czoxNToiZW5ld3Nfb3JkZXJfY2F0IjtzOjM6ImFzYyI7czoxNzoiZW5ld3Nfc3dhcF9uYXZiYXIiO047czoyMToiZW5ld3NfZGlzYWJsZV90b3B0aWVyIjtOO3M6MTQ6ImVuZXdzX3Bvc3RpbmZvIjthOjQ6e2k6MDtzOjY6ImF1dGhvciI7aToxO3M6NDoiZGF0ZSI7aToyO3M6MTA6ImNhdGVnb3JpZXMiO2k6MztzOjg6ImNvbW1lbnRzIjt9czoxNjoiZW5ld3NfdGh1bWJuYWlscyI7czoyOiJvbiI7czoxNzoiZW5ld3NfcG9zdHNfc2hhcmUiO3M6Mjoib24iO3M6MjM6ImVuZXdzX3Nob3dfcG9zdGNvbW1lbnRzIjtzOjI6Im9uIjtzOjI3OiJlbmV3c190aHVtYm5haWxfd2lkdGhfcG9zdHMiO3M6MzoiMTcyIjtzOjI4OiJlbmV3c190aHVtYm5haWxfaGVpZ2h0X3Bvc3RzIjtzOjM6IjE3MiI7czoyMToiZW5ld3NfcGFnZV90aHVtYm5haWxzIjtOO3M6MjQ6ImVuZXdzX3Nob3dfcGFnZXNjb21tZW50cyI7TjtzOjI3OiJlbmV3c190aHVtYm5haWxfd2lkdGhfcGFnZXMiO3M6MzoiMTcyIjtzOjI4OiJlbmV3c190aHVtYm5haWxfaGVpZ2h0X3BhZ2VzIjtzOjM6IjE3MiI7czoxOToiZW5ld3NfaG9tZV9jYXRib3hlcyI7czoyOiJvbiI7czoyNDoiZW5ld3NfaG9tZV9zaG9ydGVuX3Bvc3RzIjtzOjI6Im9uIjtzOjE5OiJlbmV3c19jdXN0b21fY29sb3JzIjtOO3M6MTU6ImVuZXdzX2NoaWxkX2NzcyI7TjtzOjE4OiJlbmV3c19jaGlsZF9jc3N1cmwiO3M6MDoiIjtzOjE5OiJlbmV3c19jb2xvcl9iZ2NvbG9yIjtzOjA6IiI7czoyMDoiZW5ld3NfY29sb3JfbWFpbmZvbnQiO3M6MDoiIjtzOjIwOiJlbmV3c19jb2xvcl9tYWlubGluayI7czowOiIiO3M6MjA6ImVuZXdzX2NvbG9yX3BhZ2VsaW5rIjtzOjA6IiI7czoyMDoiZW5ld3NfY29sb3JfY2F0c2xpbmsiO3M6MDoiIjtzOjIyOiJlbmV3c19jb2xvcl9mZWF0aGVhZGVyIjtzOjA6IiI7czoyNjoiZW5ld3NfY29sb3JfcmVjZW50aGVhZGluZ3MiO3M6MDoiIjtzOjI2OiJlbmV3c19jb2xvcl9zaWRlYmFyX3RpdGxlcyI7czowOiIiO3M6MjU6ImVuZXdzX2NvbG9yX2Zvb3Rlcl90aXRsZXMiO3M6MDoiIjtzOjI0OiJlbmV3c19jb2xvcl9mb290ZXJfbGlua3MiO3M6MDoiIjtzOjIyOiJlbmV3c19jb2xvcl9icmVhZGNydW1iIjtzOjA6IiI7czoxOToiZW5ld3NfY29sb3JfaGVhZGluZyI7czowOiIiO3M6MjA6ImVuZXdzX3Nlb19ob21lX3RpdGxlIjtOO3M6MjY6ImVuZXdzX3Nlb19ob21lX2Rlc2NyaXB0aW9uIjtOO3M6MjM6ImVuZXdzX3Nlb19ob21lX2tleXdvcmRzIjtOO3M6MjQ6ImVuZXdzX3Nlb19ob21lX2Nhbm9uaWNhbCI7TjtzOjI0OiJlbmV3c19zZW9faG9tZV90aXRsZXRleHQiO3M6MDoiIjtzOjMwOiJlbmV3c19zZW9faG9tZV9kZXNjcmlwdGlvbnRleHQiO3M6MDoiIjtzOjI3OiJlbmV3c19zZW9faG9tZV9rZXl3b3Jkc3RleHQiO3M6MDoiIjtzOjE5OiJlbmV3c19zZW9faG9tZV90eXBlIjtzOjI3OiJCbG9nTmFtZSB8IEJsb2cgZGVzY3JpcHRpb24iO3M6MjM6ImVuZXdzX3Nlb19ob21lX3NlcGFyYXRlIjtzOjM6IiB8ICI7czoyMjoiZW5ld3Nfc2VvX3NpbmdsZV90aXRsZSI7TjtzOjI4OiJlbmV3c19zZW9fc2luZ2xlX2Rlc2NyaXB0aW9uIjtOO3M6MjU6ImVuZXdzX3Nlb19zaW5nbGVfa2V5d29yZHMiO047czoyNjoiZW5ld3Nfc2VvX3NpbmdsZV9jYW5vbmljYWwiO047czoyODoiZW5ld3Nfc2VvX3NpbmdsZV9maWVsZF90aXRsZSI7czo5OiJzZW9fdGl0bGUiO3M6MzQ6ImVuZXdzX3Nlb19zaW5nbGVfZmllbGRfZGVzY3JpcHRpb24iO3M6MTU6InNlb19kZXNjcmlwdGlvbiI7czozMToiZW5ld3Nfc2VvX3NpbmdsZV9maWVsZF9rZXl3b3JkcyI7czoxMjoic2VvX2tleXdvcmRzIjtzOjIxOiJlbmV3c19zZW9fc2luZ2xlX3R5cGUiO3M6MjE6IlBvc3QgdGl0bGUgfCBCbG9nTmFtZSI7czoyNToiZW5ld3Nfc2VvX3NpbmdsZV9zZXBhcmF0ZSI7czozOiIgfCAiO3M6MjU6ImVuZXdzX3Nlb19pbmRleF9jYW5vbmljYWwiO047czoyNzoiZW5ld3Nfc2VvX2luZGV4X2Rlc2NyaXB0aW9uIjtOO3M6MjA6ImVuZXdzX3Nlb19pbmRleF90eXBlIjtzOjI0OiJDYXRlZ29yeSBuYW1lIHwgQmxvZ05hbWUiO3M6MjQ6ImVuZXdzX3Nlb19pbmRleF9zZXBhcmF0ZSI7czozOiIgfCAiO3M6Mjk6ImVuZXdzX2ludGVncmF0ZV9oZWFkZXJfZW5hYmxlIjtzOjI6Im9uIjtzOjI3OiJlbmV3c19pbnRlZ3JhdGVfYm9keV9lbmFibGUiO3M6Mjoib24iO3M6MzI6ImVuZXdzX2ludGVncmF0ZV9zaW5nbGV0b3BfZW5hYmxlIjtzOjI6Im9uIjtzOjM1OiJlbmV3c19pbnRlZ3JhdGVfc2luZ2xlYm90dG9tX2VuYWJsZSI7czoyOiJvbiI7czoyMjoiZW5ld3NfaW50ZWdyYXRpb25faGVhZCI7czowOiIiO3M6MjI6ImVuZXdzX2ludGVncmF0aW9uX2JvZHkiO3M6MDoiIjtzOjI4OiJlbmV3c19pbnRlZ3JhdGlvbl9zaW5nbGVfdG9wIjtzOjA6IiI7czozMToiZW5ld3NfaW50ZWdyYXRpb25fc2luZ2xlX2JvdHRvbSI7czowOiIiO3M6MTk6ImVuZXdzX2xlYWRlcl9lbmFibGUiO047czoxNjoiZW5ld3NfNDY4X2VuYWJsZSI7TjtzOjE4OiJlbmV3c19sZWFkZXJfaW1hZ2UiO3M6MDoiIjtzOjE2OiJlbmV3c19sZWFkZXJfdXJsIjtzOjA6IiI7czoxNToiZW5ld3NfNDY4X2ltYWdlIjtzOjA6IiI7czoxMzoiZW5ld3NfNDY4X3VybCI7czowOiIiO30=';
	
	/*global $options;
	
	foreach ($options as $value) {
		if( isset( $value['id'] ) ) { 
			update_option( $value['id'], $value['std'] );
		}
	}*/
	
	$importedOptions = unserialize(base64_decode($importOptions));
	
	foreach ($importedOptions as $key=>$value) {
		if ($value != '') update_option( $key, $value );
	}
} ?>