<!DOCTYPE html>
<!--{ Copyright 2015 Vin Wong @ vinexs.com }-->
<!--[if IE 8]>
<html class="lt-ie9"><![endif]-->
<!--[if (IE 9|gt IE 9|!(IE))]><!-->
<html><!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title><?php echo $this->lang('title'); ?></title>
    <?php if (isset($DESCRIPTION)) {
        echo '<meta name="description" content="' . $DESCRIPTION . '" />';
    } ?>
    <?php if (isset($KEYWORDS)) {
        echo '<meta name="keywords" content="' . $KEYWORDS . '" />';
    } ?>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css" />
    <link type="text/css" rel="stylesheet" class="respond" href="<?php echo $URL_RSC; ?>css/common.min.css"/>
    <script type="text/javascript" src="//code.jquery.com/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="<?php echo $URL_ASSETS; ?>jextender/1.0.8/jExtender.min.js"></script>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://cdn.materialdesignicons.com/2.1.19/css/materialdesignicons.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dialog-polyfill/0.4.9/dialog-polyfill.min.css">
    <link type="image/x-icon" rel="shortcut icon" href="<?php echo $URL_ASSETS; ?>favicon.ico"/>
    <link type="image/x-icon" rel="icon" href="<?php echo $URL_ASSETS; ?>favicon.ico"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="data-url"
          content="root=<?php echo $URL_ROOT; ?>, activity=<?php echo $URL_ACTIVITY; ?>, repos=<?php echo $URL_REPOS; ?>, rsc=<?php echo $URL_RSC; ?>"/>
</head>
<body data-lang="<?php echo $LANGUAGE; ?>">
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
    <header class="mdl-layout__header">
        <div class="mdl-layout__header-row">
        <!-- Title -->
        <span class="mdl-layout-title"><?php echo $this->lang('title'); ?></span>
        <!-- Add spacer, to align navigation to the right -->
        <div class="mdl-layout-spacer"></div>
        <!-- Navigation. We hide it in small screens. -->
        <nav class="mdl-navigation">
        <?php
    	if ($logged) {
    		?>
            <a class="mdl-navigation__link" href="#" data-event="remove_all"><i class="material-icons">delete</i></a>
            <a class="mdl-navigation__link" href="#" data-event="logout"><i class="material-icons">exit_to_app</i></a>
            <?php
        }
        ?>
        </nav>
        </div>
    </header>
    <main class="mdl-layout__content">
        <div class="page-content">
            <?php
            if (!is_array($CONTAIN_VIEW)) {
                $this->load_view($CONTAIN_VIEW);
            } else {
                foreach ($CONTAIN_VIEW as $VIEW) {
                    $this->load_view($VIEW);
                }
            }
            ?>
        </div>
    </main>
</div>
<script src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/dialog-polyfill/0.4.9/dialog-polyfill.min.js"></script>
<script type="text/javascript" src="<?php echo $URL_RSC; ?>js/common.js"></script>
<!-- Completed: <?php echo number_format(microtime(true) - INIT_TIME_START, 5); ?>s -->
</body>
</html>
