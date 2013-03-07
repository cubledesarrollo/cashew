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
 * Identificador de Google Analytics.
 */
$config['analytics_enabled'] = false;
$config['analytics_id'] = "XXXXXXX-XX";


/**
 * Datos del usuario de administración.
 */
$config['cashew_admin_enabled'] = false;
$config['cashew_admin_user'] = '';
$config['cashew_admin_password'] = '';

/**
 * Validación de usuarios registrados.
 * 
 * NOTA_ Para testing se crean automaticamente enabled.
 */
$config['cashew_user_validate_enabled'] = true;