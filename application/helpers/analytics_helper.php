<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


//Construir el evento en js para la trazabilidad de estos.
function makeTrackEventJS($category = 'acquisition', $action = 'undefined', $label = 'undefined', $type = 'onclick') {

    $error = FALSE;
    $type = strtolower($type);
    $action = strtolower($action);
    $label = strtolower($label);
    $category = strtolower($category);

    //Comprobamos que el evento es valido
    switch (strtolower($type)) {
        case 'onclick':;
        case 'onsubmit':;
            break;
        default: $error = TRUE;
            break;
    }

    // Estas son las categorias permitidas
    switch ($category) {
        case 'acquisition':;
        case 'activation':;
        case 'retention':;
        case 'monetization':;
            break;
        default:$error = TRUE;
    }
    // si hay error en el tipo de evento.
    //Podiamos tambien verificar que la categoria pertenece a activacion adquisicion, retencion, monetizacion
    if (!$error) {
        //comprobamos si es usuario anonimo o no.
        $CI = &get_instance();
        if ($CI->session->userdata('user_id') === false) {
            $id = "annonymous-" . mktime();
        } else {
            $id = $this->CI->session->userdata('user_id') . "-" . mktime();
        }

        // _gaq.push(['_trackEvent', 'Downloads', 'PDF', '/salesForms/orderForm1.pdf']
        $str = "\"_trackEvent('$category','$action','$label','$id')\" ";
        return " ".$type . "=" . $str;
        
    }else{
        
        return "";
        
    } 
}

//inicializamos google analytics.
function initAnalytics(){
    
    //comprobamos si existe el fichero de configuracion
    if(is_file(APPPATH.'config/analytics.php')){  
        
        include(APPPATH.'config/analytics.php'); 
        return "<script type='text/javascript'>"
        ."//<![CDATA[ 
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount','".$config['analytics_id']."']);
	_gaq.push(['_trackPageview']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	//]]>".
        "</script>";
          
    }else{
        return "<script type='text/javascript'>console.log('problem load library analytics');</script>";
    } 
}
