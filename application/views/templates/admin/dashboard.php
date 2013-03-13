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
        
    </head>
    <body>
    
        <div id="header">
            <h1><a href="<?php echo site_url('admin/dashboard')?>"><?php echo $app ?> | <?php echo _("Panel de administración")?></a></h1>
        </div>
        
        <div id="search">
            <input type="text" placeholder="Search here..."/><button type="submit" class="tip-right" title="Search"><i class="icon-search icon-white"></i></button>
        </div>
        
        <div id="user-nav" class="navbar navbar-inverse">
        </div>
        
        <div id="sidebar">
            <a href="#" class="visible-phone"><i class="icon icon-home"></i> Dashboard</a>
            <ul>
                <li class="active"><a href="#"><i class="icon icon-home"></i> <span>Dashboard</span></a></li>
            </ul>
        </div>
        
        <div id="content">
            <?php if (isset($content)) echo $content; ?>
        </div>

        <!-- Footer JavaScript -->
        <?php if(isset($js_footer)):?>
        <?php foreach($js_footer as $file):?>
        <script type="text/javascript" src="<?php echo base_url('/js/'.$file)?>" ></script>
        <?php endforeach;?>
        <?php endif;?>
    </body>
</html>