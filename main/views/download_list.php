<div id="no-downloads" style="display: flex; height: 70vh; justify-content: center; align-items: center;">
	<div class="img-caption" style="width: 300px; height: 300px;">
		<img src="<?php echo $URL_RSC; ?>img/no_download.png" />
		<p><?php echo $this->lang('no_download_item'); ?>...</p>
	</div>
</div>

<div id="download-list"></div>

<div class="fixed-action-btn" style="position: fixed; right: 5vw; bottom: 10vw; z-index: 1;">
	<button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored mdl-shadow--4dp mdl-color--accent" data-event="add-request">
	    <i class="material-icons">add</i>
	</button>
</div>

<div id="item-template" style="display: none;">
	<div class="mdl-grid mdl-grid--no-spacing mdl-shadow--2dp" data-name="item" data-pid="{PID}" data-signature="{SIGNATURE}" data-status="{STATUS}" style="margin-bottom: 8px;">
		<header class="mdl-cell mdl-cell--2-col mdl-cell--2-col-tablet mdl-cell--hide-phone" style="display: flex; justify-content: center; align-items: center;">
			<i class="mdi" style="font-size: 42px;"></i>
		</header>
		<div class="mdl-cell mdl-cell--10-col mdl-cell--6-col-tablet mdl-cell--4-col-phone">
			<div class="mdl-card" style="width: 100%; min-height: auto;">
				<div class="mdl-card__title">
					<label class="mdl-card__title-text" style="font-size: 12px;"><span class="filename" style="font-size: 16px;">{FILENAME}</span></label>
				</div>
				<div class="mdl-card__supporting-text">
					<a class="url" href="#" target="_blank" style="display: block; overflow: hidden; text-overflow: ellipsis;white-space: nowrap;">{URL}</a>
					<div class="download-progress" style="display: none; padding-top: 25px;">
						<div class="progress">
							<div class="mdl-progress mdl-js-progress is-upgraded"></div>
						</div>
						<div class="mdl-grid">
							<div class="mdl-cell mdl-cell--6 mdl-cell--4-col-tablet mdl-cell--2-col-phone speed">-/kbps</div>
							<div class="mdl-cell mdl-cell--6 mdl-cell--4-col-tablet mdl-cell--2-col-phone filesize">{FILESIZE}</div>
						</div>
					</div>
				</div>
				<div class="mdl-card__actions mdl-card--border">
					<a href="#" class="mdl-button" data-event="download" style="display: none;">
						<i class="material-icons">cloud_download</i> <?php echo $this->lang('download_to_local'); ?>
					</a>
					<a href="#" class="mdl-button" data-event="retry" style="display: none;"><?php echo $this->lang('retry'); ?></a>
					<a href="#" class="mdl-button" data-event="cancel" style="display: none;"><?php echo $this->lang('cancel_download'); ?></a>
				</div>
				<div class="mdl-card__menu">
					<button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" data-event="remove">
						<i class="material-icons">close</i>
						<span class="mdl-button__ripple-container"><span class="mdl-ripple"></span></span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<dialog id="request-dialog" class="mdl-dialog" style="width: 80%; max-width: 560px;">
	<h4 class="mdl-dialog__title"><?php echo $this->lang('create_download_request'); ?></h4>
	<div class="mdl-dialog__content">
		<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--12-col">
			<label class="mdl-textfield__label" for="textfield_url"><?php echo $this->lang('url_link'); ?></label>
			<input class="mdl-textfield__input" type="text" id="textfield_url" name="url"/>
		</div>
		<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--12-col">
			<label class="mdl-textfield__label" for="textfield_filename"><?php echo $this->lang('filename'); ?></label>
			<input class="mdl-textfield__input" type="text" id="textfield_filename" name="filename"/>
		</div>
	</div>
    <div class="mdl-dialog__actions">
		<button type="button" class="mdl-button" data-event="download"><?php echo $this->lang('confirm_download'); ?></a>
		<button type="button" class="mdl-button close" data-event="cancel"><?php echo $this->lang('close'); ?></a>
	</div>
</dialog>

<dialog id="remove-dialog" class="mdl-dialog" style="width: 80%; max-width: 560px;">
	<div class="mdl-dialog__content">
		<p><?php echo $this->lang('confirm_to_delete_ask') ?>?</p>
	</div>
	<div class="mdl-dialog__actions">
		<button type="button" class="mdl-button" data-event="remove" data-signature=""><?php echo $this->lang('confirm_to_delete'); ?></a>
		<button type="button" class="mdl-button close" data-event="cancel"><?php echo $this->lang('close'); ?></a>
	</div>
</dialog>

<script>
$.lang(<?php echo json_encode(array(
	'confirm_to_delete_ask' => $this->lang('confirm_to_delete_ask'),
	'confirm_to_delete_ask_all' => $this->lang('confirm_to_delete_ask_all'),
)); ?>);
$(function(){
	DOWNLOAD.initList();
});
</script>
