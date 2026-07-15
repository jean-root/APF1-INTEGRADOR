<?php
// ============================================================
//  MAILER – Cliente SMTP mínimo (sin dependencias/Composer)
//  Envía el correo de notificación de contacto vía Gmail SMTP + STARTTLS.
//  Si MAIL_USER/MAIL_PASS no están configurados en .env, no intenta
//  enviar nada: el mensaje ya quedó guardado en BD de todas formas
//  (Proceso 3, Capítulo V). Esto evita que el formulario de contacto
//  falle si el correo no está configurado (ej. entorno local sin SMTP).
// ============================================================
class Mailer {

    public static function enviarNotificacionContacto(string $nombre, string $email, string $telefono, string $asunto, string $mensaje): bool {
        if (empty(MAIL_USER) || empty(MAIL_PASS)) {
            return false; // Sin credenciales configuradas: modo "solo BD".
        }

        $cuerpo = "Nuevo mensaje de contacto en " . APP_NAME . "\n\n"
                . "Nombre: {$nombre}\n"
                . "Email: {$email}\n"
                . "Teléfono: " . ($telefono ?: '-') . "\n"
                . "Asunto: " . ($asunto ?: '-') . "\n\n"
                . "Mensaje:\n{$mensaje}\n";

        try {
            return self::smtpSend(
                MAIL_HOST,
                MAIL_PORT,
                MAIL_USER,
                MAIL_PASS,
                MAIL_DESTINO,
                'Nuevo prospecto: ' . ($asunto ?: $nombre),
                $cuerpo,
                $email
            );
        } catch (Throwable $e) {
            // No interrumpe el flujo del formulario si el envío falla.
            error_log('[Mailer] Error al enviar correo de contacto: ' . $e->getMessage());
            return false;
        }
    }

    // Envío de correo del Vendedor hacia su prospecto/cliente (panel del vendedor).
    // Usa la misma cuenta SMTP configurada (MAIL_USER/MAIL_PASS) como remitente técnico,
    // pero el Reply-To queda con el correo del vendedor para que el cliente le responda
    // directamente a él, y el cuerpo se firma con su nombre.
    public static function enviarCorreoVendedor(
        string $paraEmail, string $paraNombre,
        string $deVendedorNombre, string $deVendedorEmail,
        string $asunto, string $cuerpo
    ): bool {
        if (empty(MAIL_USER) || empty(MAIL_PASS)) {
            return false; // Sin credenciales configuradas: no se puede enviar.
        }

        $cuerpoFinal = $cuerpo . "\n\n---\n" . $deVendedorNombre . "\n" . APP_NAME
                     . ($deVendedorEmail !== '' ? " · {$deVendedorEmail}" : '');

        try {
            return self::smtpSend(
                MAIL_HOST,
                MAIL_PORT,
                MAIL_USER,
                MAIL_PASS,
                $paraEmail,
                $asunto,
                $cuerpoFinal,
                $deVendedorEmail !== '' ? $deVendedorEmail : MAIL_USER,
                $deVendedorNombre !== '' ? $deVendedorNombre . ' vía ' . APP_NAME : APP_NAME
            );
        } catch (Throwable $e) {
            error_log('[Mailer] Error al enviar correo del vendedor: ' . $e->getMessage());
            return false;
        }
    }

    private static function smtpSend(
        string $host, int $port, string $user, string $pass,
        string $to, string $subject, string $body, string $replyTo,
        string $fromDisplayName = ''
    ): bool {
        $timeout = 10;
        $socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $timeout);
        if (!$socket) {
            throw new Exception("No se pudo conectar a {$host}:{$port} ({$errstr})");
        }

        self::expect($socket, '220');
        self::command($socket, "EHLO " . APP_NAME, '250');
        self::command($socket, "STARTTLS", '220');

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new Exception('Fallo al negociar TLS con el servidor SMTP.');
        }

        self::command($socket, "EHLO " . APP_NAME, '250');
        se