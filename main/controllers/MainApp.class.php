<?php

/*
 * Copyright 2015 Vin Wong @ vinexs.com
 *
 * All rights reserved.
 */

class MainApp extends Index
{
    public $user = null;

    function __construct()
    {
    }

    // ==============  Custom Handler  ==============

    function handler_index()
    {
		if (!$this->check_login()) {
			return $this->handler_login();
		}
		$vars['logged'] = true;
		$vars['CONTAIN_VIEW'] = 'download_list';
        $this->load_view('frame_layout', $vars);
    }

    function handler_default($url)
    {
        $this->show_error(404, __LINE__);
    }

	function handler_login()
	{
		$vars['logged'] = false;
		$vars['CONTAIN_VIEW'] = 'login';
        $this->load_view('frame_layout', $vars);
	}
	
	function handler_get_list()
	{
		if (!$this->check_login()) {
			$this->show_json(false, 'require_login');
		}
		$storage_location = $this->setting['file_storage'] . $this->user .'/';
		exec('ls '. $storage_location .'*.stat', $state_list);
		if (empty($state_list)) {
			$this->show_json(false, 'list_empty');
		}
		$list = array();
		foreach ($state_list as $state_file) {
			$signature = basename($state_file, '.stat');
			$content = json_decode(file_get_contents($state_file), true);
			if (in_array($content['status'], array('finished', 'error', 'cancel'))) {
				$list[$signature] = $content;
				continue;
			}
			$list[$signature] = $content;
			
			// Parse progress file
			$progress_file = dirname($state_file) .'/'. $signature .'.prog';
			$headline = $tailline = array();			
			exec('head -n 10 '.$progress_file, $headline);
			exec('tail -n 10 '.$progress_file, $tailline);
			$headline = implode("\n", $headline);
			$tailline = implode("\n", $tailline);
			
			if (preg_match('/^--([0-9\-:\s]+)--\s+(http(?:s)?:\/\/[\w\d.\-\/]+)/', $headline, $matches)) {
				$list[$signature]['date'] = $matches[1];
				$list[$signature]['url'] = $matches[2];
				$list[$signature]['http_code'] = 0;
				
				if (preg_match('/HTTP request[\s\w,]+...\s(\d+)/', $headline, $matches)) {
					$list[$signature]['http_code'] = $matches[1];
				}
			}
			switch ($list[$signature]['http_code']) {
				case '200': // OK
					if (preg_match('/Length:\s(\d+)\s\([\d\w\W]+\)\s\[([\w\-\/]+)\]/', $headline, $matches)) {
						$list[$signature]['filesize'] = $matches[1];
						$list[$signature]['filetype'] = $matches[2];
					}
					if (preg_match_all('/(\d+)%[\s]+([\d\.(?:M|K|G)]+)(?:\s|\=)([\d.|h|m|s]+)/',$tailline, $matches)){
						$list[$signature]['precentage'] = end($matches[1]);
						$list[$signature]['speed'] = end($matches[2]);
						$list[$signature]['estimated_time'] = end($matches[2]);
					}
					break;
				case '304': // Not Modified
					break;
				case '302': // Found
					if (preg_match_all('/HTTP request[\s\w,]+...\s(\d+)/', $headline, $matches)) {
						$list[$signature]['http_code'] = end($matches[1]);
						if ($list[$signature]['http_code'] == 200) {
							if (preg_match('/Length:\s([\da-z]+)?(?:\s\([\d\w\W]+\))?\s\[([\w\-\/]+)\]/', $headline, $matches)) {
								$list[$signature]['filesize'] = $matches[1];
								$list[$signature]['filetype'] = $matches[2];
							}
							if (preg_match_all('/(\d+)%[\s]+([\d\.(?:M|K|G)]+)(?:\s|\=)([\d.|h|m|s]+)/',$tailline, $matches)) {
								$list[$signature]['precentage'] = !empty($matches[1][0]) ? end($matches[1]) : 100;
								$list[$signature]['speed'] = end($matches[2]);
								$list[$signature]['estimated_time'] = end($matches[3]);
							}
						} else {
							$list[$signature]['status'] = 'error';
						}
					} else {
						$list[$signature]['status'] = 'error';
					}
					break;
				case '403': // Forbidden
				case '404': // Not Found
				case '500': // Internal Server Error					
				case '502': // Bad Gateway
				case '503': // Service Unavailable
					$list[$signature]['status'] = 'error';
					break;				
				case '0': // Parse progress file error	
					break;
			}
			if (empty($content['filename'])) {
				$list[$signature]['filename'] = $signature;
			}			
			if (isset($list[$signature]['precentage']) and $list[$signature]['precentage'] == 100) {
				$list[$signature]['status'] = 'finished';
			}
			file_put_contents($state_file, json_encode($list[$signature]));
		}
		$this->show_json(true, $list);
	}
	
