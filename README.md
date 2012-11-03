# Chester WordPress MVC Theme Framework Documentation

Chester is a lightweight wordpress MVC theming framework for people who want to build their own custom themes with an MVC approach

## Top level-navigation

Test

	<?php

	function override_swpmvc_request()
	{
	    global $post, $wp_query;
	    if ($post->ID !== 3) return;
	    $wp_query->query_vars['swpmvc_controller'] = 'PostController';
	    $wp_query->query_vars['swpmvc_method'] = 'post_three';
	}

	do_action('swpmvc_request_override', 'override_swpmvc_request');