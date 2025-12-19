<?php
/**
 * Blocksy functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Blocksy
 */

if (version_compare(PHP_VERSION, '5.7.0', '<')) {
    require get_template_directory() . '/inc/php-fallback.php';
    return;
}

require get_template_directory() . '/inc/init.php';

// =============================================================================
// PERSONALIZACIÓN FINANMOTORS - CONFIGURACIÓN DE CORREO Y API PDF
// =============================================================================

/**
 * Configuración de correo: respetar WP Mail SMTP si está activo
 */
add_action('plugins_loaded', function() {
	// Detectar si WP Mail SMTP está activo para no sobrescribir su configuración
	$wpms_active = false;
	if (function_exists('is_plugin_active')) {
		$wpms_active = is_plugin_active('wp-mail-smtp/wp_mail_smtp.php');
	} else {
		if (defined('ABSPATH')) {
			$plugin_file = ABSPATH . 'wp-admin/includes/plugin.php';
			if (file_exists($plugin_file)) {
				require_once $plugin_file;
				if (function_exists('is_plugin_active')) {
					$wpms_active = is_plugin_active('wp-mail-smtp/wp_mail_smtp.php');
				}
			}
		}
	}

	if ($wpms_active) {
		// WP Mail SMTP gestiona PHPMailer; evitamos forzar ajustes para no entrar en conflicto
		error_log('[finanmotors] WP Mail SMTP activo: se omite override de PHPMailer.');
		return;
	}

	// Filtros de remitente (solo si no está WP Mail SMTP)
	add_filter('wp_mail_from', function($from_email) {
		return 'marketing@finanmotors.com';
	}, 999);

	add_filter('wp_mail_from_name', function($from_name) {
		return 'FINAN';
	}, 999);

	// Inicialización de PHPMailer propia (solo si no está WP Mail SMTP)
	add_action('phpmailer_init', 'finanmotors_force_simple_mail', 999);
}, 1);

function finanmotors_force_simple_mail($phpmailer) {
    // ⚠️ CONFIGURACIÓN SMTP - GMAIL / GOOGLE WORKSPACE
    $phpmailer->isSMTP();
    
    // 📧 Gmail SMTP (funciona perfectamente)
    $phpmailer->Host = 'smtp.gmail.com';
    $phpmailer->Port = 587;
    $phpmailer->SMTPSecure = 'tls';
    
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = 'marketing@finanmotors.com'; // Tu email de Google Workspace
    $phpmailer->Password = 'Qjqtr35ZgR'; // ⚠️ ← APP PASSWORD (NO la contraseña normal)
    
    $phpmailer->From = 'marketing@finanmotors.com';
    $phpmailer->FromName = 'FINAN';
    $phpmailer->CharSet = 'UTF-8';
    $phpmailer->Timeout = 30;
    
    // 🔍 DEBUG SMTP ACTIVADO
    $phpmailer->SMTPDebug = 2;
    $phpmailer->Debugoutput = 'error_log';
    
    return $phpmailer;
}

/**
 * Register REST endpoints
 */
add_action('rest_api_init', function() {
    // Endpoint para Test
    register_rest_route('pdf/v1', '/test-email', [
        'methods' => 'POST',
        'callback' => 'finanmotors_test_email',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ]);

    // Endpoint para Envío de PDF
    register_rest_route( 'pdf/v1', '/send', [
        'methods'  => 'POST',
        'callback' => 'finanmotors_handle_pdf_send',
        'permission_callback' => '__return_true',
    ] );
});

/**
 * Los controladores de las funciones (finanmotors_handle_pdf_send, finanmotors_test_email, etc.)
 * deben copiarse íntegramente a continuación para que la API funcione.
 */