	function handler_request()
	{
		if (!$this->check_login()) {
			$this->show_json(false, 'require_login');
		}
		
		$url_link = $this->post('url_link', 'url', null);
		$file_name = $this->post('filename', 'string');
		$signature = date('Ymd-His').'-'.rand(1000,9999);
		
		if (empty($url_link)) {
			$this->show_json(false, 'invalid_param');
		}
		if (empty($file_name)) {
			$file_name = basename($url_link);
		}
		$storage_location = $this->setting['file_storage'] . $this->user .'/';
		$download_file = $storage_location . $signature .'.file';
		$progress_file = $storage_location . $signature .'.prog';
		$state_file = $storage_location . $signature .'.stat';
		$user_agent = $this->get_user_agent();
		if (! file_exists($storage_location)) {
			mkdir($storage_location, 0770, true);
		}
		
		file_put_contents($state_file, json_encode(array(
			'status' => 'downloading',
			'filename' => $file_name,
			'url' => $url_link,
		)));
		
		$command = "wget -U \"$user_agent\" --output-document=\"$download_file\" \"$url_link\" > /dev/null 2> \"$progress_file\" &";
		shell_exec ($command);
		$this->show_json(true);
	}
	
	/*
	function handler_request_cancel()
	{
		if (!$this->check_login()) {
			$this->show_json(false, 'require_login');
		}
		$signature = $this->post('signature', 'string');
		if (empty($signature)) {
			$this->show_json(false, 'invalid_param');
		}
		
		$state_file = $this->setting['file_storage'] . $this->user .'/'. $signature .'.stat';
		$content = json_decode(file_get_contents($state_file), true);
		
		if ($content['status'] != 'downloading') {
			$this->show_json(false, 'invalid_action');
		}
		exec('ps aux | grep '. $content['pid'], $output);
		$output = implode("\n", $output);
		if (!preg_match('/(755)(?:[\d\s\W\w]+)wget --tries=/', $output, $matches)) {
			$this->show_json(false, 'invalid_pid');
		}
		exec('kill -15 '. $content['pid']);
		
		$user_dir = dirname($state_file).'/';
		unlink($user_dir . $file_signature .'.file');
		unlink($user_dir . $file_signature .'.prog');
		$content['status'] = 'cancel';		
		file_put_contents($state_file, json_encode($content));
		
		$this->show_json(true);
	}
	*/
	
	function handler_request_retry()
	{
		if (!$this->check_login()) {
			$this->show_json(false, 'require_login');
		}
		$signature = $this->post('signature', 'string');
		if (empty($signature)) {
			$this->show_json(false, 'invalid_param');
		}
		$storage_location = $this->setting['file_storage'] . $this->user .'/';
		$state_file = $storage_location . $signature .'.stat';
		$download_file = $storage_location . $signature .'.file';
		$progress_file = $storage_location . $signature .'.prog';
		
		$content = json_decode(file_get_contents($state_file), true);		
		if (empty($content['url'])) {
			$this->show_json(false, 'cannot_retry');
		}
		
		file_put_contents($state_file, json_encode(array(
			'status' => 'downloading',
			'filename' => $file_name,
			'url' => $url_link,
		)));
		
		$command = "wget -U \"$user_agent\" --output-document=\"$download_file\" \"$url_link\" > /dev/null 2> \"$progress_file\" &";
		shell_exec ($command);
		$this->show_json(true);
	}
	
