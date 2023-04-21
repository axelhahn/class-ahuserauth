<?php

namespace axelhahn;

require_once('ah-auth.class.php');

class ahAuthFile extends ahAuth implements ahAuthInterface
{


    public $isReadonly = false;
    public $isAutodect = false;
    protected $_sDatadir = false;

    public function __construct($sDatadir = false)
    {
        $this->setDatadir($sDatadir ? $sDatadir : dirname(__DIR__) . '/data/auth/userfiles');
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
        // TODO:
        // verify userid

        $sUserfile = $this->_getUserfile($sUser);
        if (file_exists($sUserfile)) {
            return false;
        }
        $aNewUser = $this->_getDefaultUser();
        $aNewUser['userid'] = $sUser;
        $aNewUser['password'] = $this->_generatePasswordHash($sPassword);
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
        unlink($this->_getUserfile());
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
        if (!$sUser) {
            $aTmp = $this->_detectSentBasicAuth();
            if (is_array($aTmp)) {
                list($sUser, $sPassword) = $aTmp;
            }
        }
        $sUserfile = $this->_getUserfile($sUser);
        if (!file_exists($sUserfile)) {
            $this->_lasterror = __METHOD__ . '() - the file [' . $sUserfile . '] does not exist.';
            return false;
        }
        $aUser = include($sUserfile);
        if (!isset($aUser['password'])) {
            $this->_lasterror = __METHOD__ . '() - the field [password] does not exist.';
            return false;
        }
        if ($this->_verifyPasswordHash($sPassword, $aUser['password'])) {
            $this->set($sUser);
            return true;
        }
        $this->_lasterror = __METHOD__ . '() - the given password is wrong.';
        return false;
    }

    /**
     * get a list of all known userids
     * @return array
     */
    public function list()
    {
        $aReturn = [];
        foreach (glob($this->_sDatadir . '/*.php') as $sFile) {
            $aUser = include($sFile);
            $aReturn[] = $aUser['userid'];
        }
        return $aReturn;
    }

    /**
     * set a user as active
     * @param  string  $sUser      userid
     * @return bool
     */
    public function set($sUser)
    {
        $_userfile = $this->_getUserfile($sUser);
        $this->_aUser = false;
        if (file_exists($_userfile)) {
            $this->_aUser = include($_userfile);
            if (isset($this->_aUser['password'])) {
                $this->_aUser['uuid'] = $this->_aUser['userid'] . '__' . __CLASS__;
                $this->_aUser['groups'][] = __CLASS__;

                $this->setSession();
                return true;
            }
        }
        $this->_lasterror = __METHOD__ . '() - the file [' . $_userfile . '] does not exist.';
        return false;
    }

    // ----------------------------------------------------------------------
    // other functions
    // ----------------------------------------------------------------------

    /**
     * helper function:
     * get a filename for a given userid
     * @param  string  $sUserid  userid; if false we take the userid of the current user
     * @return bool
     */
    protected function _getUserfile($sUserid = false)
    {
        $sUsername = $sUserid ? $sUserid : $this->_aUser['userid'];
        return $this->_sDatadir . '/' . $sUsername . '.php';
    }

    /**
     * helper function:
     * save user data of the current user
     * @return bool
     */
    protected function _saveUser()
    {
        $sFile = $this->_getUserfile();
        return file_put_contents($sFile, "<?php \nreturn " . var_export($this->_aUser, 1) . ';');
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
