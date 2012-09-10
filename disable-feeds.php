<?php
/*
Plugin Name: Disable Feeds
Plugin URI: http://wordpress.org/extend/plugins/disable-feeds/
Description: Disable all RSS/Atom feeds on your WordPress site.
Version: 1.0
Author: Samir Shah
Author URI: http://rayofsolaris.net/
License: GPLv2 or later
*/

if( !defined( 'ABSPATH' ) )
	exit;

class Disable_Feeds {
	function __construct() {
		if( ! is_admin() ) {
			add_action( 'wp_loaded', array( $this, 'remove_links' ) );
			add_filter( 'template_redirect', array( $this, 'filter_query' ), 9 );	// before redirect_canonical
		}
	}
	
	function remove_links() {
		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}
	
	function filter_query() {
		if( !is_feed() )
			return;

		if( isset( $_GET['feed'] ) ) {
			wp_redirect( remove_query_arg( 'feed' ), 301 );
			exit;
		}

		set_query_var( 'feed', '' );
		// redirect_canonical will do the rest
	}
}

new Disable_Feeds();
