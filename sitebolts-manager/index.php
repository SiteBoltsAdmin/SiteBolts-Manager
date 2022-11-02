<?php
/*
* Plugin Name: SiteBolts Manager
* Description: A central management plugin that helps you keep track of the sites you maintain.
* Version: 2.0
* Author: SiteBolts
* Author URI: https://sitebolts.com/
*/

function sbman_get_plugin_version()
{
	return '12';
}

function sbman_add_wp_admin_menu()
{
	add_submenu_page(
						'edit.php?post_type=client_site',
						'Overview',
						'Overview',
						'administrator',
						'sitebolts_manager_overview',
						'sbman_generate_overview_page',
						0,
					);
					
	add_submenu_page(
						'edit.php?post_type=client_site',
						'Settings',
						'Settings',
						'administrator',
						'sitebolts_manager_settings',
						'sbman_generate_settings_page',
						3,
					);
}

add_action('admin_menu', 'sbman_add_wp_admin_menu');

function sbman_generate_overview_page()
{
	?>
	<style>
	.client-sites
	{
		display: flex;
		flex-wrap: wrap;
		gap: 15px;
	}
	
	.client-sites .client-site
	{
		display: flex;
		width: 100%;
		max-width: 320px;
		flex-wrap: wrap;
		padding: 15px;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		border: 1px solid #999999;
		background-color: #ffffff;
	}
	
	.client-sites .client-site .site-title
	{
		font-size: 16px;
		font-weight: 700;
	}
	
	.client-sites .client-site .site-title a
	{
		color: #3c434a;
		text-decoration: none;
	}
	
	.client-sites .client-site .site-url
	{
		font-size: 14px;
	}
	
	.client-sites .client-site .site-status.site-online
	{
		color: #009900;
	}
	
	.client-sites .client-site .num-checks-passed > p
	{
		margin: 0;
		font-weight: 400;
	}
	
	.client-sites .client-site .num-checks-passed[data-issues-found="no"],
	.client-sites .client-site p.success-message
	{
		color: #009900;
	}
	
	.client-sites .client-site .num-checks-passed[data-issues-found="yes"],
	.client-sites .client-site p.error-message,
	.client-sites .client-site .failed-checks-list
	{
		color: #ff0000;
	}
	
	.client-sites .client-site .failed-checks-list
	{
		margin: 0;
		overflow: auto;
		width: 100%;
		text-align: center;
	}
	
	.client-sites .client-site .failed-checks-list li
	{
		margin: 0;
	}
	
	.test-list-container
	{
		display: inline-flex;
		padding: 15px;
		flex-direction: column;
		justify-content: center;
		border: 1px solid #999999;
		background-color: #ffffff;
	}
	</style>
	<?php
	
	$args =	[
				'post_type' 		=> 'client_site',
				'posts_per_page'	=> -1,
				'fields'			=> 'ids',
				'orderby'			=> 'title',
				'order'				=> 'asc',
			];
	
	$client_site_ids = get_posts($args);
	
	echo '<div class="wrap">';
	echo '<h1 class="wp-heading-inline">SiteBolts Manager - Overview</h1>';
	echo '<hr class="wp-header-end">';
	echo '</div>'; //.wrap
	echo '<div class="wrap">';
	
	echo '<div class="client-sites">';
	
	foreach ($client_site_ids as $client_site_id)
	{
		$site_name_html = get_the_title($client_site_id);
		$site_meta = get_post_meta($client_site_id);
		$site_edit_link = admin_url('post.php?post=' . $client_site_id) . '&action=edit';
		
		$site_url = $site_meta['site_url'][0] ?? null;
		$site_token = $site_meta['site_token'][0] ?? null;
		$endpoint_type = $site_meta['endpoint_type'][0] ?? null;
		
		$site_status = 'Live'; //Temp
		
		echo '<div class="client-site" data-endpoint-type="' . htmlspecialchars($endpoint_type ?? '') . '" data-site-token="' . htmlspecialchars($site_token ?? '') . '" data-site-url="' . htmlspecialchars($site_url ?? '') . '">';
		echo '<div class="site-title"><a href="' . htmlspecialchars($site_edit_link ?? '') . '">' . $site_name_html . '</a></div>';
		echo '<div class="site-url"><a href="' . htmlspecialchars($site_url ?? '') . '">' . htmlspecialchars($site_url ?? '') . '</a></div>';
		echo '<div class="num-checks-passed site-unchecked" data-issues-found="">Checking metrics...</div>';
		echo '<ul class="failed-checks-list"></ul>';
		echo '</div>'; //.client-site
	}
	
	echo '</div>'; //.client-sites
	echo '</div>'; //.wrap
	
	echo '<div class="test-list-container wrap">';
	echo '<h2>Tests being performed</h2>';
	echo '<ul>';
	echo '<li><b>Test 1</b>: Is the latest version of the plugin installed?</li>';
	echo '<li><b>Test 2</b>: Is the latest version of PHP 8.0 or 8.1 installed?</li>';
	echo '<li><b>Test 3</b>: Are all of the plugins up-to-date?</li>';
	echo '<li><b>Test 4</b>: Are all of the themes up-to-date?</li>';
	echo '<li><b>Test 5</b>: Is the WordPress core up-to-date?</li>';
	echo '<li><b>Test 6</b>: Are all of the translations up-to-date?</li>';
	echo '<li><b>Test 7</b>: Is everything up-to-date?</li>';
	echo '<li><b>Test 8</b>: Are comments set to require manual approval?</li>';
	echo '<li><b>Test 9</b>: Is the max number of comment links set to 0?</li>';
	echo '<li><b>Test 10</b>: Is the site using a child theme?</li>';
	echo '<li><b>Test 11</b>: Is noindex disabled?</li>';
	echo '<li><b>Test 12</b>: Is the core set to auto-update for major releases?</li>';
	echo '<li><b>Test 13</b>: Is the core set to auto-update for minor releases?</li>';
	echo '<li><b>Test 14</b>: Is the core set to auto-update for dev releases?</li>';
	echo '<li><b>Test 15</b>: Is the home URL using HTTPS?</li>';
	echo '<li><b>Test 16</b>: Is the site URL using HTTPS?</li>';
	echo '<li><b>Test 17</b>: Is the Child Theme Configurator still active?</li>';
	echo '<li><b>Test 18</b>: Is Contact Form 7 missing Flamingo?</li>';
	echo '<li><b>Test 19</b>: Is Contact Form 7 missing a honeypot?</li>';
	echo '<li><b>Test 20</b>: Is Contact Form 7 missing reCAPTCHA?</li>';
	echo '</ul>';
	echo '</div>';
}

