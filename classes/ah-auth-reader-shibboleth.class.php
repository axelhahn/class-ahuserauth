<?php

namespace axelhahn;

require_once('ah-auth-reader.interface.php');

class ahAuthReadershibboleth extends ahAuth implements ahAuthReaderInterface
{

    public $isReadonly = true;
    public $isAutodect = true;

    protected $_aShibData = [];

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
        // echo '<pre>'.print_r($_SERVER, 1).'</pre>';
        foreach ($_SERVER as $sKey => $value) {
            if (
                ( preg_match('/^[a-z]/', $sKey) && !preg_match('/^(argv|argc)$/', $sKey))
                || preg_match('/^Shib\-(Identity-Provider|Session-ID|Handler)/', $sKey)
                || preg_match('/^Meta\-(largeLogo|displayName)/', $sKey)
                || preg_match('/^(AUTH_TYPE|REMOTE_USER)$/', $sKey)
            ) {
                $this->_aShibData[$sKey] = $_SERVER[$sKey];
            }
        }
        if (!isset($this->_aShibData['uniqueID'])) {
            return false;
        }
        $aNewUser = $this->_getDefaultUser();
        $aNewUser['class'] = __CLASS__;
        $aNewUser['classfile'] = __FILE__;
        $aNewUser['groups'][] = __CLASS__;

        $aNewUser['userid'] = $this->_aShibData['uniqueID'];
        $aNewUser['uuid'] = $this->_aShibData['uniqueID']; // we trust shibboleth that its uniqueId *IS* uniq

        if (isset($this->_aShibData['homeOrganizationType'])) {
            $aNewUser['groups'][] = $this->_aShibData['homeOrganizationType'];
            if (isset($this->_aShibData['homeOrganization'])) {
                $sGroupbase = $this->_aShibData['homeOrganizationType'] . '/' . $this->_aShibData['homeOrganization'];
                $aNewUser['groups'][] = $sGroupbase;
                if (isset($this->_aShibData['affiliation'])) {
                    $aNewUser['groups'][] = $sGroupbase . '/' . $this->_aShibData['affiliation'];
                }
                if (isset($this->_aShibData['isMemberOf'])) {
                    $aNewUser['groups'][] = $sGroupbase . '/' . $this->_aShibData['isMemberOf'];
                }
            }
        }


        $aNewUser['_shibboleth'] = $this->_aShibData;
        $this->_aUser = $aNewUser;
        $this->setSession();
        return true;
    }
}
