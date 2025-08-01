<?php
// Configuración para el correo de contacto
// IMPORTANTE: Actualiza estas credenciales con las reales de tu cuenta de Gmail

// Configuración SMTP para Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl');
define('SMTP_USER', 'loautech.soporte@gmail.com'); // Cambia por tu correo Gmail
define('SMTP_PASS', 'gqyt htyk vpyw nwgp'); // Cambia por tu contraseña de aplicación
define('FROM_EMAIL', 'loautech.soporte@gmail.com'); // Cambia por tu correo Gmail
define('FROM_NAME', 'Sistema Loautech');

// Correo de destino para los mensajes de contacto
define('CONTACTO_EMAIL', 'loautech.soporte@gmail.com');

// Configuración de debug (0 = sin debug, 1 = mensajes del cliente, 2 = mensajes del cliente y servidor)
define('SMTP_DEBUG', 0);

/*
INSTRUCCIONES PARA CONFIGURAR GMAIL:

1. Ve a tu cuenta de Google: https://myaccount.google.com/
2. Activa la verificación en dos pasos si no la tienes activada
3. Ve a "Seguridad" > "Contraseñas de aplicación"
4. Genera una nueva contraseña de aplicación para "Correo"
5. Usa esa contraseña en SMTP_PASS (no tu contraseña normal de Gmail)
6. Actualiza SMTP_USER y FROM_EMAIL con tu correo Gmail

EJEMPLO DE CONFIGURACIÓN:
define('SMTP_USER', 'miempresa@gmail.com');
define('SMTP_PASS', 'abcd efgh ijkl mnop'); // Contraseña de aplicación de 16 caracteres
define('FROM_EMAIL', 'miempresa@gmail.com');
*/
?> 