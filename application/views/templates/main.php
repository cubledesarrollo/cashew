<!doctype html>
<html lang="<?php echo current_lang()?>">
    <head>
        <?php if (isset($title)): ?>
        <title><?php echo $app ?> | <?php echo $title?></title>
        <?php else:?>
        <title><?php echo $app ?></title>
        <?php endif;?>
        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <?php if(isset($less)):?>
        <?php foreach($less as $file):?>
        <link rel="stylesheet/less" type="text/css" href="<?php echo base_url('/less/'.$file)?>" />
        <?php endforeach;?>
        <?php endif;?>

        <?php if(isset($css)):?>
        <?php foreach($css as $file):?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('/css/'.$file)?>" />
        <?php endforeach;?>
        <?php endif;?>
        
        <!--[if IE 7]>
        <link rel="stylesheet" href="<?php echo base_url('/css/font-awesome/font-awesome-ie7.min.css')?>">
        <![endif]-->
        
        <?php // Cargar entorno de JavaScript ?>
        <script type="text/javascript">
        var ENVIRONMENT = {
                site_url : "<?php echo site_url(); ?>",
                base_url : "<?php echo base_url(); ?>"
                };
        </script>
        
        <?php if(isset($js)):?>
        <?php foreach($js as $file):?>
        <script type="text/javascript" src="<?php echo base_url('/js/'.$file)?>" ></script>
        <?php endforeach;?>
        <?php endif;?>
        <link rel="shortcut icon" href="<?php echo base_url('/favicon.png')?>">
        <link rel="author" href="<?php echo base_url("humans.txt")?>" />
        <?php google_analytics(); ?>
    </head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="<?php echo site_url()?>">
                        <?php echo $app?>
                    </a>
                </div>
            </div>
        </div>
        <div class="container cashew-content">
            <div class="row">
                <div class="span12">
                <?php alerts(); ?>
                <?php if (isset($content)) echo $content; ?>
                </div>
            </div>
        </div>
        <?php if (ENVIRONMENT == 'development'): ?>
        <div class="navbar navbar-fixed-bottom">
            <div class="navbar-inner">
                <div class="container">
                    <p class="navbar-text">
                    Time: <?php echo $this->benchmark->elapsed_time();?> / 
                    Memory: <?php echo $this->benchmark->memory_usage();?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </body>
</html>