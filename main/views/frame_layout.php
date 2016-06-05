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
    <link class="respond" type="text/css" rel="stylesheet"
          href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css">
    <link type="text/css" rel="stylesheet" class="respond" href="<?php echo $URL_RSC; ?>css/common.min.css"/>
    <script type="text/javascript" src="//code.jquery.com/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="<?php echo $URL_REPOS; ?>jextender/1.0.6/jExtender.js"></script>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
	<link type="text/css" rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link type="image/x-icon" rel="shortcut icon" href="<?php echo $URL_REPOS; ?>favicon.ico"/>
    <link type="image/x-icon" rel="icon" href="<?php echo $URL_REPOS; ?>favicon.ico"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="data-url"
          content="root=<?php echo $URL_ROOT; ?>, activity=<?php echo $URL_ACTIVITY; ?>, repos=<?php echo $URL_REPOS; ?>, rsc=<?php echo $URL_RSC; ?>"/>
</head>
<body data-lang="<?php echo $LANGUAGE; ?>">
<nav>
	<div class="nav-wrapper">
		<a href="#!" class="brand-logo"><?php echo $this->lang('title'); ?></a>
		<?php
		if ($logged) {
			?>
			<a href="#" data-activates="mobile-nav" class="button-collapse">
				<i class="material-icons">menu</i>
			</a>
			<ul class="right hide-on-med-and-down">
				<li><a href="#" data-event="remove_all"><?php echo $this->lang('remove_all'); ?></a></li>
				<li><a href="#" data-event="logout"><?php echo $this->lang('logout'); ?></a></li>
			</ul>
			<ul class="side-nav" id="mobile-nav">
				<li><a href="#" data-event="remove_all"><?php echo $this->lang('remove_all'); ?></a></li>
				<li><a href="#" data-event="logout"><?php echo $this->lang('logout'); ?></a></li>
			</ul>
			<?php
		}
		?>
	</div>
</nav>
<article id="main-container">
    <?php
    if (!is_array($CONTAIN_VIEW)) {
        $this->load_view($CONTAIN_VIEW);
    } else {
        foreach ($CONTAIN_VIEW as $VIEW) {
            $this->load_view($VIEW);
        }
    }
    ?>
</article>
<!--[if lt IE 9]>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<link id="respond-proxy" rel="respond-proxy" href="<?php echo $URL_REPOS;?>respond/1.4.2/respond-proxy.html"/>
<link id="respond-redirect" rel="respond-redirect"
      href="<?php echo $URL_ROOT;?>/assets/respond/1.4.2/respond.proxy.gif"/>
<script type="text/javascript" src="<?php echo $URL_ROOT;?>/assets/respond/1.4.2/respond.proxy.min.js"></script>
<![endif]-->
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>
<script type="text/javascript" src="<?php echo $URL_RSC; ?>js/common.js"></script>
<!-- Completed: <?php echo number_format(microtime(true) - INIT_TIME_START, 5); ?>s -->
</body>
</html>
