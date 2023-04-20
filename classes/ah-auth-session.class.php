<?php

namespace axelhahn;

require_once('ah-auth.class.php');

class ahAuthSession extends ahAuth implements ahAuthInterface
{


    public $isReadonly = false;
    public $isAutodect = true;

    public function __construct()
    {
        session_name(__FILE__);
        return true;
    }

    // ----------------------------------------------------------------------
    // IMPLEMENTATION
    // ----------------------------------------------------------------------

    /**
     * create a new user
     * @param  string  $sUser      userid
     * @param  string  $sPassword  password
     * @return bool
     */
    public function create($sUser, $sPassword)
    {
        $aNewUser = $this->_aDefaultuser;
        $aNewUser['userid'] = $sUser;
        $aNewUser['file'] = __FILE__;
        $aNewUser['class'] = __CLASS__;
        $aNewUser['password'] = false;
        // $aNewUser['created']=date('U');
        $this->_aUser = $aNewUser;
        return $this->_saveUser();
    }

    /**
     * get an array of known data of the current authenticated user
     * @return array
     */
    public function read()
    {
        return $this->_read();
    }

    /**
     * update user data
     * @param  array   $aData  array of new data; known keys are:
     *                         password - set a new password (it will be hashed) 
     *                         groups   - update groups value
     * @return bool
     */
    public function update($aData)
    {
        // $aKeys=array_keys($this->_aDefaultuser);
        $aUser = $this->_aUser;

        // userid is not allowed to overwrite
        if (isset($aData['userid']) && $aUser['userid'] != $aData['userid']) {
            $this->_lasterror = __METHOD__ . '() - value in field userid is not the current user.';
            return false;
        }
        // unset($aUser['userid']);

        foreach ($aData as $sKey => $value) {
            if (!isset($aUser[$sKey])) {
                // wrong key was given
                $this->_lasterror = __METHOD__ . '() - the key [' . $sKey . '] is invalid.';
                return false;
            }
            if ($sKey == 'password') {
                $aUser[$sKey] = $this->_generatePasswordHash($value);
            } else {
                $aUser[$sKey] = $value;
            }
        }
        $this->_aUser = $aUser;
        return $this->_saveUser();
    }

    /**
     * delete the current user
     * @return bool
     */
    public function delete()
    {
        session_destroy();
        $this->_aUser = false;
        return true;
    }

    // ----------------------------------------------------------------------

    /**
     * authenticate a user with username and password
     * @param  string  $sUser      userid
     * @param  string  $sPassword  password
     * @return bool
     */
    public function authenticate($sUser = false, $sPassword = false)
    {
        return false;
    }

    /**
     * get a list of all known userids
     * @return array
     */
    public function list()
    {
        return false;
    }

    // ----------------------------------------------------------------------
    // other functions
    // ----------------------------------------------------------------------

    /**
     * helper function:
     * save user data of the current user
     * @return bool
     */
    protected function _saveUser()
    {
        session_start();
        $_SESSION['AUTH_USER']=$this->_aUser;
        session_write_close();
        return true;
    }

    /**
     * set a directory of the user data files
     * @param  string  $sDatadir  path of the data directory with all user files
     * @return bool
     */
    public function setDatadir($sDatadir)
    {
        if (is_dir($sDatadir)) {
            $this->_sDatadir = $sDatadir;
            return true;
        }
        $this->_lasterror = __METHOD__ . '() - the directory [' . $sDatadir . '] does not exist.';
        return false;
    }
}
