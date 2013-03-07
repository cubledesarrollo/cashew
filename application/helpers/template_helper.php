<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * @param unknown_type $active
 * @param unknown_type $key
 */
function active_menu($active, $key)
{
    if ($active == $key) {
        echo 'class="active"';
    }
}

/**
 * Comprobamos por url si se trata de test AB.
 * El test ab se diferencia porque contiene una controller/method/v/<number>
 * devuelve false sino es testab, si es devuelve int corresponde a la version a mostrar
 */
function is_test()
{
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

/**
 * 
 */
function google_analytics()
{
    $CI = &get_instance();
    $CI->load->config('cashew');
    $enabled = $CI->config->item('analytics_enabled');
    if ($enabled)
    {
        ?>
        <script type="text/javascript">
        
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', '<?php echo $CI->config->item('analytics_id');?>']);
          _gaq.push(['_trackPageview']);
        
          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        
        </script>
        <?php
    }
}

/**
 * 
 */
function alerts()
{
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