function sbman_generate_settings_page()
{
	echo 'TODO';
}

function sbman_register_client_site_post_type()
{
	$labels =	[
					'name'					=> _x('Client Sites', 'Post Type General Name'),
					'singular_name'			=> _x('Client Site', 'Post Type Singular Name'),
					'menu_name'				=> __('SiteBolts Manager'),
					'parent_item_colon'		=> __('Parent Client Sites'),
					'all_items'				=> __('Client Sites'),
					'view_item'				=> __('View Client Sites'),
					'add_new'				=> __('Add New'),
					'edit_item'				=> __('Edit Client Site'),
				];
				
	$args =	[
				'labels'				=>	$labels,
				'supports'				=>	['title', 'revisions', 'thumbnail'],
				'hierarchical'			=>	false,
				'public'				=>	false,
				'revision'				=>	true,
				'show_ui'				=>	true,
				'show_in_menu'			=>	true,
				'show_in_nav_menus'		=>	true,
				'show_in_admin_bar'		=>	true,
				'can_export'			=>	true,
				'has_archive'			=>	false,
				'exclude_from_search'	=>	true,
				'rewrite'				=>	['slug' => 'client-site'],
				'publicly_queryable'	=>	false,
				'menu_icon'				=>	'dashicons-admin-generic',
			];
	
	$registration_result = register_post_type('client_site', $args);
}

add_action('init', 'sbman_register_client_site_post_type');


function sbman_add_client_sites_metabox()
{
	add_meta_box(
					'wporg_box_id',
					'Site Details',
					'sbman_generate_client_sites_metabox',
					'client_site',
				);
}
add_action('add_meta_boxes', 'sbman_add_client_sites_metabox');


