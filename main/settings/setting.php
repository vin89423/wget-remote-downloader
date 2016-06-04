<?php

# ============== Framework Variable ================
$SETTING['account'] = array(
	'{username}' => '{password}',
);

$SETTING['file_storage'] = '{wget-download-location}';

$SETTING['user_agent'] = array(
	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246',
	'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
	'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
	'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36',
	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
	'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
	'Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
	'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36',
	'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36',
	'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36',
	'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
	'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0',
	'Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/31.0',
	'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20130401 Firefox/31.0',
	'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
	'Mozilla/6.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:2.0.0.0) Gecko/20061028 Firefox/3.0',
	'Mozilla/5.0 (X11; U; SunOS sun4u; it-IT; ) Gecko/20080000 Firefox/3.0',
	'Mozilla/5.0 (X11; U; Linux x86_64; pl-PL; rv:1.9) Gecko/2008060309 Firefox/3.0',
	'Mozilla/5.0 (X11; U; Linux x86_64; it; rv:1.9) Gecko/2008061017 Firefox/3.0',
	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
	'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
	'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; de-at) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',
	'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',
	'Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16',
	'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
	'Mozilla/5.0 (Windows NT 6.0; rv:2.0) Gecko/20100101 Firefox/4.0 Opera 12.14',
	'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0) Opera 12.14',
);


$SETTING['mime'] = array(
	// Type application
	'application/x-7z-compressed' => '7z',
	'application/msword' => 'docx',
	'application/pdf' => 'pdf',
	'application/vnd.ms-excel' => 'xlsx',
	'application/vnd.ms-powerpoint' => 'pptx',
	'application/x-dvi' => 'dvi',
	'application/x-rar-compressed' => 'rar',
	'application/x-shockwave-flash' => 'swf',
	'application/x-tar' => 'tar',
	'application/xhtml+xml' => 'xhtml',
	'application/zip' => 'zip',
		// Type audio
	'audio/mpeg' => 'mp3',
	'audio/vnd.rn-realaudio' => 'rp',
	'audio/x-wav' => 'wav',
	'audio/x-ms-wma' => 'wma',
		// Type image
	'image/gif' => 'gif',
	'image/vnd.microsoft.icon' => 'ico',
	'image/jpeg' => 'jpeg',
	'image/png' => 'png',
	'image/tiff' => 'tiff',
	'image/webp' => 'webp',
		// Type text
	'text/css' => 'css',
	'text/html' => 'htm',
	'text/javascript' => 'js',
	'text/plain' => 'txt',
	'text/xml' => 'xml',
		// Type video
	'video/flv' => 'flv',
	'video/x-flv' => 'flv',
	'video/quicktime' => 'mov',
	'video/mpeg' => 'mpeg',
	'video/mp4' => 'mp4',
	'video/quicktime' => 'quicktime',
	'video/webm' => 'webm',
	'video/x-ms-wmv' => 'wmv',
	'video/3gpp' => '3gp',
		// Type binary
	'application/octet-stream' => 'file',
		// Type font
	'application/vnd.ms-fontobject' => 'eot',
	'application/x-font-opentype' => 'otf',
	'image/svg+xml' => 'svg',
	'application/x-font-ttf' => 'ttf',
	'application/x-font-woff' => 'woff',
);