<?php
/**
 * Plugin Name: WP Nonce Client
 * Description: WP Nonce usage in a demo plugin
 * Author: Mehul Kaklotar
 * License: GPL2+
 **/
/**
 * Plugin Name: WP Nonce Client
 * Plugin URI: https://github.com/mehulkaklotar/wp-nonce-client
 * Description: WP Nonce usage in a demo plugin
 * Version: 0.0.1
 * Author: Mehul Kaklotar
 * Author URI: http://kaklo.me
 * Requires at least: 4.1
 * Tested up to: 4.5
 *
 * Text Domain: wp-nonce-client
 *
 * @package wpnonce_client
 * @category Core
 * @author mehulkaklotar
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use mehulkaklotar\wpnonce\NonceSetting;
use mehulkaklotar\wpnonce\NonceCreateURL;
use mehulkaklotar\wpnonce\NonceCreateField;
use mehulkaklotar\wpnonce\NonceVerify;
require_once( __DIR__ . '/vendor/autoload.php' );

if ( ! class_exists( 'NonceDemo' ) ) {
    /**
	 * NonceDemo Class
	 **/
	class NonceDemo {

		/**
		 * The post ID to be protected
		 *
		 * @var int
		 **/
		private $post_id = 1;

		/**
		 * The configuration
		 *
		 * @var NonceSetting
		 **/
		private $setting;

		/**
		 * demo
		 **/
		function run() {

			// Configure the Nonce.
			$this->setting = new NonceSetting(
				'display-post-' . $this->post_id,
				'_wp_nonce_test',
				60
			);

			add_action( 'template_redirect', array( $this, 'validate' ) );
		}

		/**
		 * Display the form
		 **/
		function form() {

			get_header();

			$field = new NonceCreateField( $this->setting );
			?>
            <h1>
				<?php esc_html_e( 'Prove that your human!', 'wp-nonce-client' ); ?>
            </h1>
            <p>
				<?php esc_html_e( 'You can view this post within 60 seconds.', 'wp-nonce-client' ); ?>
            </p>
            <form method="post" action="<?php echo esc_url( get_permalink( $this->post_id ) ); ?>">
				<?php $field->create_field( false, true ); ?>
                <button><?php esc_html_e( 'View Post', 'wp-nonce-client' ); ?></button>
            </form>
			<?php
			$url = new NonceCreateURL( $this->setting );
			?>
            <hr/>
            <p>
				<?php esc_html_e( 'Use the link to view the post:', 'wp-nonce-client' ); ?>
                <a href="<?php echo esc_url( $url->create_url( get_permalink( $this->post_id ) ) ); ?>"><?php esc_html_e( 'Click me!', 'wp-nonce-client' ); ?></a>
            </p>
			<?php

			get_footer();
		}

		/**
		 * validate the nonce
		 **/
		function validate() {

			// check if our post or not
			if ( ! is_single( $this->post_id ) ) {
				return;
			}

			$validate = new NonceVerify( $this->setting );
			if ( ! $validate->verify() ) {
				$this->form();
				exit;
			}

		}

	}

	$noncedemo = new NonceDemo();
	$noncedemo->run();
}