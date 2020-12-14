<?php
/**
 * Astra Builder Loader.
 *
 * @package astra-builder
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Builder_Header' ) ) {

	/**
	 * Class Astra_Builder_Header.
	 */
	final class Astra_Builder_Header {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;


		/**
		 * Dynamic Methods.
		 *
		 * @var dynamic methods
		 */
		private static $methods = array();


		/**
		 *  Initiator
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			if ( Astra_Builder_Helper::$is_header_footer_builder_active ) {
				$this->remove_existing_actions();

				add_action( 'astra_header', array( $this, 'prepare_header_builder_markup' ) );

				add_action( 'body_class', array( $this, 'add_body_class' ) );

				// Header Desktop Builder.
				add_action( 'astra_masthead', array( $this, 'desktop_header' ) );

				add_action( 'astra_above_header', array( $this, 'above_header' ) );
				add_action( 'astra_primary_header', array( $this, 'primary_header' ) );
				add_action( 'astra_below_header', array( $this, 'below_header' ) );

				add_action( 'astra_render_header_column', array( $this, 'render_column' ), 10, 2 );

				// Mobile Builder.
				add_action( 'astra_mobile_header', array( $this, 'mobile_header' ) );

				add_action( 'astra_mobile_above_header', array( $this, 'mobile_above_header' ) );
				add_action( 'astra_mobile_primary_header', array( $this, 'mobile_primary_header' ) );
				add_action( 'astra_mobile_below_header', array( $this, 'mobile_below_header' ) );

				add_action( 'astra_render_mobile_header_column', array( $this, 'render_mobile_column' ), 10, 2 );

				// Load Off-Canvas Markup on Footer.
				add_action( 'wp_footer', array( $this, 'mobile_popup' ) );


				add_action( 'astra_mobile_header_content', array( $this, 'render_mobile_column' ), 10, 2 );

				add_action( 'astra_render_mobile_popup', array( $this, 'render_mobile_column' ), 10, 2 );

				// Buttons.
				for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_button; $index++ ) {
					add_action( 'astra_header_button_' . $index, array( $this, 'button_' . $index ) );
					self::$methods[] = 'button_' . $index;
				}

				// Menus.
				for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_menu; $index++ ) {
					add_action( 'astra_header_menu_' . $index, array( $this, 'menu_' . $index ) );
					self::$methods[] = 'menu_' . $index;
				}

				// Htmls.
				for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_html; $index++ ) {
					add_action( 'astra_header_html_' . $index, array( $this, 'header_html_' . $index ) );
					self::$methods[] = 'header_html_' . $index;
				}

				for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_social_icons; $index++ ) {
					add_action( 'astra_header_social_' . $index, array( $this, 'header_social_' . $index ) );
					self::$methods[] = 'header_social_' . $index;
				}

				for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_divider; $index++ ) {
					add_action( 'astra_header_divider_' . $index, array( $this, 'header_divider_' . $index ) );
					self::$methods[] = 'header_divider_' . $index;
				}

				add_action( 'astra_mobile_site_identity', __CLASS__ . '::site_identity' );
				add_action( 'astra_header_search', array( $this, 'header_search' ), 10, 1 );
				add_action( 'astra_header_woo_cart', array( $this, 'header_woo_cart' ) );
				add_action( 'astra_header_edd_cart', array( $this, 'header_edd_cart' ) );
				add_action( 'astra_header_account', array( $this, 'header_account' ) );

				add_action( 'astra_header_mobile_trigger', array( $this, 'header_mobile_trigger' ) );

				add_action( 'astra_header_menu_mobile', array( $this, 'header_mobile_menu_markup' ) );
			}

			add_action( 'astra_site_identity', __CLASS__ . '::site_identity' );
		}

		/**
		 * Callback when method not exists.
		 *
		 * @param  string $func function name.
		 * @param array  $params function parameters.
		 */
		public function __call( $func, $params ) {

			if ( in_array( $func, self::$methods, true ) ) {

				if ( 0 === strpos( $func, 'header_html_' ) ) {
					Astra_Builder_UI_Controller::render_html_markup( str_replace( '_', '-', $func ) );
				} elseif ( 0 === strpos( $func, 'button_' ) ) {
					$index = (int) substr( $func, strrpos( $func, '_' ) + 1 );
					if ( $index ) {
						Astra_Builder_UI_Controller::render_button( $index, 'header' );
					}
				} elseif ( 0 === strpos( $func, 'menu_' ) ) {
					$index = (int) substr( $func, strrpos( $func, '_' ) + 1 );
					if ( $index ) {
						Astra_Header_Menu_Component::menu_markup( $index );
					}
				} elseif ( 0 === strpos( $func, 'header_social_' ) ) {
					$index = (int) substr( $func, strrpos( $func, '_' ) + 1 );
					if ( $index ) {
						Astra_Builder_UI_Controller::render_social_icon( $index, 'header' );
					}
				} elseif ( 0 === strpos( $func, 'header_divider_' ) ) {
					$index = (int) substr( $func, strrpos( $func, '_' ) + 1 );
					if ( $index ) {
						Astra_Builder_UI_Controller::render_divider_markup( str_replace( '_', '-', $func ) );
					}
				}           
			}
		}



		/**
		 * Inherit Header base layout.
		 * Do all actions for header.
		 */
		public function header_builder_markup() {

			do_action( 'astra_header' );
		}


		/**
		 * Inherit Header base layout.
		 */
		public function prepare_header_builder_markup() {

			// Before header markup.
			do_action( 'astra_header_markup_before' );
			?>
			<header
				<?php
				echo astra_attr(
					'header',
					array(
						'id'    => 'masthead',
						'class' => join( ' ', astra_get_header_classes() ),
					)
				);
				?>
			>
				<?php 
				astra_masthead_top();

				astra_masthead();

				astra_masthead_bottom();
				
				do_action( 'astra_sticky_header_markup' );
				do_action( 'astra_bottom_header_after_markup' ); 
				?>
			</header><!-- #masthead -->

			<?php

			// After header markup.
			do_action( 'astra_header_markup_after' );
		}

		/**
		 * Remove existing Header to load Header Builder.
		 *
		 * @since 3.0.0
		 * @return void
		 */
		public function remove_existing_actions() {
			remove_action( 'astra_masthead', 'astra_masthead_primary_template' );
			remove_action( 'astra_header', 'astra_header_markup' );

			remove_action( 'astra_masthead_content', 'astra_primary_navigation_markup', 10 );

			remove_filter( 'wp_page_menu_args', 'astra_masthead_custom_page_menu_items', 10, 2 );
			remove_filter( 'wp_nav_menu_items', 'astra_masthead_custom_nav_menu_items' );
		}

		/**
		 * Header Mobile trigger
		 */
		public function header_mobile_trigger() {

			Astra_Builder_UI_Controller::render_mobile_trigger();
		}

		/**
		 * Render WooCommerce Cart.
		 */
		public function header_woo_cart() {
			if ( class_exists( 'Astra_Woocommerce' ) ) {
				echo Astra_Woocommerce::get_instance()->woo_mini_cart_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Render EDD Cart.
		 */
		public function header_edd_cart() {
			if ( class_exists( 'Easy_Digital_Downloads' ) ) {
				echo Astra_Edd::get_instance()->edd_mini_cart_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Render account icon.
		 */
		public function header_account() {
			Astra_Builder_UI_Controller::render_account();
		}

		/**
		 * Render Search icon.
		 *
		 * @param  string $device   Device name.
		 */
		public function header_search( $device = 'desktop' ) {
			echo astra_get_search( '', $device ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Render site logo.
		 */
		public static function site_identity() {
			Astra_Builder_UI_Controller::render_site_identity();
		}

		/**
		 * Call component header UI.
		 *
		 * @param string $row row.
		 * @param string $column column.
		 */
		public function render_column( $row, $column ) {

			Astra_Builder_Helper::render_builder_markup( $row, $column, 'desktop', 'header' );
		}

		/**
		 * Render desktop header layout.
		 */
		public function desktop_header() {

			get_template_part( 'template-parts/header/builder/desktop-builder-layout' );

		}

		/**
		 *  Call above header UI.
		 */
		public function above_header() {

			if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {

				get_template_part(
					'template-parts/header/builder/header',
					'row',
					array(
						'row' => 'above',
					)
				);
			} else {

				set_query_var( 'row', 'above' );
				get_template_part( 'template-parts/header/builder/header', 'row' );
			}
		}

		/**
		 *  Call primary header UI.
		 */
		public function primary_header() {

			$display_header = get_post_meta( get_the_ID(), 'ast-main-header-display', true );

			$display_header = apply_filters( 'ast_main_header_display', $display_header );

			if ( 'disabled' !== $display_header ) {

				if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {

					get_template_part(
						'template-parts/header/builder/header',
						'row',
						array(
							'row' => 'primary',
						)
					);
				} else {

					set_query_var( 'row', 'primary' );
					get_template_part( 'template-parts/header/builder/header', 'row' );
				}
			}
		}

		/**
		 *  Call below header UI.
		 */
		public function below_header() {

			if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {

				get_template_part(
					'template-parts/header/builder/header',
					'row',
					array(
						'row' => 'below',
					)
				);
			} else {

				set_query_var( 'row', 'below' );
				get_template_part( 'template-parts/header/builder/header', 'row' );
			}
		}

		/**
		 * Call mobile component header UI.
		 *
		 * @param string $row row.
		 * @param string $column column.
		 */
		public function render_mobile_column( $row, $column ) {
			Astra_Builder_Helper::render_builder_markup( $row, $column, 'mobile', 'header' );
		}

		/**
		 * Render Mobile header layout.
		 */
		public function mobile_header() {

			get_template_part( 'template-parts/header/builder/mobile-builder-layout' );

		}

		/**
		 *  Call Mobile above header UI.
		 */
		public function mobile_above_header() {

			if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {

				get_template_part(
					'template-parts/header/builder/mobile-header',
					'row',
					array(
						'row' => 'above',
					)
				);
			} else {

				set_query_var( 'row', 'above' );
				get_template_part( 'template-parts/header/builder/mobile-header', 'row' );
			}

		}

		/**
		 *  Call Mobile primary header UI.
		 */
		public function mobile_primary_header() {

			if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {

				get_template_part(
					'template-parts/header/builder/mobile-header',
					'row',
					array(
						'row' => 'primary',
					)
				);
			} else {

				set_query_var( 'row', 'primary' );
				get_template_part( 'template-parts/header/builder/mobile-header', 'row' );
			}

		}


		/**
		 *  Call Mobile below header UI.
		 */
		public function mobile_below_header() {

			if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {

				get_template_part(
					'template-parts/header/builder/mobile-header',
					'row',
					array(
						'row' => 'below',
					)
				);
			} else {

				set_query_var( 'row', 'below' );
				get_template_part( 'template-parts/header/builder/mobile-header', 'row' );
			}

		}
		/**
		 *  Call Mobile Popup UI.
		 */
		public function mobile_popup() {

			$mobile_header_type = astra_get_option( 'mobile-header-type' );

			if ( 'off-canvas' === $mobile_header_type || 'full-width' === $mobile_header_type || is_customize_preview() ) {

				Astra_Builder_Helper::render_mobile_popup_markup();
			}
		}
		/**
		 *  Call Mobile Menu Markup.
		 */
		public function header_mobile_menu_markup() {
			Astra_Mobile_Menu_Component::menu_markup();
		}
		
		/**
		 * Defines all constants
		 *
		 * @since 1.0.0
		 */
		public function define_constants() {

		}

		/**
		 * Add Body Classes
		 *
		 * @param array $classes Body Class Array.
		 * @return array
		 */
		public function add_body_class( $classes ) {

			$classes[] = 'astra-hfb-header';

			return $classes;
		}

	}

	/**
	 *  Prepare if class 'Astra_Builder_Header' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Astra_Builder_Header::get_instance();
}