function finanmotors_test_email(WP_REST_Request $request) {
	$params = $request->get_json_params();
	$test_email = isset($params['email']) ? sanitize_email($params['email']) : get_option('admin_email');
	
	if (!is_email($test_email)) {
		return new WP_REST_Response([
			'success' => false,
			'message' => 'Email inválido'
		], 400);
	}
	
	// Capturar errores de wp_mail
	$mail_errors = [];
	$mail_error_handler = function($wp_error) use (&$mail_errors) {
		if (is_wp_error($wp_error)) {
			$mail_errors[] = $wp_error->get_error_message();
		}
	};
	add_action('wp_mail_failed', $mail_error_handler);
	
	// Configurar headers simples
	$headers = [];
	$headers[] = 'From: FINAN <marketing@finanmotors.com>';
	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	
	$subject = '✅ Test de Email - FINAN';
	$message = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;">';
	$message .= '<div style="background: #00205C; color: white; padding: 20px; text-align: center; border-radius: 8px;">';
	$message .= '<h2 style="margin: 0;">✅ Test de Configuración</h2>';
	$message .= '<p style="margin: 10px 0 0 0;">Sistema de correos funcionando</p>';
	$message .= '</div>';
	$message .= '<div style="padding: 20px; background: white; border-radius: 8px; margin-top: 10px;">';
	$message .= '<p><strong>Si recibes este email, la configuración está funcionando correctamente.</strong></p>';
	$message .= '<h3>Detalles del test:</h3>';
	$message .= '<ul>';
	$message .= '<li><strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '</li>';
	$message .= '<li><strong>Servidor:</strong> ' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '</li>';
	$message .= '<li><strong>WordPress:</strong> ' . get_bloginfo('version') . '</li>';
	$message .= '<li><strong>PHP:</strong> ' . phpversion() . '</li>';
	$message .= '</ul>';
	$message .= '<div style="background: #e8f5e8; padding: 15px; border-radius: 5px; margin-top: 20px;">';
	$message .= '<p style="margin: 0; color: #2e7d32; font-weight: bold;">🎉 ¡El sistema de cotizaciones está listo para enviar PDFs!</p>';
	$message .= '</div>';
	$message .= '</div></div>';
	
	// Intentar envío
	$sent = wp_mail($test_email, $subject, $message, $headers);
	
	// Remover el handler de errores
	remove_action('wp_mail_failed', $mail_error_handler);
	
	// Construir respuesta con detalles
	$response_data = [
		'success' => $sent,
		'message' => $sent ? 'Email de prueba enviado correctamente' : 'Error al enviar email de prueba',
		'email' => $test_email,
		'timestamp' => date('Y-m-d H:i:s'),
		'mail_method' => 'PHP mail() function',
		'debug' => [
			'mail_errors' => $mail_errors,
			'php_last_error' => error_get_last(),
			'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown'
		]
	];
	
	// Si hubo errores, incluir detalles adicionales
	if (!$sent || !empty($mail_errors)) {
		$response_data['troubleshooting'] = [
			'suggestions' => [
				'Verificar que el servidor permita envío de correos',
				'Revisar configuración PHP mail()',
				'Consultar logs del servidor',
				'Verificar que no haya plugins bloqueando el envío'
			]
		];
	}
	
	return new WP_REST_Response($response_data, $sent ? 200 : 500);
}

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
				return new WP_REST_Response( [ 'success' => false, 'message' => 'No se pudo guardar el archivo PDF' ], 500 );
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
				$msg = strval( $wp_error );
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
				$request_size += strlen( serialize( $_POST ) );
			}
			if ( ! empty( $_FILES['file']['size'] ) ) {
				$request_size += intval( $_FILES['file']['size'] );
			}
		} else {
			$request_size = 0;
			if ( is_array( $params ) ) {
				$request_size = strlen( serialize( $params ) );
			}
		}
		$log( 'Approx request payload size (bytes): ' . $request_size );

		if ( ! $received_via_file ) {
			// JSON/base64 branch: validate required fields
			$required = [ 'pdf', 'correo', 'nombre' ];
			$missing = [];
			foreach ( $required as $r ) {
				if ( ! isset( $params[ $r ] ) || empty( $params[ $r ] ) ) {
					$missing[] = $r;
				}
			}
			if ( ! empty( $missing ) ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Faltan campos: ' . implode( ', ', $missing ) ], 400 );
			}

			$pdf_base64 = $params['pdf'];
			if ( ! is_string( $pdf_base64 ) ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Campo pdf debe ser string' ], 400 );
			}

			// Check size to avoid memory exhaustion (base64 length). Adjust limit if needed.
			$max_base64_length = defined( 'FINANMOTORS_PDF_PAYLOAD_LIMIT' ) ? FINANMOTORS_PDF_PAYLOAD_LIMIT : ( 12 * 1024 * 1024 ); // 12 MB base64 default
			if ( strlen( $pdf_base64 ) > $max_base64_length ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Archivo PDF demasiado grande' ], 413 );
			}

			$client_email = sanitize_email( $params['correo'] );
			if ( ! is_email( $client_email ) ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Email inválido' ], 400 );
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
				$pdf_base64 = substr( $pdf_base64, strpos( $pdf_base64, 'base64,' ) + 7 );
			}
			$pdf_base64 = preg_replace( '/\s+/', '', $pdf_base64 );

			$pdf = base64_decode( $pdf_base64 );
			if ( $pdf === false ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'No se pudo decodificar PDF' ], 400 );
			}

			// Save temporary PDF file
			$dir = trailingslashit( $upload['basedir'] ) . 'finanmotors_pdfs';
			if ( ! file_exists( $dir ) ) {
				if ( ! wp_mkdir_p( $dir ) ) {
					return new WP_REST_Response( [ 'success' => false, 'message' => 'No se pudo crear directorio para PDFs' ], 500 );
				}
			}
			$filename = 'cotizacion_' . time() . '_' . wp_generate_password( 6, false, false ) . '.pdf';
			$file_path = trailingslashit( $dir ) . $filename;
			$written = @file_put_contents( $file_path, $pdf );
			if ( $written === false ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'No se pudo guardar PDF' ], 500 );
			}
			// Validate PDF was written correctly
			if ( $written < 1024 ) { // PDF should be at least 1KB
				return new WP_REST_Response( [ 'success' => false, 'message' => 'PDF guardado incompleto' ], 500 );
			}
			$log( "PDF saved to $file_path ({$written} bytes)" );
		} else {
			// received_via_file branch: ensure client email/name are present (extracted earlier)
			if ( empty( $client_email ) || empty( $client_name ) ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Faltan campos requeridos' ], 400 );
			}
		}

		// Prepare email details
		$company_email = 'contacto@finanmotors.com';
		$from_email = 'marketing@finanmotors.com';
		$from_name = 'FINAN';

		$headers = [];
		$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
		$headers[] = 'Reply-To: ' . $from_name . ' <' . $from_email . '>';
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$headers[] = 'X-Mailer: WordPress/' . get_bloginfo('version');
		$headers[] = 'X-Priority: 1'; // High priority for company emails

		$subject_company = "Nueva cotización registrada: $client_name";
		$body_company = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
		$body_company .= '<div style="background: #00205C; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">';
		$body_company .= '<h2 style="margin: 0;">🚗 Nueva Cotización Registrada</h2>';
		$body_company .= '</div>';
		$body_company .= '<div style="padding: 20px; background: #f8f9fa; border-radius: 0 0 10px 10px;">';
		$body_company .= '<p style="font-size: 16px; margin-bottom: 20px;">Se ha registrado una nueva cotización en el sistema.</p>';
		$body_company .= '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';
		$body_company .= '<tr style="background: #e3f2fd;"><td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Cliente:</td><td style="padding: 10px; border: 1px solid #ddd;">' . esc_html( $client_name ) . '</td></tr>';
		$body_company .= '<tr><td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Email:</td><td style="padding: 10px; border: 1px solid #ddd;">' . esc_html( $client_email ) . '</td></tr>';
		if ( $client_phone ) {
			$body_company .= '<tr style="background: #e3f2fd;"><td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Teléfono:</td><td style="padding: 10px; border: 1px solid #ddd;">' . esc_html( $client_phone ) . '</td></tr>';
		}
		if ( $marca || $modelo ) {
			$body_company .= '<tr><td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Vehículo:</td><td style="padding: 10px; border: 1px solid #ddd;">' . esc_html( $marca . ' ' . $modelo ) . '</td></tr>';
		}
		if ( $precio ) {
			$body_company .= '<tr style="background: #e3f2fd;"><td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Precio referencia:</td><td style="padding: 10px; border: 1px solid #ddd;">' . esc_html( $precio ) . '</td></tr>';
		}
		$body_company .= '<tr><td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Tipo financiación:</td><td style="padding: 10px; border: 1px solid #ddd;">' . esc_html( $tipo_financiacion ) . '</td></tr>';
		$body_company .= '<tr style="background: #e3f2fd;"><td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Monto entrada:</td><td style="padding: 10px; border: 1px solid #ddd;">' . esc_html( $monto_entrada ) . '</td></tr>';
		$body_company .= '<tr><td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Porcentaje entrada:</td><td style="padding: 10px; border: 1px solid #ddd;">' . esc_html( $porcentaje_entrada ) . '</td></tr>';
		$body_company .= '<tr style="background: #fff3e0;"><td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Fecha:</td><td style="padding: 10px; border: 1px solid #ddd;">' . date('d/m/Y H:i:s') . '</td></tr>';
		$body_company .= '</table>';
		$body_company .= '<p style="text-align: center; margin-top: 20px; font-size: 14px; color: #666;">📎 La cotización completa está adjunta en el PDF.</p>';
		$body_company .= '</div></div>';

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
		$subject_client = 'Tu cotización en Finan';
		$body_client = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f8f9fa; border-radius: 10px; overflow: hidden;">';
		$body_client .= '<div style="background: linear-gradient(135deg, #00205C 0%, #1a2a5c 100%); color: white; padding: 30px; text-align: center;">';
		$body_client .= '<h1 style="margin: 0; font-size: 28px;">🎉 ¡Tu Cotización está Lista!</h1>';
		$body_client .= '<p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">Gracias por confiar en FINAN</p>';
		$body_client .= '</div>';
		$body_client .= '<div style="padding: 30px;">';
		$body_client .= '<h2 style="color: #00205C; margin-bottom: 20px;">Hola ' . esc_html( $client_name ) . ',</h2>';
		$body_client .= '<p style="font-size: 16px; line-height: 1.6; color: #333;">Hemos preparado tu cotización personalizada. Encontrarás todos los detalles en el archivo PDF adjunto.</p>';
		
		$body_client .= '<div style="background: #fff3e0; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #ff9800;">';
		$body_client .= '<h3 style="color: #e65100; margin-top: 0;">📞 Próximos Pasos:</h3>';
		$body_client .= '<p style="margin: 5px 0;">✅ Revisa tu cotización adjunta</p>';
		$body_client .= '<p style="margin: 5px 0;">✅ Un asesor especializado te contactará pronto</p>';
		$body_client .= '<p style="margin: 5px 0;">✅ Preparate para tu nueva adquisición</p>';
		$body_client .= '</div>';
		$body_client .= '<div style="text-align: center; margin-top: 30px; padding: 20px; background: #e8f5e8; border-radius: 10px;">';
		$body_client .= '<p style="margin: 0; color: #2e7d32; font-size: 16px; font-weight: bold;">¡Estamos aquí para hacer realidad tu sueño!</p>';
		$body_client .= '<p style="margin: 10px 0 0 0; color: #2e7d32;">Saludos cordiales,<br/><strong>El equipo de Finan </strong></p>';
		$body_client .= '</div>';
		$body_client .= '</div></div>';

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
					$public_url = $uploads_url_base . $relative;
				}
			}

			// Normalize client phone to Ecuador international format without '+' (WhatsApp Cloud expects plain digits)
			$raw = preg_replace( '/\D+/', '', $client_phone );
			if ( substr( $raw, 0, 1 ) === '0' ) {
				$raw = '593' . substr( $raw, 1 );
			} elseif ( strlen( $raw ) === 9 && substr( $raw, 0, 1 ) === '9' ) {
				$raw = '593' . $raw;
			} elseif ( substr( $raw, 0, 3 ) === '593' ) {
				// already good
			}

			if ( substr( $raw, 0, 3 ) === '593' ) {
				$log( "Sending WhatsApp Cloud API message to: $raw" );
				$wh_endpoint = "https://graph.facebook.com/v17.0/$wh_phone_id/messages";
				$wh_body = [
					'messaging_product' => 'whatsapp',
					'to' => $raw,
					'type' => 'template',
					'template' => [
						'name' => 'cotizacion_enviada',
						'language' => [ 'code' => 'es' ],
						'components' => [
							[
								'type' => 'body',
								'parameters' => [
									[ 'type' => 'text', 'text' => $client_name ],
								],
							],
							[
								'type' => 'button',
								'sub_type' => 'url',
								'index' => '0',
								'parameters' => [
									[ 'type' => 'text', 'text' => $public_url ],
								],
							],
						],
					],
				];
				$wh_ch = curl_init( $wh_endpoint );
				curl_setopt( $wh_ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $wh_ch, CURLOPT_POST, true );
				curl_setopt( $wh_ch, CURLOPT_HTTPHEADER, [
					'Authorization: Bearer ' . $wh_token,
					'Content-Type: application/json',
				] );
				curl_setopt( $wh_ch, CURLOPT_POSTFIELDS, wp_json_encode( $wh_body ) );
				$wh_result = curl_exec( $wh_ch );
				$wh_http = curl_getinfo( $wh_ch, CURLINFO_HTTP_CODE );
				$wh_err = curl_error( $wh_ch );
				curl_close( $wh_ch );
				$log( "WhatsApp Cloud API response: HTTP $wh_http, Body: $wh_result, Error: $wh_err" );
				$response['debug']['whatsapp_cloud'] = [ 'http_code' => $wh_http, 'curl_error' => $wh_err, 'response' => $wh_result ];
			} else {
				$log( 'Invalid phone number format after normalization: ' . $raw );
				$response['debug']['whatsapp_cloud'] = [ 'error' => 'invalid_phone_format', 'normalized' => $raw ];
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
				$public_url = $uploads_url_base . $relative;
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
				'mail_errors' => $mail_errors,
				'file_path' => $file_path,
				'file_size' => $written,
				'request_size' => $request_size,
				'method' => $received_via_file ? 'multipart/form-data' : 'json/base64',
				'timestamp' => date( 'Y-m-d H:i:s' ),
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