<div id="no-downloads">



	<div class="valign-wrapper">
		<div class="valign">
			<img src="<?php echo $URL_RSC; ?>img/no_download.png" />
			<p>這裡沒有任何內容...</p>
		</div>
	</div>
	
	
	
</div>

<div id="download-list"></div>

<div class="fixed-action-btn">
	<a class="btn-floating btn-large red" data-event="add-request">
		<i class="material-icons">mode_edit</i>
	</a>
</div>

<div id="item-template" style="display: none;">
	<div class="item row">
		<div class="col s12">
			<div class="card" data-signature="{SIGNATURE}" data-status="{STATUS}">
				<div class="card-content">
					<div class="file-icon valign-wrapper">
						<div class="valign">
							<i class="fa fa-2x"></i>
						</div>
					</div>
					<div class="close">
						<button class="btn waves-effect" data-event="remove">✕</button>
					</div>
					<a class="filename" href="#" target="_blank">{FILENAME}</a>
					<a class="url" href="#" target="_blank">{URL}</a>
					<div class="action-ctrl">
						<div class="progress" style="display: none;">
							<div class="determinate" style="width: 0%"></div>
						</div>
						<a href="#" data-event="download" style="display: none;">下載到本機</a>
						<a href="#" data-event="retry" style="display: none;">重試</a>
						<!--<a href="#" data-event="cancel" style="display: none;">取消下載</a>-->
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>

<div id="request-modal" class="modal">
	<div class="modal-content">
		<h4>建立下載計劃</h4>
		<div class="input-field col s12">
			<input id="url" type="url" class="validate">
			<label for="url" data-error="連結不能是空的">網址連結</label>
		</div>
		<div class="input-field col s12">
			<input id="filename" type="text" class="validate">
			<label for="filename">檔案名稱</label>
		</div>
	</div>
	<div class="modal-footer">
		<a href="#!" class="modal-action waves-effect btn-flat" data-event="cancel">取消</a>
		<a href="#!" class=" modal-action waves-effect btn-flat" data-event="download">確認下載</a>
	</div>
</div>

<div id="remove-modal" class="modal">
	<div class="modal-content">
		<p>你確要刪除這個項目嗎?</p>
	</div>
	<div class="modal-footer">
		<a href="#!" class="modal-action waves-effect btn-flat" data-event="cancel">取消</a>
		<a href="#!" class="modal-action waves-effect btn-flat" data-event="remove" data-signature="">確認刪除</a>
	</div>
</div>


<script>
$(function(){
	DOWNLOAD.initList();
});
</script>