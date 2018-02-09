<?php

/*
 * Copyright 2017 Vin Wong @ vinexs.com	(MIT License)
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the <organization>.
 * 4. Neither the name of the <organization> nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY <COPYRIGHT HOLDER> ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * Version: 2.2.0
 * Last Update: 2017-05-30
 *
 * Reference:
 *	MVC - http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller
 */

class index
{
    public $manifest = array();
    public $url = array();
    public $inheritance = array();
    public $setting = array();
    public $text = array();
    public $vars = array();

    /**
     * This php framework start from here, analyze url, divide user to activity's class.
     */
    public function initialize()
    {
        $this->manifest = $this->parse_startup();

        ## repackage url to array variable
        $url = str_replace($this->manifest['url_root'], '', $_SERVER['REQUEST_URI']);
        $url_exp = explode('?', preg_replace('~/+~', '/', $url));
        $url = array_shift($url_exp);
        $this->url = explode('/', trim($url, '/'));

        ## determine activity to run
        $this->manifest['url_activity'] = $this->manifest['url_root'].'/';
        if (!in_array(current($this->url), array_keys($this->manifest['activity']))) {
            $activity = $this->manifest['activity_default'];
        } else {
            $activity = array_shift($this->url);
            $this->manifest['url_activity'] .= $activity.'/';
        }
        $this->manifest['activity_current'] = $activity;
        if (!empty($this->manifest['activity'][$activity]['inherit'])) {
            foreach ($this->manifest['activity'][$activity]['inherit'] as $inherit) {
                if (empty($this->manifest['inherit_launch'][$inherit])) {
                    continue;
                }
                $class_path = ROOT_FOLDER.$inherit.'controllers/'.$this->manifest['inherit_launch'][$inherit].'.class.php';
                if (file_exists($class_path)) {
                    include_once $class_path;
                }
            }
            $this->inheritance = array_reverse($this->manifest['activity'][$activity]['inherit']);
        }
        array_unshift($this->inheritance, ROOT_FOLDER.$activity.'/');

        ## determine activity language
        if (!empty($this->manifest['activity'][$activity]['language']) and in_array(current($this->url), $this->manifest['activity'][$activity]['language'])) {
            $this->manifest['lang_current'] = array_shift($this->url);
            $this->manifest['url_activity'] .= $this->manifest['lang_current'].'/';
        } else {
            $this->manifest['lang_current'] = $this->manifest['activity'][$activity]['language_default'];
        }

        ksort($this->manifest);

        $this->setting = $this->load_setting();
        $this->setting['storage'] = empty($this->manifest['activity'][$activity]['storage']) ? null : $this->manifest['activity'][$activity]['storage'];

        if (($launch = $this->load_controller($this->manifest['activity'][$activity]['launch'])) == false) {
            $this->show_error(500);
        }
        if (empty($this->url[0])) {
            return $launch->handler_index();
        }
        $method = 'handler_default';
        if (method_exists($launch, 'handler_'.current($this->url))) {
            $method = 'handler_'.array_shift($this->url);
        }

        return $launch->{$method}($this->url);
    }

    /** Check manifest content and parse it into validated array.
     *  @return array Parsed manifest variables.
     */
    public function parse_startup()
    {
        if (!file_exists(ROOT_FOLDER .'startup.php')) {
            die('Startup: startup.php is missing');
        }
        include_once(ROOT_FOLDER. 'startup.php');
        if (empty($START_UP)) {
            die('Startup: startup.php do not contain var $START_UP');
        }
        if (empty($START_UP['url'])) {
            die('Startup: startup.php do not contain var $START_UP[url]');
        }
        if (empty($START_UP['application']['activity'])) {
            die('Manifest: Application do not contain activity launcher.');
        }
        if (!empty($START_UP['url'])) {
            foreach ($START_UP['url'] as $name => $value) {
                $manifest['url_'.$name] = $value;
            }
        }
        if (!empty($START_UP['session'])) {
            foreach ($START_UP['session'] as $name => $value) {
                $manifest['session_'.$name] = $value;
            }
        }
        $manifest['activity_default'] = current(array_keys($START_UP['application']['activity']));
        if (empty($START_UP['application']['activity'][$manifest['activity_default']])) {
            die('Manifest: Activity '.$manifest['activity_default'].' do not contain any setting.');
        }
        foreach ($START_UP['application']['activity'] as $name => $activity) {
            if (empty($activity['launch'])) {
                die('Manifest: Activity '.$name.' missing launch name.');
            }
            $START_UP['application']['activity'][$name]['language_default'] = current($activity['language']);
            if (empty($activity['languageSource'])) {
                $START_UP['application']['activity'][$name]['languageSource'] = 'ini';
            }
            if (!empty($activity['inherit'])) {
                foreach ($activity['inherit'] as $i => $inherit) {
                    if (empty($inherit[1])) {
                        continue;
                    }
                    $manifest['inherit_launch'][$inherit[0]] = $inherit[1];
                    $START_UP['application']['activity'][$name]['inherit'][$i] = $inherit[0];
                }
            }
        }
        $manifest['activity'] = $START_UP['application']['activity'];
        if (!empty($START_UP['database'])) {
            foreach ($START_UP['database'] as $name => $db) {
                if (!empty($db['host'])) {
                    try {
                        $dsn = 'mysql:host='.$db['host'].';dbname='.$db['db_name'].'';
                        $manifest['database'][$name] = array(
                            'type' => 'mysql',
                            'pdo' => new PDO($dsn, $db['user'], $db['password']),
                        );
                    } catch (Exception $e) {
                    }
                } else {
                    try {
                        $manifest['database'][$name] = array(
                            'type' => 'sqlite',
                            'pdo' => new PDO('sqlite:'.$db['sqlite']),
                        );
                    } catch (Exception $e) {
                    }
                }
            }
        }

        return $manifest;
    }

