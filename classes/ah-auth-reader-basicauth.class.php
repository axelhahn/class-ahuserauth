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

    public function read()
    {
        return $this->_read();
    }

    // ----------------------------------------------------------------------
    // other functions
    // ----------------------------------------------------------------------

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
        $aNewUser['userid'] = $sFoundUser;
        $aNewUser['uuid'] = $sFoundUser . '__' . __CLASS__;
        $aNewUser['groups'][] = $sKey;

        $this->_aUser = $aNewUser;
        $this->setSession();

        return true;
    }
}
