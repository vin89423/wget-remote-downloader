<div id="no-downloads">
	<div class="valign-wrapper">
		<div class="valign">
			<img src="<?php echo $URL_RSC; ?>img/no_download.png" />
			<p><?php echo $this->lang('no_download_item'); ?>...</p>
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
						<a href="#" data-event="download" style="display: none;"><?php echo $this->lang('download_to_local'); ?></a>
						<a href="#" data-event="retry" style="display: none;"><?php echo $this->lang('retry'); ?></a>
						<!--<a href="#" data-event="cancel" style="display: none;"><?php echo $this->lang('cancel_download'); ?></a>-->
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>
<div id="request-modal" class="modal">
	<div class="modal-content">
		<h4><?php echo $this->lang('create_download_request'); ?></h4>
		<div class="input-field col s12">
			<input id="url" type="url" class="validate">
			<label for="url" data-error="<?php echo $this->lang('url_link_cannot_blank'); ?>"><?php echo $this->lang('url_link'); ?></label>
		</div>
		<div class="input-field col s12">
			<input id="filename" type="text" class="validate">
			<label for="filename"><?php echo $this->lang('filename'); ?></label>
		</div>
	</div>
	<div class="modal-footer">
		<a href="#!" class="modal-action waves-effect btn-flat" data-event="cancel"><?php echo $this->lang('cancel'); ?></a>
		<a href="#!" class=" modal-action waves-effect btn-flat" data-event="download"><?php echo $this->lang('confirm_download'); ?></a>
	</div>
</div>

<div id="remove-modal" class="modal">
	<div class="modal-content">
		<p><?php echo $this->lang('confirm_to_delete_ask') ?>?</p>
	</div>
	<div class="modal-footer">
		<a href="#!" class="modal-action waves-effect btn-flat" data-event="cancel"><?php echo $this->lang('cancel'); ?></a>
		<a href="#!" class="modal-action waves-effect btn-flat" data-event="remove" data-signature=""><?php echo $this->lang('confirm_to_delete'); ?></a>
	</div>
</div>


<script>
$(function(){
	DOWNLOAD.initList();
});
</script>
