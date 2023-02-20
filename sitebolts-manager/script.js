document.addEventListener('DOMContentLoaded', function()
{
	function sbman_get_wordpress_metrics(site_url, site_token, client_site)
	{
		let site_status_element = client_site.querySelector('.site-status');
		let num_checks_passed_element = client_site.querySelector('.num-checks-passed');
		let failed_checks_list_element = client_site.querySelector('.failed-checks-list');
		
		let form_data = new FormData();
		form_data.append('action', 'sbmon_get_site_metrics_ajax');
		form_data.append('token', site_token);

		fetch(site_url + '?rest_route=/sitebolts-monitor/v1/sbmon_get_site_metrics_rest', {method: 'POST', headers: {}, body: form_data})
		.then
		(
			response => response.json(),
		)
		.then
		(
			data =>
			{
				console.log({site_url, data});
				
				if (data.code === 'rest_no_route')
				{
					num_checks_passed_element.innerHTML = '<p class="error-message">REST API endpoint not found.</p>';
					num_checks_passed_element.setAttribute('data-issues-found', 'yes');
				}
				
				else if (data.status === 'error')
				{
					num_checks_passed_element.innerHTML = data.html_message;
					num_checks_passed_element.setAttribute('data-issues-found', 'yes');
				}
				
				else if (data.status === 'success')
				{
					let metrics = sbman_validate_site_metrics(data.metrics);
					console.log('test 1...');
					console.log({metrics});					
		
					let total_num_checks = metrics.total_num_checks;
					let num_checks_passed = metrics.num_checks_passed;
					let num_issues_found = metrics.num_issues_found;
					let issues_found = metrics.issues_found;
					
					num_checks_passed_element.innerHTML = '<p>' + num_checks_passed + ' / ' + total_num_checks + ' checks passed.</p>';
					
					failed_checks_list_element.innerHTML = '';
					
					issues_found.forEach(function(issue)
					{
						failed_checks_list_element.innerHTML += '<li>' + issue + '</li>';
					});

					
					if (num_checks_passed < total_num_checks)
					{
						num_checks_passed_element.setAttribute('data-issues-found', 'yes');
					}
					
					else
					{
						num_checks_passed_element.setAttribute('data-issues-found', 'no');
					}
				}
				
				else
				{
					num_checks_passed_element.innerHTML = '<p class="error-message">Unexpected response status.</p>';
					num_checks_passed_element.setAttribute('data-issues-found', 'yes');
				}
			}
		)
		.catch(error =>
		{
			console.log({site_url, error});
			num_checks_passed_element.innerHTML = '<p class="error-message">Error checking site</p>';
			num_checks_passed_element.setAttribute('data-issues-found', 'yes');
		});
	}
	
	function sbman_validate_site_metrics(site_metrics)
	{
		if (site_metrics === null || site_metrics === undefined)
		{
			return	{
						'total_num_checks': 0,
						'num_checks_passed': 0,
						'num_issues_found': 0,
					};
		}
		
		let tests =	[
						{'key': 'plugin_version', 'acceptable_values': ['4']},
						{'key': 'php_version', 'acceptable_values': [
																		'8.1.14', //Latest GoDaddy version
																		'8.1.13', //Latest Lightsail version
																		'8.1.15', //Latest NameCheap version
																	]},
						{'key': 'num_plugin_updates', 'acceptable_values': [0]},
						{'key': 'num_theme_updates', 'acceptable_values': [0]},
						{'key': 'num_core_updates', 'acceptable_values': [0]},
						{'key': 'num_translation_updates', 'acceptable_values': [0]},
						{'key': 'total_num_updates', 'acceptable_values': [0]},
						{'key': 'comments_require_manual_approval', 'acceptable_values': ['1', 1, true]},
						{'key': 'max_links_to_hold_comment', 'acceptable_values': ['0', 0]},
						{'key': 'site_is_using_child_theme', 'acceptable_values': [true]},
						{'key': 'noindex_disabled', 'acceptable_values': ['1', 1, true]},
						{'key': 'auto_update_core_major', 'acceptable_values': ['enabled', true]},
						{'key': 'auto_update_core_minor', 'acceptable_values': ['enabled', true]},
						{'key': 'auto_update_core_dev', 'acceptable_values': ['enabled', true]},
						{'key': 'home_url_is_using_https', 'acceptable_values': [true]},
						{'key': 'site_url_is_using_https', 'acceptable_values': [true]},
						{'key': 'child_theme_configurator_active', 'acceptable_values': [false]},
						{'key': 'cf7_missing_flamingo', 'acceptable_values': [false]},
						{'key': 'cf7_missing_honeypot', 'acceptable_values': [false]},
						{'key': 'cf7_missing_recaptcha', 'acceptable_values': [false]},
					];
		
		console.log({site_metrics});
		
		let total_num_checks = 0;
		let num_checks_passed = 0;
		let num_issues_found = 0;
		let issues_found = [];
		
		tests.forEach(function(test)
		{
			let test_key = test.key;
			let acceptable_values = test.acceptable_values;
			
			let metric_value = site_metrics?.[test_key];
			let test_passed = acceptable_values.includes(metric_value);
			
			if (test_passed === true)
			{
				num_checks_passed++;
			}
			
			else
			{
				num_issues_found++;
				issues_found.push(test_key + '=' + metric_value);
			}
			
			total_num_checks++;
		});
		
		return	{
					'total_num_checks': total_num_checks,
					'num_checks_passed': num_checks_passed,
					'num_issues_found': num_issues_found,
					'issues_found': issues_found,
				};
		
	}
	
	document.querySelectorAll('.client-sites .client-site').forEach(function(client_site)
	{
		let site_status_element = client_site.querySelector('.site-status');
		let num_checks_passed_element = client_site.querySelector('.num-checks-passed');
		let failed_checks_list_element = client_site.querySelector('.failed-checks-list');
		
		let site_token = client_site.getAttribute('data-site-token');
		let site_url = client_site.getAttribute('data-site-url');
		let endpoint_type = client_site.getAttribute('data-endpoint-type');
		
		if (endpoint_type === 'wordpress')
		{
			sbman_get_wordpress_metrics(site_url, site_token, client_site);
		}
		
		else if (endpoint_type === 'none')
		{
			num_checks_passed_element.innerHTML = '';
			num_checks_passed_element.setAttribute('data-issues-found', 'no');
		}
		
		else if ((endpoint_type === null) || (endpoint_type === ''))
		{
			num_checks_passed_element.innerHTML = '<p class="error-message">No endpoint type selected.</p>';
			num_checks_passed_element.setAttribute('data-issues-found', 'yes');
		}
		
		else
		{
			num_checks_passed_element.innerHTML = '<p class="error-message">Unsupported endpoint type.</p>';
			num_checks_passed_element.setAttribute('data-issues-found', 'yes');
		}
	});
});