	function handler_get_file()
	{
		if (!$this->check_login()) {
			$this->show_error(403);
		}
		$signature = $this->post('signature', 'string');
		if (empty($signature)) {
			$this->show_error(404);
		}
		
		$state_file = $this->setting['file_storage'] . $this->user .'/'. $signature .'.stat';
		$download_file = $this->setting['file_storage'] . $this->user .'/'. $signature .'.file';
		$content = json_decode(file_get_contents($state_file), true);
		
		if ($content['status'] != 'finished' or !file_exists($download_file)) {
			$this->show_error(404);
		}
		if (($ext = pathinfo($content['filename'], PATHINFO_EXTENSION)) == "") {
			$content['filename'] = $content['filename'] .'.'. $this->get_expect_extension($content['filetype']);
		}
        header('Content-Description: File Transfer');
		header('Content-disposition: attachment; filename="'. $content['filename'] .'"');
        header('Content-Type: '. $content['filetype']);
        header('Connection: close');
		readfile($download_file);
	}
	
	function handler_request_remove()
	{
		if (!$this->check_login()) {
			$this->show_json(false, 'require_login');
		}
		$signature = $this->post('signature', 'string');
		if (empty($signature)) {
			$this->show_json(false, 'invalid_param');
		}
		$storage_location = $this->setting['file_storage'] . $this->user .'/';
		exec('ls '. $storage_location .'*.stat', $state_list);
		if (empty($state_list)) {
			$this->show_json(false, 'list_empty');
		}
		$list = array();
		foreach ($state_list as $state_file) {			
			$file_signature = basename($state_file, '.stat');
			if ($file_signature == $signature or $signature == 'all_signature') {
				$user_dir = dirname($state_file).'/';
				unlink($user_dir . $file_signature .'.file');
				unlink($user_dir . $file_signature .'.prog');
				unlink($user_dir . $file_signature .'.stat');
			}
		}
		$this->show_json(true);
	}
	
	function get_expect_extension($mime)
	{
		if (!empty($this->setting['mime'][$mime])) {
			return $this->setting['mime'][$mime];
		}
		return 'file';
	}
	
	function get_user_agent()
	{
		$index = rand(0, count($this->setting['user_agent']) - 1);
		return $this->setting['user_agent'][$index];
	}
	
    // Add handler here ...

    /** Allow developer to custom error response. */
    function show_error($error, $line = null)
    {
        parent::show_error($error, $line);
    }

    /** For manage account session, such as create user, change password, login and logout. */
    function handler_session($url)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST' or !isset($url[0])) {
            return $this->show_error(403);
        }
        $session = $this->load_controller('Session');
        return $session->{'handler_process_' . $url[0]}($url);
    }

    /** For spider to read robots.txt. */
    function handler_robots_txt()
    {
        return $this->load_file(ASSETS_FOLDER . 'robots.txt');
    }

    //  ==============  Handle Error  ==============

    /** For browser to read favicon.ico unless layout do not contain one. */
    function handler_favicon_ico()
    {
        return $this->load_file(ASSETS_FOLDER . 'favicon.ico');
    }

    //  ==============  Session & Permission  ==============

    /** Check visitor is logged in or not. */
    function check_login()
    {
        if ($this->user != null) {
            return true;
        }
        if (!isset($_COOKIE[$this->manifest['session_token']])) {
            return false;
        }
        $session = $this->load_controller('Session');
        $this->user = $session->recover_session_by_token($_COOKIE[$this->manifest['session_token']]);
        if ($this->user == false) {
            $session->remove_session_recover_cookie();
            return false;
        }
        return true;
    }

    //  ==============  Layout variable  ==============

    /** Add activity base variable to view. */
    function load_default_vars()
    {
        parent::load_default_vars();
        $this->vars['URL_REPOS'] = '//www.vinexs.com/repos/';
        $this->vars['URL_RSC'] = $this->vars['URL_ASSETS'] . $this->manifest['activity_current'] . '/';
    }

}
