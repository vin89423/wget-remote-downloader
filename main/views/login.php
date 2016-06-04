<div class="container">
	<div id="login-form">
		<div class="row">
			<div class="col s12">
				<div class="row">
					<div class="input-field col s12">
						<input id="username" type="text" class="validate" required>
						<label for="username" data-error="用戶名不能是空的">用戶名</label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<input id="password" type="password" class="validate" required>
						<label for="password" data-error="請輸入密碼">密碼</label>
					</div>
				</div>
				<div class="row">
					<div class="col s12">
						<p>
							<input type="checkbox" id="remember">
							<label for="remember">保持登入</label>
						</p>
					</div>
				</div>
				<div class="divider"></div>
				<div class="row">
					<div class="col m12">
						<p class="right-align">
							<button class="btn btn-large waves-effect waves-light" type="button" data-event="login">登入</button>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>		
</div>
<script>
$(function(){
	DOWNLOAD.initLogin();
});
</script>