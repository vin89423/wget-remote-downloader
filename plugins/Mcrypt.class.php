<?php

/*
 *  Copyright (c) 2011-2012 Infotoo International Limited
 */

class Mcrypt
{
    const AES = 0;
    const ECB = 1;

    protected $iv = '';
    protected $key = '';
    protected $mode = 0;

    function __construct($key, $iv = null)
    {
        $this->key = $key;
        if ($iv == null) {
            $this->mode = Mcrypt::ECB;
        } else {
            $this->iv = $iv;
            $this->mode = Mcrypt::AES;
        }
    }

    function encrypt($input)
    {
        if (strlen($input) == 0) {
            return null;
        }
        switch ($this->mode) {
            case Mcrypt::AES:
                $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
                mcrypt_generic_init($td, $this->key, $this->iv);
                $encrypted = mcrypt_generic($td, $input);
                mcrypt_generic_deinit($td);
                mcrypt_module_close($td);
                return bin2hex($encrypted);
                break;
            case Mcrypt::ECB:
                $block = mcrypt_get_block_size('des', 'ecb');
                $pad = $block - (strlen($input) % $block);
                $input .= str_repeat(chr($pad), $pad);
                return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $input, MCRYPT_MODE_ECB));
                break;
            default:
                return null;
        }
    }

    function decrypt($code)
    {
        if (strlen($code) == 0) {
            return null;
        }
        $code = $this->hex2bin($code);
        switch ($this->mode) {
            case Mcrypt::AES:
                $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
                mcrypt_generic_init($td, $this->key, $this->iv);
                $decrypted = mdecrypt_generic($td, $code);
                mcrypt_generic_deinit($td);
                mcrypt_module_close($td);
                return utf8_encode(trim($decrypted));
                break;
            case Mcrypt::ECB:
                $text = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $code, MCRYPT_MODE_ECB));
                return substr($text, 0, -ord($text[strlen($text) - 1]));
                break;
            default:
                return null;
        }
    }

    private function hex2bin($hexdata)
    {
        $bindata = '';
        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }

}