function sbman_generate_client_sites_metabox()
{
	$post_id = get_the_ID();
	$post_meta = get_post_meta($post_id);
	
	$site_url = $post_meta['site_url'][0] ?? null;
	$site_token = $post_meta['site_token'][0] ?? null;
	$endpoint_type = $post_meta['endpoint_type'][0] ?? null;
	$site_notes = $post_meta['site_notes'][0] ?? null;
	$uptime_kuma_id = $post_meta['uptime_kuma_id'][0] ?? null;
	
	echo '<p><span style="display: inline-block; min-width: 90px;">Site URL:</span> <input type="text" name="site_url" id="site-url-field" class="site-url-field" value="' . htmlspecialchars($site_url ?? '') . '" size="50"></p>';
	
	echo '<p><span style="display: inline-block; min-width: 90px;">Site token:</span> <input type="text" name="site_token" id="site-token-field" class="site-token-field" value="' . htmlspecialchars($site_token ?? '') . '" size="50"></p>';
	
	echo	'<p>
				<span style="display: inline-block; min-width: 90px;">Endpoint type:</span>
				<select name="endpoint_type" id="endpoint-type-field" class="endpoint-type-field" style="min-width: 369px;">
					<option value=""' . (($endpoint_type === '') ? ' selected' : '') . '>(Select)</option>
					<option value="wordpress"' . (($endpoint_type === 'wordpress') ? ' selected' : '') . '>WordPress</option>
					<option value="none"' . (($endpoint_type === 'none') ? ' selected' : '') . '>None</option>
				</select>
			</p>';
	
	echo '<p><span style="display: inline-block; min-width: 90px;">Notes:</span> <textarea name="site_notes" id="site-notes-field" class="site-notes-field" cols="53" rows="10">' . htmlspecialchars($site_notes) . '</textarea></p>';
	
	echo '<p><span style="display: inline-block; min-width: 90px;">Uptime Kuma ID:</span> <input type="text" name="uptime_kuma_id" id="uptime-kuma-field" class="uptime-kuma-field" value="' . htmlspecialchars($uptime_kuma_id ?? '') . '" size="50"></p>';
	
	echo '<p><span style="display: inline-block; min-width: 90px;">(TODO) Maintainer:</span> <input type="text" name="TEMP1" id="site-token-field" class="site-token-field" value="' . htmlspecialchars($site_token ?? '') . '" size="50"></p>';
	
	echo '<p><span style="display: inline-block; min-width: 90px;">(TODO) Maintenance pay:</span> <input type="text" name="TEMP2" id="site-token-field" class="site-token-field" value="' . htmlspecialchars($site_token ?? '') . '" size="50"></p>';
	
	echo '<p>TODO: Add a select element for whether a site is SiteBolts managed (Yes, Partially (please specify), No)</p>';
}


//TODO
function wporg_save_postdata($post_id)
{
	$site_url = $_POST['site_url'] ?? null;
	$site_token = $_POST['site_token'] ?? null;
	$endpoint_type = $_POST['endpoint_type'] ?? null;
	$site_notes = $_POST['site_notes'] ?? null;
	$uptime_kuma_id = $_POST['uptime_kuma_id'] ?? null;
	
	if ($site_url !== null)
	{
		update_post_meta($post_id, 'site_url', $site_url);
	}
	
	if ($site_token !== null)
	{
		update_post_meta($post_id, 'site_token', $site_token);
	}
	
	if ($endpoint_type !== null)
	{
		update_post_meta($post_id, 'endpoint_type', $endpoint_type);
	}
	
	if ($site_notes !== null)
	{
		update_post_meta($post_id, 'site_notes', $site_notes);
	}
	
	if ($uptime_kuma_id !== null)
	{
		update_post_meta($post_id, 'uptime_kuma_id', $uptime_kuma_id);
	}
	
}
add_action('save_post_client_site', 'wporg_save_postdata');


function sbman_enqueue_scripts()
{
	$plugin_version = sbman_get_plugin_version();
	
	wp_enqueue_script('sitebolts-manager-script', plugins_url('script.js', __FILE__ ), [], $plugin_version, false);
}

add_action('admin_enqueue_scripts', 'sbman_enqueue_scripts');