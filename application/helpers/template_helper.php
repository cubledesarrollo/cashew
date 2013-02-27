<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function active_menu($active, $key) {
    if ($active == $key) {
        echo 'class="active"';
    }
}

//comprobamos por url si se trata de test AB
//El test ab se diferencia porque contiene una controller/method/v/<number>
//devuelve false sino es testab, si es devuelve int corresponde a la version a mostrar
function isTest() {

    $CI = &get_instance();
    $i = 0;
    //Recorremos la uri detectamos si tiene una v y numeric despues. 
    foreach ($CI->uri->segment_array() as $seg) {

        $i = $i+1;
         
        if (strtolower($seg) == 'v') {
            if (is_numeric($CI->uri->segment($i + 1))) { 
                return $CI->uri->segment($i + 1);
            }
        }
    }
    return false;
}

function facebook_login() {
    $CI = & get_instance();
    $CI->load->library('CashewFacebook');
    $CI->cashewfacebook->init();
    ?>
    <div class="fb-login-button" data-scope="email">
    <?php echo _('Entrar con Facebook') ?>
    </div>
        <?php
    }

    function alerts() {
        $CI = & get_instance();
        if ($CI->session->flashdata('warning')):
            ?>
        <div class="alert alert-warning" data-alert="alert">
            <a class="close" data-dismiss="alert">&times;</a>
            <p><?php echo $CI->session->flashdata('warning'); ?></p>
        </div>
    <?php endif; ?>
    <?php if ($CI->session->flashdata('error')): ?>
        <div class="alert alert-error" data-alert="alert">
            <a class="close" data-dismiss="alert">&times;</a>
            <p><?php echo $CI->session->flashdata('error'); ?></p>
        </div>
    <?php endif; ?>
    <?php if ($CI->session->flashdata('success')): ?>
        <div class="alert alert-success" data-alert="alert">
            <a class="close" data-dismiss="alert">&times;</a>
            <p><?php echo $CI->session->flashdata('success'); ?></p>
        </div>
    <?php
    endif;
}
