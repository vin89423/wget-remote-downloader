<div class="container">
	<div id="login-form">
		<div class="row">
			<div class="col s12">
				<div class="row">
					<div class="input-field col s12">
						<input id="username" type="text" class="validate" required>
						<label for="username" data-error="<?php echo $this->lang('username_cannot_blank') ?>"><?php echo $this->lang('username'); ?></label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<input id="password" type="password" class="validate" required>
						<label for="password" data-error="<?php echo $this->lang('password_cannot_blank') ?>"><?php echo $this->lang('password'); ?></label>
					</div>
				</div>
				<div class="row">
					<div class="col s12">
						<p>
							<input type="checkbox" id="remember">
							<label for="remember"><?php echo $this->lang('keep_login'); ?></label>
						</p>
					</div>
				</div>
				<div class="divider"></div>
				<div class="row">
					<div class="col m12">
						<p class="right-align">
							<button class="btn btn-large waves-effect waves-light" type="button" data-event="login"><?php echo $this->lang('login'); ?></button>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>		
</div>
<script>
$.lang(<?php echo json_encode(array(
	'username_not_exist' => $this->lang('username_not_exist'),
)); ?>)
$(function(){
	DOWNLOAD.initLogin();
});
</script>
