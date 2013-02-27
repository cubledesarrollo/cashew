<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Idioma por defecto.
 */
$config['cashew_default_language'] = 'es';

/**
 * Ficheros de configuración de Cashew.
 */
$config['cashew_app_name'] = _('Cashew App');
$config['cashew_test_ab']  = TRUE;

/**
 * Datos del usuario de administración.
 */
$config['cashew_admin_enabled'] = true;
$config['cashew_admin_user'] = 'info@cuble.es';
$config['cashew_admin_password'] = '961531931';

/**
 * Validación de usuarios registrados.
 * 
 * NOTA_ Para testing se crean automaticamente enabled.
 */
$config['cashew_user_validate_enabled'] = true;