    /** Load all file in target activity's [settings] folder and store $SETTING variable to class variable for future use.
     * @param bool $current_only Return only current activity settings.
     *
     * @return array Setting variables
     */
    public function load_setting($current_only = true)
    {
        $target_folders = ($current_only) ? array(current($this->inheritance)) : array_reverse($this->inheritance);
        foreach ($target_folders as $folder) {
            $setting_dir = $folder.'settings/';
            if (!is_dir($setting_dir)) {
                continue;
            }
            $list = array_slice(scandir($setting_dir), 2);
            foreach ($list as $file) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                switch ($ext) {
                    case 'php':
                        require $setting_dir.$file;
                        break;
                    case 'ini':
                        $ini_setting = parse_ini_file($setting_dir.$file, true);
                        $SETTING = !isset($SETTING) ? $ini_setting : array_merge($SETTING, $ini_setting);
                        break;
                }
            }
        }

        return isset($SETTING) ? $SETTING : array();
    }

    /** Load controller file and return as object.
     * @param string $controller_name Controller name inside controllers folder with out .class.php extension.
     *
     * @return object|bool Controller object or boolean false.
     */
    public function load_controller($controller_name)
    {
        $prop = strtolower($controller_name).'_ctrl';
        if (property_exists($this, $prop)) {
            return $this->{$prop};
        }

        $class_path = null;
        foreach ($this->inheritance as $folder) {
            if (file_exists($folder.'controllers/'.$controller_name.'.class.php')) {
                $class_path = $folder.'controllers/'.$controller_name.'.class.php';
                break;
            }
        }
        if ($class_path == null) {
            return false;
        }
        include_once $class_path;
        $this->{$prop} = new $controller_name();

        # inherit property to class
        foreach (get_object_vars($this) as $obj => $val) {
            if (isset($this->{$prop}->{$obj}) and is_array($this->{$obj}) and is_array($this->{$prop}->{$obj})) {
                $this->{$prop}->{$obj} = array_merge_recursive($this->{$prop}->{$obj}, $this->{$obj});
            } else {
                $this->{$prop}->{$obj} = $val;
            }
        }

        return $this->{$prop};
    }

    /** Return error message and output to client
     * @param int $error Http error status code
     * @param int $line  Line no to report error.
     */
    public function show_error($error, $line = null)
    {
        switch ($error) {
            case 400:
                header('HTTP/1.0 400 Bad Request');
                die('400 Bad Request.'.($line != null ? ' ('.$line.')' : ''));
            case 403:
                header('HTTP/1.0 403 Forbidden');
                die('You don\'t have permission to access this page '.$_SERVER['REQUEST_URI'].'.'.($line != null ? ' ('.$line.')' : ''));
            case 404:
                header('HTTP/1.0 404 Not Found');
                die($_SERVER['REQUEST_URI'].' not found.'.($line != null ? ' ('.$line.')' : ''));
            case 416:
                header('HTTP/1.0 416 Requested Range not satisfiable');
                die($_SERVER['REQUEST_URI'].' not satisfiable.'.($line != null ? ' ('.$line.')' : ''));
            case 500:
                header('HTTP/1.1 500 Internal Server Error');
                die('500 Internal Server Error.'.($line != null ? ' ('.$line.')' : ''));
            case 503:
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');
                header('Retry-After: 300');
                die('503 Service Temporarily Unavailable.'.($line != null ? ' ('.$line.')' : ''));
            default:
                die('Error: '.$error.($line != null ? ' ('.$line.')' : ''));
        }
    }

    /** Default controller handler. */
    public function handler_index()
    {
        $this->show_error(404);
    }

    /** In case of .htaccess not work. */
    public function handler_assets()
    {
        $this->load_file(ASSETS_FOLDER.$_SERVER['REQUEST_URI']);
    }

    /** Output file in specific location, do not echo any content before load file.
     * @param string $file_path Output file absolute path.
     */
    public function load_file($file_path)
    {
        if (dirname($file_path) == ROOT_FOLDER) {
            $this->show_error(404);
        }
        $block_exts = array('php', 'htaccess');
        $filename = basename($file_path);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (empty($ext) or in_array($ext, $block_exts)) {
            $this->show_error(403);
        }
        if (!file_exists($file_path)) {
            $this->show_error(404);
        }
        ## to support caching
        $file_last_modified_at = filemtime($file_path);

        if (isset($_SERVER['HTTP_CACHE_CONTROL'])) {
            $etag = md5_file($file_path);
            $modified_sence = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : 0;
            $none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : '';
            if ($_SERVER['HTTP_CACHE_CONTROL'] == 'max-age=0' and
                (strtotime($modified_sence) == $file_last_modified_at or
                    trim($none_match) == $etag)) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
            header('Etag: '.$etag);
        }
        $finfo = finfo_open();
        $file_mime = finfo_file($finfo, $file_path, FILEINFO_MIME_TYPE);
        finfo_close($finfo);

        ## to support download continuously
        if (isset($_SERVER['HTTP_RANGE'])) {
            if (!preg_match("/^bytes=(\d+)?-(\d+)?/i", $_SERVER['HTTP_RANGE'], $matches)) {
                $this->show_error(416);
            }
            $filesize = filesize($file_path);
            $offset_start = empty($matches[1]) ? 0 : $matches[1];
            $offset_end = empty($matches[2]) ? $filesize - 1 : $matches[2];
            $length = $offset_end - $offset_start + 1;
            if ($offset_start < 0 or $offset_start >= $filesize or $offset_end < 0 or $offset_end >= $filesize or $length < 1) {
                $this->show_error(416);
            }
            header('HTTP/1.1 206 Partial Content');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s', $file_last_modified_at).' GMT');
            header('Accept-Ranges: bytes');
            header('Content-Range: bytes '.$offset_start.'-'.$offset_end.'/'.$filesize);
            header('Content-Length: '.$length);
            header('Content-Type: '.$file_mime);
            echo file_get_contents($file_path, false, null, $offset_start, $length);
            exit();
        }
        ## output new file with header
        header('Content-Description: File Transfer');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $file_last_modified_at).' GMT');
        header('Content-Disposition: filename='.$filename);
        header('Content-Length: '.filesize($file_path));
        header('Content-Type: '.$file_mime);
        header('Connection: close');
        @ob_end_clean();
        readfile($file_path);
        exit;
    }

    /** For loading file from file storage.
     * @param string $url Handler to response server file, which load by php redirection.
     */
    public function handler_file($url)
    {
        if (empty($this->manifest['activity'][$this->manifest['activity_current']]['storage'])) {
            $this->show_error('System environment not complete', __LINE__);
        }
        $file_path = $this->manifest['activity'][$this->manifest['activity_current']]['storage'].implode('/', $url);
        $this->load_file($file_path);
    }

    /** Load module project
     * @param string $module_path     Module path to be load in /plugins folders.
     * @param string $controller_name Module activity launcher name.
     * @param array  $assign_setting  Pre-assigned setting variable.
     *
     * @return object Plugin launcher object.
     */
    public function load_module($module_path, $controller_name, $assign_setting = array())
    {
        array_unshift($this->inheritance, $module_path);
        $this->setting = array_merge_recursive($this->setting, $assign_setting);
        $this->load_default_vars();
        $launcher = $this->load_controller($controller_name);

        return $launcher;
    }

    /** Add default variable to view files. */
    public function load_default_vars()
    {
        if (!isset($this->vars['URL_ROOT'])) {
            $this->vars['URL_ROOT'] = $this->manifest['url_root'];
            $this->vars['URL_DOMAIN'] = $this->manifest['url_domain'];
            $this->vars['URL_ACTIVITY'] = $this->manifest['url_activity'];
            $this->vars['URL_ASSETS'] = $this->manifest['url_root'].'/assets/';
            $this->vars['LANGUAGE'] = $this->manifest['lang_current'];
        }
    }

    /** Load view file to output.
     * @param string $view View file name inside views directory with out .php extension.
     * @param array  $vars Variable to pass through view.
     *
     * @return bool Load view success or fail.
     */
    public function load_view($view, $vars = array())
    {
        $view_path = null;
        foreach ($this->inheritance as $folder) {
            if (file_exists($folder.'views/'.$view.'.php')) {
                $view_path = $folder.'views/'.$view.'.php';
                break;
            }
        }
        if ($view_path == null) {
            return false;
        }
        $this->load_language();
        $this->load_default_vars();
        if ($vars != null) {
            $this->vars = array_merge($this->vars, $vars);
        }
        extract($this->vars);
        include $view_path;

        return true;
    }

    /** Load language file or database contain as a array variable.
     * @param bool $current_only Is only load current module language.
     *
     * @return bool Language load success or fail.
     */
    public function load_language($current_only = false)
    {
        if ($this->manifest['activity'][$this->manifest['activity_current']]['languageSource'] == 'ini') {
            ## read language variable from ini file
            $target_folders = ($current_only) ? array(current($this->inheritance)) : array_reverse($this->inheritance);
            foreach ($target_folders as $folder) {
                $lang_path = $folder.'languages/'.$this->manifest['lang_current'].'.ini';
                if (file_exists($lang_path)) {
                    $text = parse_ini_file($lang_path);
                    $this->text = array_merge($this->text, $text);
                }
            }

            return true;
        } else {
            $db_name = $this->manifest['activity'][$this->manifest['activity_current']]['languageSource'];
            if (empty($this->manifest['database'][$db_name])) {
                return false;
            }
            ## read language variable from database
            if (($lang_model = $this->load_model('LanguageModel', $db_name)) == false) {
                return false;
            }
            $this->text = $lang_model->load_lang($this->manifest['lang_current']);

            return true;
        }
    }

    /** Load model file and return as object.
     * @param string $model_name Load model name inside models directory with out .class.php extension.
     * @param string $db_name    Which database name the model will connect.
     *
     * @return object|bool Model object or boolean false.
     */
    public function load_model($model_name, $db_name)
    {
        if (empty($this->manifest['database'][$db_name])) {
            die('Cannot find data source from manifest.');

            return false;
        }
        $prop = strtolower($model_name).'_model';
        if (property_exists($this, $prop)) {
            return $this->{$prop};
        }

        $model_path = null;
        foreach ($this->inheritance as $folder) {
            if (file_exists($folder.'models/'.$model_name.'.class.php')) {
                $model_path = $folder.'models/'.$model_name.'.class.php';
                break;
            }
        }
        if ($model_path == null) {
            return false;
        }
        if (!class_exists('BaseModel')) {
            $this->load_plugin('BaseModel');
        }
        include_once $model_path;
        $this->{$prop} = new $model_name();
        $this->{$prop}->setting = array_merge($this->{$prop}->setting, $this->setting);

        switch ($this->manifest['database'][$db_name]['type']) {
            case 'mysql':
                $this->{$prop}->db = $this->manifest['database'][$db_name]['pdo'];
                @$this->{$prop}->db->exec('SET NAMES "utf8"');
                @$this->{$prop}->db->exec('SET CHARACTER SET UTF8');
                @$this->{$prop}->db->exec('SET CHARACTER_SET_RESULTS=UTF8');
                break;
            case 'sqlite':
                $this->{$prop}->db = $this->manifest['database'][$db_name]['pdo'];
                @$this->{$prop}->db->exec('PRAGMA encoding="UTF-8"');
                break;
        }

        return $this->{$prop};
    }

    /**
     * Load plugin class.php file to process.
     * @param string $plugin_name Plugin name in plugin directory without .class.php extension.
     *
     * @return bool load class success or fail.
     */
    public function load_plugin($plugin_name)
    {
        if (file_exists(ROOT_FOLDER.'plugins/'.$plugin_name.'.class.php')) {
            include_once ROOT_FOLDER.'plugins/'.$plugin_name.'.class.php';

            return true;
        }
        foreach ($this->inheritance as $folder) {
            if (file_exists($folder.'controllers/'.$plugin_name.'.class.php')) {
                include_once $folder.'controllers/'.$plugin_name.'.class.php';

                return true;
            }
        }

        return false;
    }

    /** Return localized text from loaded text object.
     * @param string $code Language represent code.
     *
     * @return string with correct language.
     */
    public function lang($code)
    {
        return isset($this->text[$code]) ? $this->text[$code] : $code;
    }

    /** Quick way to redirect visitor to another page.
     * @param string $extra Redirect to more specific path.
     */
    public function redirect($extra = '')
    {
        header('Location: '.(!empty($this->manifest['url_activity']) ? $this->manifest['url_activity'] : '').$extra);
        exit;
    }

    /** Return data as xml content to client
     * @param bool   $status Result in successful status or error status.
     * @param string $data   String or Array data to output.
     */
    public function show_xml($status, $data = '')
    {
        header('Content-Type: application/xml; charset=utf-8');
        $res = array(
            'status' => ($status) ? 'OK' : 'ERROR',
            'data' => $data,
        );
        $xml = new SimpleXMLElement('<index/>');
        array_walk_recursive($res, array($xml, 'addChild'));
        echo $xml->asXML();
        exit;
    }

    /** Return data as json object to client
     * @param $status $status Result in successful status or error status.
     * @param object $data String or Array data to output.
     */
    public function show_json($status, $data = null)
    {
        header('Content-Type: application/json; charset=utf-8');
        $res = array(
            'status' => ($status) ? 'OK' : 'ERROR',
            'data' => $data,
        );
        echo json_encode($res);
        exit;
    }

    /** Un-serialize language object and return suitable language content
     * @param string $json Stringify json string.
     *
     * @return string A single string with correct language.
     */
    public function get_lang_var($json)
    {
        $var = json_decode($json, true);
        if ($var == null) {
            return $json;
        }
        $lang_var = isset($var[$this->manifest['lang_current']]) ? $var[$this->manifest['lang_current']] : '';
        if (!empty($lang_var)) {
            return $lang_var;
        }
        foreach ($var as $key => $val) {
            if (!empty($val)) {
                return (string) $val;
            }
        }

        return '';
    }

    /** Return $_POST variable with pre-set exception return
     * @param string $name    $_POST variable name.
     * @param string $type    Expected data type.
     * @param string $default Default value.
     *
     * @return int|mixed Specify $_POST variable result.
     */
    public function post($name, $type = null, $default = null)
    {
        return $this->request_var($_POST, $name, $type, $default);
    }

    /** Return $_GET variable with pre-set exception return
     * @param string $name    $_GET variable name.
     * @param string $type    Expected data type.
     * @param string $default Default value.
     *
     * @return int|mixed Specify $_GET variable result.
     */
    public function get($name, $type = null, $default = null)
    {
        return $this->request_var($_GET, $name, $type, $default);
    }

    /** Return array content with pre-set exception return
     * @param array  $var     $_GET or $_POST variable
     * @param string $name    Variable name.
     * @param string $type    Expected data type.
     * @param string $default Default value.
     *
     * @return int|mixed Specify variable result.
     */
    public function request_var($var, $name, $type, $default)
    {
        if (!isset($var[$name])) {
            return $default;
        }
        if ($type == null) {
            return $var[$name];
        }
        switch ($type) {
            case 'boolean':
                return ($var[$name] or strtolower($var[$name]) == 'true') ? true : ($default == null ? false : $default);
            case 'date':
                return @date('Y-m-d', strtotime($var[$name]));
            case 'datetime':
                return @date('Y-m-d H:i:s', strtotime($var[$name]));
            case 'email':
                return preg_match('/[a-z0-9-_]+@[a-z0-9_-]+.[a-z]{2,4}/i', $var[$name]) ? $var[$name] : $default;
            case 'float':
                return is_numeric($var[$name]) ? (float) $var[$name] : $default;
            case 'int':
                return is_numeric($var[$name]) ? (int) $var[$name] : $default;
            case 'ip_addr':
                return preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $var[$name]) ? $var[$name] : $default;
            case 'string':
                return $var[$name];
            case 'timestamp':
                return strtotime($var[$name]);
            case 'url':
                return preg_match("/^https?:\/\/[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\//i", $var[$name]) ? $var[$name] : $default;
        }

        return;
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_STRICT);

define('INIT_TIME_START', microtime(true));
define('ROOT_FOLDER', getcwd().'/');
define('ASSETS_FOLDER', getcwd().'/assets/');

$index = new Index();

$index->initialize();
