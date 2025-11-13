<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.4.3' );
define( 'EHP_THEME_SLUG', 'hello-elementor' );

define( 'HELLO_THEME_PATH', get_template_directory() );
define( 'HELLO_THEME_URL', get_template_directory_uri() );
define( 'HELLO_THEME_ASSETS_PATH', HELLO_THEME_PATH . '/assets/' );
define( 'HELLO_THEME_ASSETS_URL', HELLO_THEME_URL . '/assets/' );
define( 'HELLO_THEME_SCRIPTS_PATH', HELLO_THEME_ASSETS_PATH . 'js/' );
define( 'HELLO_THEME_SCRIPTS_URL', HELLO_THEME_ASSETS_URL . 'js/' );
define( 'HELLO_THEME_STYLE_PATH', HELLO_THEME_ASSETS_PATH . 'css/' );
define( 'HELLO_THEME_STYLE_URL', HELLO_THEME_ASSETS_URL . 'css/' );
define( 'HELLO_THEME_IMAGES_PATH', HELLO_THEME_ASSETS_PATH . 'images/' );
define( 'HELLO_THEME_IMAGES_URL', HELLO_THEME_ASSETS_URL . 'images/' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				get_template_directory_uri() . '/header-footer' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

require HELLO_THEME_PATH . '/theme.php';

HelloTheme\Theme::instance();

/**
 * Register REST endpoint to receive PDF and send emails.
 * Endpoint: /wp-json/pdf/v1/send
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'pdf/v1', '/send', [
		'methods'  => 'POST',
		'callback' => 'finanmotors_handle_pdf_send',
		'permission_callback' => '__return_true',
	] );
} );

/**
 * Handle incoming PDF send requests.
 * Expects JSON body with at least: pdf (base64), correo, nombre
 */
function finanmotors_handle_pdf_send( WP_REST_Request $request ) {
	try {
			$params = $request->get_json_params();

			// Prepare basic logging helper early so it can be used in all branches (including file-upload branch)
			$upload = wp_upload_dir();
			$log_file = trailingslashit( $upload['basedir'] ) . 'finanmotors_pdf_log.txt';
			$log = function( $msg ) use ( $log_file ) {
				$time = date( 'Y-m-d H:i:s' );
				error_log( "[finanmotors_pdf] $time - $msg" );
				@file_put_contents( $log_file, "[$time] $msg\n", FILE_APPEND );
			};

			// Initialize variables used later
			$file_path = '';
			$written = 0;
			$client_email = '';
			$client_name = '';
			$client_phone = '';

			// Support multipart/form-data uploads (file field named 'file') to avoid base64 JSON issues.
		$received_via_file = false;
		if ( ! empty( $_FILES ) && ! empty( $_FILES['file'] ) && is_uploaded_file( $_FILES['file']['tmp_name'] ) ) {
			$received_via_file = true;
			$upload = wp_upload_dir();
			$dir = trailingslashit( $upload['basedir'] ) . 'finanmotors_pdfs';
			if ( ! file_exists( $dir ) ) {
				wp_mkdir_p( $dir );
			}
			$filename = 'cotizacion_' . time() . '_' . wp_generate_password( 6, false, false ) . '.pdf';
			$file_path = trailingslashit( $dir ) . $filename;
			// Move uploaded file to our uploads folder
			if ( ! move_uploaded_file( $_FILES['file']['tmp_name'], $file_path ) ) {
				$log( 'Failed to move uploaded file to: ' . $file_path );
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Unable to save uploaded file on server' ], 500 );
			}
			$written = filesize( $file_path );
			// Extract fields from POST
			$client_email = sanitize_email( wp_unslash( $_POST['correo'] ?? '' ) );
			$client_name = sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) );
				$client_phone = isset( $_POST['telefono'] ) ? sanitize_text_field( wp_unslash( $_POST['telefono'] ) ) : '';
			$marca = isset( $_POST['vehiculo_marca'] ) ? sanitize_text_field( wp_unslash( $_POST['vehiculo_marca'] ) ) : '';
			$modelo = isset( $_POST['vehiculo_modelo'] ) ? sanitize_text_field( wp_unslash( $_POST['vehiculo_modelo'] ) ) : '';
			$precio = isset( $_POST['precio_vehiculo'] ) ? sanitize_text_field( wp_unslash( $_POST['precio_vehiculo'] ) ) : '';
			$tipo_financiacion = isset( $_POST['tipo_financiacion'] ) ? sanitize_text_field( wp_unslash( $_POST['tipo_financiacion'] ) ) : '';
			$monto_entrada = isset( $_POST['monto_entrada'] ) ? sanitize_text_field( wp_unslash( $_POST['monto_entrada'] ) ) : '';
			$porcentaje_entrada = isset( $_POST['porcentaje_entrada'] ) ? sanitize_text_field( wp_unslash( $_POST['porcentaje_entrada'] ) ) : '';
			$log( "Received file upload. Saved to $file_path ({$written} bytes)" );
		}

        
		$log( 'Request received.' );

		// Log received param keys (for diagnostics) but not their full values
		if ( is_array( $params ) ) {
			$log( 'Params keys: ' . implode( ',', array_keys( $params ) ) );
		} else {
			$log( 'Params not an array' );
		}

		// Capture wp_mail failures for debugging
		$mail_errors = [];
		$mail_failed_cb = function( $wp_error ) use ( & $mail_errors, $log ) {
			$msg = '';
			if ( is_wp_error( $wp_error ) ) {
				$msg = $wp_error->get_error_message();
			} elseif ( is_object( $wp_error ) || is_array( $wp_error ) ) {
				$msg = print_r( $wp_error, true );
			} else {
				$msg = (string) $wp_error;
			}
			$mail_errors[] = $msg;
			$log( 'wp_mail_failed: ' . $msg );
		};
		add_action( 'wp_mail_failed', $mail_failed_cb );

		// Capture request size for diagnostics
		if ( $received_via_file ) {
			// approximate size from $_POST and uploaded file
			$request_size = 0;
			if ( ! empty( $_POST ) ) {
				$request_size = strlen( wp_json_encode( $_POST ) );
			}
			if ( ! empty( $_FILES['file']['size'] ) ) {
				$request_size += (int) $_FILES['file']['size'];
			}
		} else {
			$request_size = 0;
			if ( is_array( $params ) ) {
				$request_size = strlen( wp_json_encode( $params ) );
			}
		}
		$log( 'Approx request payload size (bytes): ' . $request_size );

		if ( ! $received_via_file ) {
			// JSON/base64 branch: validate required fields
			$required = [ 'pdf', 'correo', 'nombre' ];
			$missing = [];
			foreach ( $required as $r ) {
				if ( empty( $params[ $r ] ) ) {
					$missing[] = $r;
				}
			}
			if ( ! empty( $missing ) ) {
				$msg = 'Missing required fields: ' . implode( ',', $missing );
				$log( $msg );
				return new WP_REST_Response( [ 'success' => false, 'message' => $msg ], 400 );
			}

			$pdf_base64 = $params['pdf'];
			if ( ! is_string( $pdf_base64 ) ) {
				$log( 'PDF payload not a string' );
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Invalid PDF payload' ], 400 );
			}

			// Check size to avoid memory exhaustion (base64 length). Adjust limit if needed.
			$max_base64_length = defined( 'FINANMOTORS_PDF_PAYLOAD_LIMIT' ) ? FINANMOTORS_PDF_PAYLOAD_LIMIT : ( 12 * 1024 * 1024 ); // 12 MB base64 default
			if ( strlen( $pdf_base64 ) > $max_base64_length ) {
				$log( 'PDF payload too large: ' . strlen( $pdf_base64 ) );
				return new WP_REST_Response( [ 'success' => false, 'message' => 'PDF too large. Use smaller file or switch to multipart upload.' ], 413 );
			}

			$client_email = sanitize_email( $params['correo'] );
			if ( ! is_email( $client_email ) ) {
				$log( 'Invalid client email: ' . (string) $params['correo'] );
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Correo inválido' ], 400 );
			}
			$client_name = sanitize_text_field( $params['nombre'] );
			$client_phone = isset( $params['telefono'] ) ? sanitize_text_field( $params['telefono'] ) : '';

			// Optional fields
			$marca = isset( $params['vehiculo_marca'] ) ? sanitize_text_field( $params['vehiculo_marca'] ) : '';
			$modelo = isset( $params['vehiculo_modelo'] ) ? sanitize_text_field( $params['vehiculo_modelo'] ) : '';
			$precio = isset( $params['precio_vehiculo'] ) ? sanitize_text_field( $params['precio_vehiculo'] ) : '';
			$tipo_financiacion = isset( $params['tipo_financiacion'] ) ? sanitize_text_field( $params['tipo_financiacion'] ) : '';
			$monto_entrada = isset( $params['monto_entrada'] ) ? sanitize_text_field( $params['monto_entrada'] ) : '';
			$porcentaje_entrada = isset( $params['porcentaje_entrada'] ) ? sanitize_text_field( $params['porcentaje_entrada'] ) : '';

			// Clean data URI prefix if present and remove whitespace
			if ( strpos( $pdf_base64, 'base64,' ) !== false ) {
				$pdf_base64 = preg_replace( '/^data:application\/(pdf|octet-stream);base64,/', '', $pdf_base64 );
				$pdf_base64 = preg_replace( '/^data:.*;base64,/', '', $pdf_base64 );
			}
			$pdf_base64 = preg_replace( '/\s+/', '', $pdf_base64 );

			$pdf = base64_decode( $pdf_base64 );
			if ( $pdf === false ) {
				$log( 'Failed to base64_decode the PDF.' );
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Invalid PDF data' ], 400 );
			}

			// Save temporary PDF file
			$dir = trailingslashit( $upload['basedir'] ) . 'finanmotors_pdfs';
			if ( ! file_exists( $dir ) ) {
				if ( ! wp_mkdir_p( $dir ) ) {
					$log( 'Failed to create directory: ' . $dir );
					return new WP_REST_Response( [ 'success' => false, 'message' => 'Unable to create directory on server' ], 500 );
				}
			}
			$filename = 'cotizacion_' . time() . '_' . wp_generate_password( 6, false, false ) . '.pdf';
			$file_path = trailingslashit( $dir ) . $filename;
			$written = @file_put_contents( $file_path, $pdf );
			if ( $written === false ) {
				$log( "Failed to write PDF to $file_path" );
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Unable to save PDF on server' ], 500 );
			}
			$log( "PDF saved to $file_path ({$written} bytes)" );
		} else {
			// received_via_file branch: ensure client email/name are present (extracted earlier)
			if ( empty( $client_email ) || empty( $client_name ) ) {
				$log( 'Missing correo/nombre in multipart POST' );
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Missing correo or nombre in multipart form' ], 400 );
			}
		}

		// Prepare email details
		$company_email = 'contacto@finanmotors.com';
		$from_email = 'wordpress@finanmotors.com';
		$from_name = 'Finan Motors';

		$headers = [];
		$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
		$headers[] = 'Reply-To: ' . $from_name . ' <' . $from_email . '>';
		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		$subject_company = "Nueva cotización registrada: $client_name";
		$body_company = '<p>Se ha registrado una nueva cotización.</p>';
		$body_company .= '<p><strong>Cliente:</strong> ' . esc_html( $client_name ) . ' &lt;' . esc_html( $client_email ) . '&gt;</p>';
		if ( $marca || $modelo ) {
			$body_company .= '<p><strong>Vehículo:</strong> ' . esc_html( $marca ) . ' - ' . esc_html( $modelo ) . '</p>';
		}
		if ( $precio ) {
			$body_company .= '<p><strong>Precio referencia:</strong> ' . esc_html( $precio ) . '</p>';
		}
		$body_company .= '<p><strong>Tipo financiación:</strong> ' . esc_html( $tipo_financiacion ) . '</p>';
		$body_company .= '<p><strong>Monto entrada:</strong> ' . esc_html( $monto_entrada ) . '</p>';
		$body_company .= '<p><strong>Porcentaje entrada:</strong> ' . esc_html( $porcentaje_entrada ) . '</p>';

		// Attachments (only include if file exists)
		$attachments = [];
		if ( ! empty( $file_path ) && file_exists( $file_path ) ) {
			$attachments[] = $file_path;
		}

		// Send to company
		$log( "Sending email to company: $company_email" );
		$sent_company = wp_mail( $company_email, $subject_company, $body_company, $headers, $attachments );
		$log( 'Company email sent: ' . ( $sent_company ? 'yes' : 'no' ) );

		// Send to client
		$subject_client = 'Tu cotización en Finan Motors';
		$body_client = '<p>Hola ' . esc_html( $client_name ) . ',</p>';
		$body_client .= '<p>Adjuntamos tu cotización. Un asesor se pondrá en contacto contigo pronto.</p>';
		$body_client .= '<p><strong>Resumen:</strong></p>';
		if ( $marca || $modelo ) {
			$body_client .= '<p>' . esc_html( $marca ) . ' - ' . esc_html( $modelo ) . '</p>';
		}
		if ( $precio ) {
			$body_client .= '<p><strong>Precio referencia:</strong> ' . esc_html( $precio ) . '</p>';
		}
		$body_client .= '<p>Saludos,<br/>Finan Motors</p>';

		$log( "Sending email to client: $client_email" );
		$sent_client = wp_mail( $client_email, $subject_client, $body_client, $headers, $attachments );
		$log( 'Client email sent: ' . ( $sent_client ? 'yes' : 'no' ) );

		// ---- Optional: send SMS / WhatsApp with PDF link via Twilio if telefono provided ----
		// Requirements (define these constants in wp-config.php or elsewhere):
		// TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, TWILIO_FROM (e.g. "+1234567890"), optional TWILIO_USE_WHATSAPP (true/false)
		// First, support sending via WhatsApp Cloud API (Meta) if configured.
		if ( ! empty( $client_phone ) && defined( 'WHATSAPP_CLOUD_TOKEN' ) && defined( 'WHATSAPP_PHONE_ID' ) ) {
			$wh_token = WHATSAPP_CLOUD_TOKEN;
			$wh_phone_id = WHATSAPP_PHONE_ID;

			// Build public URL for uploaded PDF
			$upload = wp_upload_dir();
			$public_url = '';
			if ( ! empty( $file_path ) && file_exists( $file_path ) ) {
				$uploads_base = trailingslashit( $upload['basedir'] );
				$uploads_url_base = trailingslashit( $upload['baseurl'] );
				if ( strpos( $file_path, $uploads_base ) === 0 ) {
					$relative = substr( $file_path, strlen( $uploads_base ) );
					$public_url = $uploads_url_base . str_replace( '\\', '/', $relative );
				}
			}

			// Normalize client phone to Ecuador international format without '+' (WhatsApp Cloud expects plain digits)
			$raw = preg_replace( '/\D+/', '', $client_phone );
			if ( substr( $raw, 0, 1 ) === '0' ) {
				$raw = '593' . substr( $raw, 1 );
			} elseif ( strlen( $raw ) === 9 && substr( $raw, 0, 1 ) === '9' ) {
				$raw = '593' . $raw;
			} elseif ( substr( $raw, 0, 3 ) === '593' ) {
				// ok
			}

			if ( substr( $raw, 0, 3 ) === '593' ) {
				$to_number = $raw; // e.g. 5939xxxxxxx

				// Prepare payload to send document message via WhatsApp Cloud API
				$payload = [
					'messaging_product' => 'whatsapp',
					'to' => $to_number,
					'type' => 'document',
					'document' => [
						'link' => $public_url,
						'filename' => isset( $filename ) ? $filename : 'cotizacion.pdf',
					],
				];

				$wh_url = 'https://graph.facebook.com/v16.0/' . $wh_phone_id . '/messages';
				$ch = curl_init( $wh_url );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_POST, true );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, [
					'Authorization: Bearer ' . $wh_token,
					'Content-Type: application/json',
				] );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, wp_json_encode( $payload ) );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 15 );
				$wh_result = curl_exec( $ch );
				$wh_http = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
				$wh_err = curl_error( $ch );
				curl_close( $ch );

				$log( 'WhatsApp Cloud API attempted. HTTP code: ' . $wh_http . ', curl_err: ' . $wh_err . ', response: ' . substr( $wh_result, 0, 1000 ) );
				// Expose Cloud API response in debug for easier troubleshooting
				$response['debug']['whatsapp_cloud'] = [ 'http_code' => $wh_http, 'curl_error' => $wh_err, 'response' => $wh_result ];
			} else {
				$log( 'Unable to normalize client phone for WhatsApp Cloud API: ' . $client_phone );
				$response['debug']['whatsapp_cloud'] = [ 'error' => 'phone_normalization_failed', 'input' => $client_phone ];
			}

		} elseif ( ! empty( $client_phone ) ) {
			// WhatsApp Cloud API not configured, and Twilio integration was removed per request.
			$log( 'Client phone provided but no WhatsApp provider configured; skipping send.' );
			$response['debug']['whatsapp'] = [ 'error' => 'no_provider_configured' ];
		}

		// Remove mail_failed listener to avoid leaks
		remove_action( 'wp_mail_failed', $mail_failed_cb );

		// Provide a publicly accessible URL for the saved PDF (if inside uploads)
		$public_url = '';
		if ( ! empty( $file_path ) && file_exists( $file_path ) ) {
			$uploads_base = trailingslashit( $upload['basedir'] );
			$uploads_url_base = trailingslashit( $upload['baseurl'] );
			if ( strpos( $file_path, $uploads_base ) === 0 ) {
				$relative = substr( $file_path, strlen( $uploads_base ) );
				$public_url = $uploads_url_base . str_replace( '\\', '/', $relative );
			}
			// NOTE: keep the file for later inspection; if you prefer to remove it, uncomment the unlink below.
			// @unlink( $file_path );
			$log( "Temporary file retained: $file_path" );
		}

		// Build response
		$response = [
			'success' => ( $sent_company || $sent_client ),
			'sent_company' => (bool) $sent_company,
			'sent_client' => (bool) $sent_client,
			'message' => '',
			// public_url is a publicly accessible link to the saved PDF in uploads (if available)
			'public_url' => $public_url,
			'debug' => [
				'pdf_saved_path' => isset( $file_path ) ? $file_path : null,
				'pdf_bytes' => isset( $written ) ? (int) $written : null,
				'attachments' => isset( $attachments ) ? $attachments : [],
				'headers' => isset( $headers ) ? $headers : [],
				'request_size_bytes' => $request_size,
				'mail_errors' => $mail_errors,
				'php_last_error' => error_get_last(),
			],
		];
		if ( ! $sent_company && ! $sent_client ) {
			// Do not return HTTP 500 for email sending failures; return 200 with success=false
			$response['message'] = 'No se pudo enviar ninguno de los correos.';
			$log( 'Neither email sent successfully. See debug.mail_errors and php_last_error.' );
			// Keep response.success = false and include debug info so frontend can handle gracefully
			return new WP_REST_Response( $response, 200 );
		}

		$response['message'] = 'Correo(s) enviado(s) correctamente.';
		$log( 'Handler finished successfully.' );
		return new WP_REST_Response( $response, 200 );
	} catch ( Throwable $e ) {
		// Catch any unexpected error, log details and return generic message
		$upload = wp_upload_dir();
		$log_file = trailingslashit( $upload['basedir'] ) . 'finanmotors_pdf_log.txt';
		$time = date( 'Y-m-d H:i:s' );
		error_log( "[finanmotors_pdf] $time - Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() );
		@file_put_contents( $log_file, "[$time] Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n", FILE_APPEND );
		// Return more details for debugging (remove or reduce in production)
		$last_error = error_get_last();
		$trace = $e->getTraceAsString();
		$trace_short = substr( $trace, 0, 1000 );
		return new WP_REST_Response( [
			'success' => false,
			'message' => 'Server error - check logs',
			'exception' => [ 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $trace_short ],
			'php_last_error' => $last_error,
		], 500 );
	}
}
