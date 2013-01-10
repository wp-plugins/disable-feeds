<?php
/*
Plugin Name: Disable Feeds
Plugin URI: http://wordpress.org/extend/plugins/disable-feeds/
Description: Disable all RSS/Atom feeds on your WordPress site.
Version: 1.1
Author: Samir Shah
Author URI: http://rayofsolaris.net/
License: GPLv2 or later
*/

if( !defined( 'ABSPATH' ) )
	exit;

class Disable_Feeds {
	function __construct() {
		if( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_setup' ) );
		}
		else {
			add_action( 'wp_loaded', array( $this, 'remove_links' ) );
			add_filter( 'parse_query', array( $this, 'filter_query' ) );
		}
	}
	
	function admin_setup() {
		add_settings_field( 'disable_feeds_redirect', 'Disable Feeds Plugin', array( $this, 'settings_field' ), 'reading' );
		register_setting( 'reading', 'disable_feeds_redirect', array( $this, 'sanitize_settings' ) );
	}
	
	function sanitize_settings( $val ) {
		return (bool) $val;
	}
	
	function settings_field() {
		echo '<p>The <em>Disable Feeds</em> plugin is active, and all feed are disabled. By default, all requests for feeds are redirected to the corresponding HTML content. If you want to issue a 404 (page not found) response instead, uncheck the box below.</p><p><input type="checkbox" name="disable_feeds_redirect" id="disable_feeds_redirect" class="checkbox" ' . checked( get_option( 'disable_feeds_redirect', true ), true, false ) . '/><label for="disable_feeds_redirect"> Redirect feed requests to corresponding HTML content</label></p>';
	}
	
	function remove_links() {
		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}
	
	function filter_query( $wp_query ) {
		if( !is_feed() )
			return;

		if( get_option( 'disable_feeds_redirect', true ) ) {
			if( isset( $_GET['feed'] ) ) {
				wp_redirect( remove_query_arg( 'feed' ), 301 );
				exit;
			}

			set_query_var( 'feed', '' );	// redirect_canonical will do the rest
		}
		else {
			$wp_query->is_feed = false;
			$wp_query->set_404();
			status_header( 404 );
		}
	}
}

new Disable_Feeds();
