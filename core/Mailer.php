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

    private static function smtpSend(
        string $host, int $port, string $user, string $pass,
        string $to, string $subject, string $body, string $replyTo
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
        self::command($socket, "AUTH LOGIN", '334');
        self::command($socket, base64_encode($user), '334');
        self::command($socket, base64_encode($pass), '235');

        self::command($socket, "MAIL FROM:<{$user}>", '250');
        self::command($socket, "RCPT TO:<{$to}>", '250');
        self::command($socket, "DATA", '354');

        $headers = "From: " . APP_NAME . " <{$user}>\r\n"
                 . "To: <{$to}>\r\n"
                 . "Reply-To: <{$replyTo}>\r\n"
                 . "Subject: {$subject}\r\n"
                 . "MIME-Version: 1.0\r\n"
                 . "Content-Type: text/plain; charset=UTF-8\r\n\r\n";

        fwrite($socket, $headers . $body . "\r\n.\r\n");
        self::expect($socket, '250');

        self::command($socket, "QUIT", '221');
        fclose($socket);

        return true;
    }

    private static function command($socket, string $cmd, string $expectedCode): void {
        fwrite($socket, $cmd . "\r\n");
        self::expect($socket, $expectedCode);
    }

    private static function expect($socket, string $expectedCode): void {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        if (!str_starts_with($response, $expectedCode)) {
            throw new Exception("Respuesta SMTP inesperada (se esperaba {$expectedCode}): {$response}");
        }
    }
}
