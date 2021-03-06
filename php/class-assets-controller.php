<?php
/**
 * Assets Controller Class.
 *
 * @since	1.3.0
 *
 * @package dtg\gift_aid_for_woocommerce
 */

namespace dtg\gift_aid_for_woocommerce;

/**
 * Class Assets_Controller
 *
 * Enqueues JS and CSS dependencies.
 *
 * @since	1.3.0
 *
 * @package dtg\gift_aid_for_woocommerce
 */
class Assets_Controller {

	/**
	 * Path to the root plugin file.
	 *
	 * @var 	string
	 * @access	private
	 * @since	1.3.0
	 */
	private $plugin_root;

	/**
	 * Plugin name.
	 *
	 * @var 	string
	 * @access	private
	 * @since	1.3.0
	 */
	private $plugin_name;

	/**
	 * Plugin prefix.
	 *
	 * @var 	string
	 * @access	private
	 * @since	1.3.0
	 */
	private $plugin_prefix;

	/**
	 * Debug mode status
	 *
	 * @var 	bool
	 * @access	private
	 * @since	1.3.0
	 */
	private $debug_mode;

	/**
	 * Asset Suffix
	 *
	 * @var 	string
	 * @access	private
	 * @since	1.3.0
	 */
	private $asset_suffix;

	/**
	 * Constructor.
	 *
	 * @since	1.3.0
	 */
	public function __construct() {
		$this->plugin_root 		 = DTG_GIFT_AID_ROOT;
		$this->plugin_name		 = DTG_GIFT_AID_NAME;
		$this->plugin_prefix     = DTG_GIFT_AID_PREFIX;
		$this->plugin_slug		 = DTG_GIFT_AID_SLUG;

		// Determine whether we're in debug mode, and what the
		// asset suffix should be.
		$this->debug_mode   = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? true : false;
		$this->asset_suffix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
	}

	/**
	 * Unleash Hell.
	 *
	 * @since	1.3.0
	 */
	public function run() {
		// Enqueue Front-end JS.
		add_action( 'wp_enqueue_scripts', array( $this, 'public_enqueue_scripts' ), 10 );

		// Enqueue Admin JS.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );

		// Enqueue Customizer JS.
		add_action( 'customize_preview_init', array( $this, 'customizer_preview_js' ), 10 );
	}

	/**
	 * Enqueue Public Scripts.
	 *
	 * @since	1.3.0
	 */
	public function public_enqueue_scripts() {

		$do_public_enqueue     = apply_filters( $this->plugin_prefix . 'do_public_enqueue', true );
		$do_public_css_enqueue = apply_filters( $this->plugin_prefix . 'do_public_css_enqueue', true );
		$do_public_js_enqueue  = apply_filters( $this->plugin_prefix . 'do_public_js_enqueue', true );

		// Public CSS.
		if ( $do_public_enqueue && $do_public_css_enqueue ) {
			$public_css_url  = plugins_url( '/assets/css/' . $this->plugin_slug . '-public' . $this->asset_suffix . '.css', $this->plugin_root );
			$public_css_path = dirname( $this->plugin_root ) . '/assets/css/' . $this->plugin_slug . '-public' . $this->asset_suffix . '.css';

			wp_enqueue_style(
				$this->plugin_slug . '-public-css',
				$public_css_url,
				false,
				filemtime( $public_css_path )
			);
		}

		// Public JS.
		if ( $do_public_enqueue && $do_public_js_enqueue ) {
			$public_js_url   = plugins_url( '/assets/js/' . $this->plugin_slug . '-public' . $this->asset_suffix . '.js', $this->plugin_root );
			$public_js_path  = dirname( $this->plugin_root ) . '/assets/js/' . $this->plugin_slug . '-public' . $this->asset_suffix . '.js';

			wp_enqueue_script(
				$this->plugin_slug . '-public-js',
				$public_js_url,
				array( 'jquery' ),
				filemtime( $public_js_path ),
				true
			);
		}

		// Localize the Public JS.
		wp_localize_script(
			$this->plugin_slug . '-public-js',
			'gift_aid_html',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'giftaid_ajax_security' ),
			)
		);
	}

	/**
	 * Enqueue Admin Scripts.
	 *
	 * @since	1.3.0
	 */
	public function admin_enqueue_scripts() {

		$do_admin_enqueue     = apply_filters( $this->plugin_prefix . 'do_admin_enqueue', true );
		$do_admin_css_enqueue = apply_filters( $this->plugin_prefix . 'do_admin_css_enqueue', true );
		$do_admin_js_enqueue  = apply_filters( $this->plugin_prefix . 'do_admin_js_enqueue', true );

		if ( $do_admin_enqueue && $do_admin_css_enqueue ) {
			$admin_css_url  = plugins_url( '/assets/css/' . $this->plugin_slug . '-admin' . $this->asset_suffix . '.css', $this->plugin_root );
			$admin_css_path = dirname( $this->plugin_root ) . '/assets/css/' . $this->plugin_slug . '-admin' . $this->asset_suffix . '.css';

			wp_enqueue_style(
				$this->plugin_slug . '-admin-css',
				$admin_css_url,
				false,
				filemtime( $admin_css_path )
			);
		}

		if ( $do_admin_enqueue && $do_admin_js_enqueue ) {
			$admin_js_url   = plugins_url( '/assets/js/' . $this->plugin_slug . '-admin' . $this->asset_suffix . '.js', $this->plugin_root );
			$admin_js_path  = dirname( $this->plugin_root ) . '/assets/js/' . $this->plugin_slug . '-admin' . $this->asset_suffix . '.js';

			wp_enqueue_script(
				$this->plugin_slug . '-admin-js',
				$admin_js_url,
				array( 'jquery' ),
				filemtime( $admin_js_path ),
				true
			);
		}
	}

	/**
	 * Enqueue live preview JS handlers.
	 *
	 * @since	1.3.0
	 */
	function customizer_preview_js() {
		$do_customizer_js_enqueue  = apply_filters( $this->plugin_prefix . 'do_customizer_js_enqueue', true );

		if ( $do_customizer_js_enqueue ) {
			$customizer_js_url  = plugins_url( '/assets/js/' . $this->plugin_slug . '-customizer' . $this->asset_suffix . '.js', $this->plugin_root );
			$customizer_js_path = dirname( $this->plugin_root ) . '/assets/js/' . $this->plugin_slug . '-customizer' . $this->asset_suffix . '.js';

			wp_enqueue_script( $this->plugin_slug . '-customizer', $customizer_js_url, array( 'customize-preview' ), filemtime( $customizer_js_path ), true );
		}
	}
}
