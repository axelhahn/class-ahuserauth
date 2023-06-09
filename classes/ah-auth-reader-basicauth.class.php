<?php

namespace axelhahn;

require_once('ah-auth-reader.interface.php');

class ahAuthReaderbasicauth extends ahAuth implements ahAuthReaderInterface
{

    public $isReadonly = true;
    public $isAutodect = true;

    /**
     * list of array keys with usernames
     * @var array
     */
    protected $_aSrvVars = [
        "AUTH_USER",
        "PHP_AUTH_USER",
        "REMOTE_USER",
    ];

    // ----------------------------------------------------------------------
    // IMPLEMENTATION
    // ----------------------------------------------------------------------

    /**
     * read current user
     * implementation of interface function
     * 
     * @return array
     */
    public function read()
    {
        return $this->_read();
    }

    // ----------------------------------------------------------------------
    // other functions
    // ----------------------------------------------------------------------

    /**
     * detect basic authentication from $_SERVER scope
     * implementation of interface function
     * 
     * remark: start basic auth detection after other logins that write an auth
     * user to REMOTE_UESER
     * 
     * @return array
     */
    public function detectAuth()
    {
        $sFoundUser = false;
        foreach ($this->_aSrvVars as $sKey) {
            if (isset($_SERVER[$sKey])) {
                $sFoundUser = $_SERVER[$sKey];
                break;
            }
        }
        if (!$sFoundUser) {
            $this->_lasterror = __METHOD__ . '() - no basic auth user was found.';
            return false;
        }
        $aNewUser = $this->_getDefaultUser();
        $aNewUser['class'] = __CLASS__;
        $aNewUser['classfile'] = __FILE__;
        $aNewUser['groups'][] = __CLASS__;

        $aNewUser['userid'] = $sFoundUser;
        $aNewUser['uuid'] = $sFoundUser . '__' . __CLASS__;
        $aNewUser['groups'][] = $sKey;

        $this->_aUser = $aNewUser;
        $this->setSession();

        return true;
    }
}
