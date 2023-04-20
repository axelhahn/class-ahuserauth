<?php

namespace axelhahn;

require_once('ah-auth.interface.php');

class ahAuth
{

    /**
     * flag: is readonly user auth
     * @var bool
     */
    public $isReadonly = false;
    /**
     * flag: user auth automatically detects current user from $SERVER scope
     * @var bool
     */
    public $isAutodect = false;
    /**
     * array of data of an authenticated user
     * @var array
     */
    protected $_aUser = false;

    /**
     * array of emtpy default user keys
     * @var array
     */
    protected $_aDefaultuser = [
        'userid' => '',
        'uuid' => '',
        'password' => '',
        'groups' => [],
        'class' => '',
        'file' => '',
    ];

    /**
     * last error
     * @var string
     */
    protected $_lasterror = '';

    protected $_iPasswordAlgorithm = PASSWORD_DEFAULT;

    // ----------------------------------------------------------------------
    //
    // ----------------------------------------------------------------------


    public function __construct()
    {
        return true;
    }

    // ----------------------------------------------------------------------

    /**
     * get last error
     * @return string
     */
    public function error()
    {
        return $this->_lasterror;
    }

    // ----------------------------------------------------------------------
    // USER FUNCTIONS
    // ----------------------------------------------------------------------

    /**
     * detect a sent basic authentication in request header and get an array 
     * with user and password or false
     * @return bool|array
     */
    protected function _detectSentBasicAuth()
    {
        $aHeaders = apache_request_headers();
        if (is_array($aHeaders) && isset($aHeaders['Authorization'])) {
            $sAuthline = preg_replace('/^Basic /', '', $aHeaders['Authorization']);

            $aAuth = explode(':', base64_decode($sAuthline));
            if (is_array($aAuth) && count($aAuth) == 2) {
                return $aAuth;
            }
        }
        return false;
    }

    /**
     * shared function: generate a password hash of a given password
     * @param  string  $sPassword  password to hash
     * @return string
     */
    protected function _generatePasswordHash($sPassword)
    {
        return password_hash($sPassword, $this->_iPasswordAlgorithm);
    }

    /**
     * shared function: compare a given password with a known hash
     * @param  string  $sPassword      password to verify
     * @param  string  $sPasswordHash  known stored hash of valid password
     * @return bool
     */
    protected function _verifyPasswordHash($sPassword, $sPasswordHash)
    {
        return password_verify($sPassword, $sPasswordHash);
    }

    // ----------------------------------------------------------------------
    /**
     * abstracted read implementation for all auth types
     * @return bool|array
     */
    protected function _read()
    {
        if (!is_array($this->_aUser)) {
            return false;
        }
        $aReturn = $this->_aUser;
        if (isset($aReturn['password'])) {
            unset($aReturn['password']);
        }
        return $aReturn;
    }

    /**
     * helper: get a single value from user data hash
     * @param  string  name of the subkey
     * @return string|array
     */
    protected function _getAuthValue($sKey)
    {
        return isset($this->_aUser[$sKey]) ? $this->_aUser[$sKey] : false;
    }

    /**
     * get generated uuid from authenticated user
     * @return string
     */
    public function getUserid()
    {
        return $this->_getAuthValue('uuid');
    }

    /**
     * get user id from authenticated user
     * @return string
     */
    public function getAuthUser()
    {
        return $this->_getAuthValue('userid');
    }

    /**
     * get array of groups from authenticated user
     * @return array
     */
    public function getAuthUsergroups()
    {
        return $this->_getAuthValue('groups');
    }

    /**
     * get user id from authenticated user
     * @return string
     */
    public function getAuthType()
    {
        $sFile = $this->_getAuthValue('file');
        if (!$sFile) {
            return false;
        }
        return str_replace(
            ['ah-auth-', '.class.php'],
            ['', ''],
            basename($sFile)
        );
    }
    /**
     * get array of all user data of authenticated user
     * @return array
     */
    public function getUserdata()
    {
        return $this->_aUser;
    }
    // ----------------------------------------------------------------------
    // USER FUNCTIONS
    // ----------------------------------------------------------------------
}
