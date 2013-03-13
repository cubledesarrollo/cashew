<!DOCTYPE html>
<html lang="<?php echo current_lang()?>">
    <head>
        <?php if (isset($title)): ?>
        <title><?php echo $app ?> | <?php echo $title?></title>
        <?php else:?>
        <title><?php echo $app ?> | <?php echo _("Panel de administración")?></title>
        <?php endif;?>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
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
        
        <?php // Cargar entorno de JavaScript ?>
        <script type="text/javascript">
        var ENVIRONMENT = {
                site_url : "<?php echo site_url(); ?>";
                base_url : "<?php echo base_url(); ?>";
                };
        </script>
        
        <?php if(isset($js)):?>
        <?php foreach($js as $file):?>
        <script type="text/javascript" src="<?php echo base_url('/js/'.$file)?>" ></script>
        <?php endforeach;?>
        <?php endif;?>
        <link rel="shortcut icon" href="<?php echo base_url('/favicon.png')?>">
        <link rel="author" href="<?php echo base_url("humans.txt")?>" />
        
    </head>
    <body>
        <?php if (isset($content)) echo $content; ?>
        <!-- Footer JavaScript -->
        <?php if(isset($js_footer)):?>
        <?php foreach($js_footer as $file):?>
        <script type="text/javascript" src="<?php echo base_url('/js/'.$file)?>" ></script>
        <?php endforeach;?>
        <?php endif;?>
    </body>
</html>