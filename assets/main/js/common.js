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

		$('a[data-event=add-request]').click(function(e){
			e.preventDefault();
			DOWNLOAD.openRequestDialog();
		});

		$('a[data-event=remove_all]').click(function(e){
			e.preventDefault();
			DOWNLOAD.removeRequest('all_signature');
		});

		$('a[data-event=logout]').click(function(e){
			e.preventDefault();
			DOWNLOAD.logout();
		});

		$(".button-collapse").sideNav();

		setInterval(function(){
			if ($('#download-list .card[data-status=downloading]').length > 0) {
				DOWNLOAD.loadList();
			}
		}, 5000);

	},
	login: function(){
		var hasError = false;
		var $username = $('input#username');
		var $password = $('input#password');
		if ($username.isEmpty()) {
			$username.addClass('invalid');
			hasError = true;
		}
		if ($password.isEmpty()) {
			$password.addClass('invalid');
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
					return Materialize.toast($.lang('username_not_exist'), 4000);
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
				return 'fa-file-word-o';
			case 'application/pdf':
				return 'fa-file-pdf-o';
			case 'application/vnd.ms-excel':
				return 'fa-file-excel-o';
			case 'application/vnd.ms-powerpoint':
				return 'fa-file-powerpoint-o';
			case 'application/x-dvi':
				return 'fa-file-video-o';
			case 'application/x-shockwave-flash':
			case 'application/xhtml+xml':
				return 'fa-file-o';
			case 'application/x-rar-compressed':
			case 'application/x-7z-compressed':
			case 'application/x-tar':
			case 'application/zip':
				return 'fa-file-archive-o';
		// Type audio
			case 'audio/mpeg':
			case 'audio/vnd.rn-realaudio':
			case 'audio/x-wav':
			case 'audio/x-ms-wma':
				return 'fa-file-audio-o';
		// Type image
			case 'image/gif':
			case 'image/vnd.microsoft.icon':
			case 'image/jpeg':
			case 'image/png':
			case 'image/tiff':
			case 'image/webp':
				return 'fa-file-image-o';
		// Type text
			case 'text/plain':
				return 'fa-file-text-o';
			case 'text/css':
			case 'text/html':
			case 'text/javascript':
			case 'text/xml':
				return 'fa-file-code-o';
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
				return 'fa-file-video-o';
		// Type binary
			case 'application/octet-stream':
				return 'fa-file-o';
		// Type font
			case 'application/vnd.ms-fontobject':
			case 'application/x-font-opentype':
			case 'image/svg+xml':
			case 'application/x-font-ttf':
			case 'application/x-font-woff':
				return 'fa-font';
		}
		return 'fa-file-o';
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
					if ($('.card[data-signature='+ signature +']').length == 0) {
						var card = $cardTemplate.html();
						var $card = $(card.replaceAll('{SIGNATURE}', signature)
												.replaceAll('{STATUS}', list[signature].status)
												.replaceAll('{FILENAME}', list[signature].filename)
												.replaceAll('{URL}', list[signature].url)
												.replaceAll('{FILESIZE}', DOWNLOAD.getFileSize(list[signature].filesize))
												.replaceAll('{ETA}', list[signature].estimated_time)
												.replaceAll('{SPEED}', list[signature].speed));
						$card.find('.file-icon i.fa').addClass(DOWNLOAD.getMimeIcon(list[signature].filetype));
						$card.find('a.filename, a.url').attr('href', list[signature].url);
						$('#download-list').prepend($card);
					} else {
						$card = $('.card[data-signature='+ signature +']');
					}
					$card.attr('data-status', list[signature].status);
					switch (list[signature].status) {
						case 'finished':
							$card.find('.download-progress, .action-ctrl a[data-event]').hide();
							$card.find('[data-event=download]').show();
							break;
						case 'cancel':
						case 'error':
							$card.find('.download-progress, .action-ctrl a[data-event]').hide();
							$card.find('[data-event=retry]').show();
							break;
						case 'downloading':
							$card.find('.download-progress .speed').html(list[signature].estimated_time +' - '+ list[signature].speed);
							$card.find('.download-progress .determinate').css('width', list[signature].precentage +'%');
							$card.find('.download-progress, [data-event=cancel]').show();
							break;
					}
				}
			}
		});
	},
	setListEvent: function(){
		$('#download-list').on('click', '.card [data-event]', function(e){
			e.preventDefault();
			var $btn = $(this);
			var $card = $btn.parents('.card[data-signature]');
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
	initRequestDialog: function(){
		$('#request-modal a[data-event=download]').click(function(e){
			e.preventDefault();
			var hasError = false;
			var $url = $('input#url');
			var $filename = $('input#filename');
			if ($url.isEmpty() || !$url.val().isURL()) {
				$url.addClass('invalid');
				hasError = true;
			}
			if (hasError) {
				return;
			}
			DOWNLOAD.downloadRequest($url.val(), $filename.val());
		});

		$('#request-modal a[data-event=cancel]').click(function(e){
			e.preventDefault();
			$('#request-modal').closeModal();
		});
	},
	initRemoveDialog: function(){
		$('#remove-modal [data-event=remove]').click(function(e){
			e.preventDefault();
			var signature = $('#remove-modal [data-signature]').attr('data-signature');
			DOWNLOAD.removeRequest(signature);
		});

		$('#remove-modal [data-event=cancel]').click(function(e){
			e.preventDefault();
			$('#remove-modal').closeModal();
		});
	},
	openRequestDialog: function() {
		$('#request-modal input').val('');
		$('#request-modal').openModal({
			dismissible: false
		});
	},
	openRemoveDialog: function(signature){
		$('#remove-modal').find('[data-signature]').attr('data-signature', signature);
		$('#remove-modal').openModal();
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
				$('#request-modal').closeModal();
			},
			dataType: 'json',
			success: function(json){
				if (json.status != 'OK') {
					return;
				}
				setTimeout(function(){
					DOWNLOAD.loadList();
				}, 500)
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
					$card.find('.download-progress, .action-ctrl a[data-event]').hide();
					$card.find('.action-ctrl [data-event=retry]').show();
				}
			}
		});
	},
	retryRequest: function($card, signature){
		$.ajax({
			url: $.url.activity +'request_retry?'+ $.now(),
			type: 'POST',
			data: {
				'signature': signature
			},
			dataType: 'json',
			success: function(json) {
				$card.find('.action-ctrl [data-event]').hide();
				$card.find('.download-progress .determinate').css('width', '0%');
				$card.find('.download-progress, .action-ctrl [data-event=cancel]').show();
				DOWNLOAD.loadList();
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
				$('#remove-modal').closeModal();
			},
			dataType: 'json',
			success: function(json) {
				$('#remove-modal [data-signature]').attr('data-signature', '');
				if (json.status == 'OK') {
					if ( signature == 'all_signature') {
						$('#download-list .item.row').remove();
					} else {
						$('.card[data-signature='+ signature +']').parents('.item.row').remove();
					}
					if ($('#download-list .card[data-signature]').length == 0) {
						$('#download-list').hide().html('');
						$('#no-downloads').show();
					}
				}
			}
		});
	}
};
