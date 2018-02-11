/*
 * Copyright 2015 Vin Wong @ vinexs.com
 *
 * All rights reserved.
 */

(function(jQuery){

	jQuery.extend({
		url: (function(){
			var data = {};
			var $meta = $('meta[name=data-url]');
			if ($meta.length > 0) {
				var content = $meta.attr('content').split(',');
				for (var i=0; i < content.length; i++) {
					var variable = content[i].trim().split('=');
					data[variable[0]] = variable[1];
				}
			}
			return data;
		})()
	});

})(jQuery);

DOWNLOAD = {
	initLogin: function(){
		$('#login-form button[data-event=login]').click(function(e){
			e.preventDefault();
			DOWNLOAD.login();
		});
	},
	initList: function(){
		DOWNLOAD.setListEvent();
		DOWNLOAD.loadList();
		DOWNLOAD.initRequestDialog();
		DOWNLOAD.initRemoveDialog();

		$('[data-event=add-request]').click(function(e){
			e.preventDefault();
			DOWNLOAD.openRequestDialog();
		});

		$('[data-event=remove_all]').click(function(e){
			e.preventDefault();
			DOWNLOAD.openRemoveDialog('all_signature');
		});

		$('[data-event=logout]').click(function(e){
			e.preventDefault();
			DOWNLOAD.logout();
		});

		setInterval(function(){
			if ($('#download-list [data-status=downloading]').length > 0) {
				DOWNLOAD.loadList();
			}
		}, 5000);

	},
	login: function(){
		var hasError = false,
			snackbar = $('#snackbar-login-fail').get(0),
			$username = $('input[name=username]'),
			$password = $('input[name=password]');
		if ($username.isEmpty()) {
			snackbar.MaterialSnackbar.showSnackbar({
				message: $.lang('username_cannot_blank'),
				timeout: 2000,
				actionText: $.lang('retry'),
				actionHandler: function() {
					$username.focus();
				}
			});
			hasError = true;
		}
		if ($password.isEmpty()) {
			snackbar.MaterialSnackbar.showSnackbar({
				message: $.lang('password_cannot_blank'),
				timeout: 2000,
				actionText: $.lang('retry'),
				actionHandler: function() {
					$password.focus();
				}
			});
			hasError = true;
		}
		if (hasError) {
			return;
		}
		$.ajax({
			url : $.url.activity + 'session/login?'+ $.now(),
			type: 'POST',
			data: {
				username: $username.val(),
				password: $password.val(),
				keep_login: ($('#login-form #remember:checked').length == 1)
			},
			dataType: 'json',
			success: function(json){
				if (json.status != 'OK') {
					snackbar.MaterialSnackbar.showSnackbar({
						message: $.lang('username_not_exist'),
						timeout: 2000,
						actionText: $.lang('retry'),
						actionHandler: function() {
							$username.focus();
						}
					});
					return;
				}
				location.href = $.url.activity;
			}
		});
	},
	logout: function(){
		$.cookie.remove('RD_TOKEN');
		location.reload();
	},
	getMimeIcon: function(mime) {
		switch (mime) {
		// Type application
			case 'application/msword':
				return 'mdi-file-word';
			case 'application/pdf':
				return 'mdi-file-pdf';
			case 'application/vnd.ms-excel':
				return 'mdi-file-excel';
			case 'application/vnd.ms-powerpoint':
				return 'mdi-file-powerpoint';
			case 'application/x-dvi':
				return 'mdi-file-video';
			case 'application/x-shockwave-flash':
			case 'application/xhtml+xml':
				return 'mdi-file';
			case 'application/x-rar-compressed':
			case 'application/x-7z-compressed':
			case 'application/x-tar':
			case 'application/zip':
				return 'mdi-zip-box';
		// Type audio
			case 'audio/mpeg':
			case 'audio/vnd.rn-realaudio':
			case 'audio/x-wav':
			case 'audio/x-ms-wma':
				return 'mdi-file-music';
		// Type image
			case 'image/gif':
			case 'image/vnd.microsoft.icon':
			case 'image/jpeg':
			case 'image/png':
			case 'image/tiff':
			case 'image/webp':
				return 'mdi-file-image';
		// Type text
			case 'text/plain':
				return 'mdi-file-document';
			case 'text/css':
			case 'text/html':
			case 'text/javascript':
			case 'text/xml':
				return 'mdi-file-xml';
		// Type video
			case 'video/flv':
			case 'video/x-flv':
			case 'video/quicktime':
			case 'video/mpeg':
			case 'video/mp4':
			case 'video/quicktime':
			case 'video/webm':
			case 'video/x-ms-wmv':
			case 'video/3gpp':
				return 'mdi-file-video';
		// Type binary
			case 'application/octet-stream':
				return 'mdi-file';
		// Type font
			case 'application/vnd.ms-fontobject':
			case 'application/x-font-opentype':
			case 'image/svg+xml':
			case 'application/x-font-ttf':
			case 'application/x-font-woff':
				return 'mdi-file';
		}
		return 'mdi-file';
	},
	getFileSize(bytes) {
		var size = "";
		if (bytes >= Math.pow(1024, 3)) {
			return Math.round((parseInt((bytes / Math.pow(1024, 2)).toFixed()) / 1000) * 100) / 100 + ' GB'
		} else if (bytes >= Math.pow(1024, 2)) {
			return Math.round((parseInt((bytes / Math.pow(1024, 1)).toFixed()) / 1000) * 10) / 10 + ' MB';
		} else if (bytes >= 1024) {
			return Math.round(parseInt((bytes)).toFixed() / 1000) + ' KB';
		} else {
			return bytes + ' B';
		}
	},
	loadList: function(){
		$.ajax({
			url: $.url.activity +'get_list?'+ $.now(),
			dataType: 'json',
			success: function(json){
				if (json.status != 'OK') {
					if (json.data == 'list_empty') {
						$('#download-list').hide().html('');
						$('#no-downloads').show();
					}
					return;
				}
				$('#no-downloads').hide();
				$('#download-list').show();
				var list = json.data;
				var $cardTemplate = $('#item-template');
				for (var signature in list) {
					if ($('#download-list [data-signature='+ signature +']').length == 0) {
						var card = $cardTemplate.html();
						var $card = $(card.replaceAll('{SIGNATURE}', signature)
												.replaceAll('{STATUS}', list[signature].status)
												.replaceAll('{FILENAME}', list[signature].filename)
												.replaceAll('{URL}', list[signature].url)
												.replaceAll('{FILESIZE}', DOWNLOAD.getFileSize(list[signature].filesize))
												.replaceAll('{ETA}', list[signature].estimated_time)
												.replaceAll('{SPEED}', list[signature].speed));
						$card.find('header .mdi').addClass(DOWNLOAD.getMimeIcon(list[signature].filetype));
						$card.find('a.url').attr('href', list[signature].url);
						$('#download-list').prepend($card);
					} else {
						$card = $('#download-list [data-signature='+ signature +']');
					}
					$card.attr('data-status', list[signature].status);
					switch (list[signature].status) {
						case 'finished':
							$card.find('.download-progress, [data-event=retry]').hide();
							$card.find('[data-event=download]').show();
							break;
						case 'cancel':
						case 'error':
							$card.find('.download-progress, [data-event=download]').hide();
							$card.find('[data-event=retry]').show();
							break;
						case 'downloading':
							$card.find('.download-progress .speed').html(list[signature].estimated_time +' - '+ list[signature].speed);
							$card.find('.download-progress, [data-event=cancel]').show();
							var $mdlProgress = $card.find('.download-progress .mdl-progress');
							$mdlProgress.find('.progressbar').css('width', list[signature].precentage +'%');
							$mdlProgress.find('.bufferbar').css('width', list[signature].precentage +'%');
							$mdlProgress.find('.auxbar').css('width', '100%');
							break;
					}
				}
			}
		});
	},
	setListEvent: function(){
		$('#download-list')
			.on('click', '[data-signature] [data-event]', function(e){
				e.preventDefault();
				var $btn = $(this);
				var $card = $btn.parents('[data-signature]');
				var signature = $card.attr('data-signature');
				switch ($btn.attr('data-event')) {
					case 'download':
						DOWNLOAD.fileRequest(signature);
						break;
					case 'retry':
						DOWNLOAD.retryRequest($card, signature);
						break;
					case 'cancel':
						DOWNLOAD.cancelRequest($card, signature);
						break;
					case 'remove':
						DOWNLOAD.openRemoveDialog(signature);
						break;
				}
			});
	},
	initRequestDialog: function() {
		var $requestDialog = $('#request-dialog'),
			requestDialog = $requestDialog.get(0);
		$requestDialog
			.on('click', '[data-event=download]', function(e) {
				e.preventDefault();
				var hasError = false;
				var $url = $('input[name=url]');
				var $filename = $('input[name=filename]');
				if ($url.isEmpty() || !$url.val().isURL()) {
					$url.addClass('invalid');
					hasError = true;
				}
				if (hasError) {
					return;
				}
				DOWNLOAD.downloadRequest($url.val(), $filename.val());
				requestDialog.close();
			})
			.on('click', '[data-event=cancel]', function(e) {
				e.preventDefault();
				requestDialog.close();
			});
	},
	initRemoveDialog: function() {
		var $removeDialog = $('#remove-dialog'),
			removeDialog = $removeDialog.get(0);
		$removeDialog
			.on('click', '[data-event=remove]', function(e) {
				e.preventDefault();
				var signature = $('#remove-dialog [data-signature]').attr('data-signature');
				DOWNLOAD.removeRequest(signature);
				removeDialog.close();
			})
			.on('click', '[data-event=cancel]', function(e) {
				e.preventDefault();
				removeDialog.close();
			});
	},
	openRequestDialog: function() {
		var $dialog = $('#request-dialog'),
			dialog = $dialog.get(0);
		$dialog.find('input').val('');
		if (!dialog.showModal) {
			dialogPolyfill.registerDialog(dialog);
		}
		dialog.showModal();
	},
	openRemoveDialog: function(signature){
		var $dialog = $('#remove-dialog'),
			dialog = $dialog.get(0);
		$dialog.find('[data-signature]').attr('data-signature', signature);
		$dialog.find('p').text(signature == 'all_signature' ? $.lang('confirm_to_delete_ask_all') : $.lang('confirm_to_delete_ask'));
		if (!dialog.showModal) {
			dialogPolyfill.registerDialog(dialog);
		}
		dialog.showModal();
	},
	downloadRequest: function(url_link, filename) {
		$.ajax({
			url: $.url.activity + 'request?'+ $.now(),
			type: 'POST',
			data: {
				url_link: url_link,
				filename: filename
			},
			beforeSend: function(){
				$('#request-dialog').get(0).close();
			},
			dataType: 'json',
			success: function(json){
				if (json.status != 'OK') {
					return;
				}
				setTimeout(function(){
					DOWNLOAD.loadList();
				}, 1000)
			}
		});
	},
	fileRequest: function(signature) {
		post($.url.activity + 'get_file', {'signature': signature}, signature);
	},
	cancelRequest: function($card, signature){
		$.ajax({
			url: $.url.activity +'request_cancel?'+ $.now(),
			type: 'POST',
			data: {
				'signature': signature
			},
			dataType: 'json',
			success: function(json) {
				if (json.status == 'OK') {
					$card.find('.download-progress, [data-event=download], [data-event=cancel]').hide();
					$card.find('[data-event=retry]').show();
				}
			}
		});
	},
	retryRequest: function($card, signature){
		$.ajax({
			url: $.url.activity + 'request?'+ $.now(),
			type: 'POST',
			data: {
				url_link: $card.find('a.url').attr('href'),
				filename: $card.find('filename').text(),
			},
			beforeSend: function(){
				DOWNLOAD.removeRequest(signature);
				$('#request-dialog').get(0).close();
			},
			dataType: 'json',
			success: function(json){
				if (json.status != 'OK') {
					return;
				}
				setTimeout(function(){
					DOWNLOAD.loadList();
				}, 1000)
			}
		});
	},
	removeRequest: function(signature){
		$.ajax({
			url: $.url.activity +'request_remove?'+ $.now(),
			type: 'POST',
			data: {
				'signature': signature
			},
			beforeSend: function(){
				$('#remove-dialog').get(0).close();
			},
			dataType: 'json',
			success: function(json) {
				$('#remove-dialog [data-signature]').attr('data-signature', '');
				if (json.status == 'OK') {
					if ( signature == 'all_signature') {
						$('#download-list [data-signature]').remove();
					} else {
						$('#download-list [data-signature='+ signature +']').remove();
					}
					if ($('#download-list [data-signature]').length == 0) {
						$('#download-list').hide().html('');
						$('#no-downloads').show();
					}
				}
			}
		});
	}
};
