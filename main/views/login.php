<div class="mdl-grid">
	<div class="mdl-cell mdl-cell--3-col mdl-cell--2-col-tablet mdl-cell--hide-phone"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-tablet mdl-cell--4-col-phone">
		<div class="mdl-grid">
		    <div class="mdl-card mdl-shadow--16dp util-center util-spacing-h--40px" style="margin: 0 auto;">
		        <div class="mdl-card__title">
		            <h2 class="mdl-card__title-text"><?php echo $this->lang('login'); ?></h2>
		        </div>
		        <div class="mdl-card__supporting-text mdl-grid">
		            <form id="login-form" action="session/login" method="POST">
		                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--12-col">
		                    <label class="mdl-textfield__label" for="textfield_username"><?php echo $this->lang('username'); ?></label>
		                    <input class="mdl-textfield__input" type="text" id="textfield_username" name="username"/>
		                </div>
		                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--12-col">
		                    <label class="mdl-textfield__label" for="textfield_password"><?php echo $this->lang('password'); ?></label>
		                    <input class="mdl-textfield__input" type="password" id="textfield_password" name="password"/>
		                </div>
		                <div class="mdl-cell mdl-cell--12-col send-button" align="center">
		                    <button type="submit" class="mdl-button mdl-js-ripple-effect mdl-js-button mdl-button--raised mdl-button--colored mdl-color--primary" data-event="login">
		                        <?php echo $this->lang('login'); ?>
		                    </button>
		                </div>
		            </form>
		        </div>
		    </div>
		</div>
	</div>
	<div class="mdl-cell mdl-cell--3-col mdl-cell--2-col-tablet mdl-cell--hide-phone"></div>
</div>
<div id="snackbar-login-fail" class="mdl-js-snackbar mdl-snackbar">
	<div class="mdl-snackbar__text"></div>
	<button class="mdl-snackbar__action" type="button"></button>
</div>
<script>
$.lang(<?php echo json_encode(array(
	'username_not_exist' => $this->lang('username_not_exist'),
	'username_cannot_blank' => $this->lang('username_cannot_blank'),
	'password_cannot_blank' => $this->lang('password_cannot_blank'),
	'retry' => $this->lang('retry'),
)); ?>);
$(function(){
	DOWNLOAD.initLogin();
});
</script>
