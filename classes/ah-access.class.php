<?php

namespace axelhahn;

use Exception;

require_once('ah-auth.class.php');
require_once('ah-auth.interface.php');

class ahAccesscontrol
{
    /**
     * userid of authenticated user
     * @var string
     */
    protected $_sUsertype = '';

    protected $_sConfigdir = false;
    protected $_sDatadir = false;
    protected $_aSequence = [];

    /**
     * loaded user class by auth type
     * @var object
     */
    public $auth = false;

    /**
     * loaded user class
     * @var object
     */
    public $user = false;

    /**
     * local groups
     * @var array
     */
    public $groups = false;

    /**
     * local roles of the current user
     * @var array
     */
    public $roles = false;

    /**
     * PDO database resource
     * @var object
     */
    public $db = false;

    // ----------------------------------------------------------------------
    //
    // ----------------------------------------------------------------------


    public function __construct($sType = false)
    {
        $this->_init();
        if ($sType) {
            $this->setAuthType($sType);
        }

        return true;
    }

    /**
     * init access class ... called from constructor
     * @return boolean
     */
    protected function _init()
    {
        $this->_sConfigdir = dirname(__DIR__) . '/config/';
        $this->_sDatadir   = dirname(__DIR__) . '/data/';

        $this->_aSequence  = require($this->_sConfigdir . '/cfg_auth-chain.php');
        $this->_checkSequence();
        return true;
    }

    /**
     * check configuration of the auth chain (cfg_auth-chain.php)
     * - valid references
     * - loop check
     * @return boolean
     */
    protected function _checkSequence($sType = false)
    {
        static $aUsedTypes;
        if (!$sType) {
            $sType = array_key_first($this->_aSequence);
            $aUsedTypes = [];
        }

        // echo __METHOD__ . "($sType)" . PHP_EOL;

        // --- check if a next hop does not exist
        if (!isset($this->_aSequence[$sType])) {
            throw new Exception("CONFIG ERROR: key [$sType] does not exist in sequence.");
        }

        // --- loop detection
        if (isset($aUsedTypes[$sType])) {
            throw new Exception("CONFIG ERROR: loop in sequence detected: key [$sType] was handled already");
        }
        $aSeqItem = $this->_aSequence[$sType];
        $aUsedTypes[$sType] = 1;

        // --- check subkeys of an element ni chain
        foreach (['status', 'ok', 'error', 'na'] as $sKey) {
            if (!isset($aSeqItem[$sKey])) {
                throw new Exception("CONFIG ERROR: sequence[$sType] has no subkey $sKey.");
            }
        }

        if ($aSeqItem['status'] != USAGE_DISABLED) {
            // detect existance of classfile
            if (!$this->setAuthType($sType, true)){
                throw new Exception("CONFIG ERROR: the class for type [$sType] does not exist.");
            }
        }
        $aNextitems = [];
        foreach (['ok', 'error', 'na'] as $sKey) {
            $sNext = $aSeqItem[$sKey];
            if ($sNext[0] != '_') {
                $aNextitems[$sNext] = 1;
            }
        }
        foreach (array_keys($aNextitems) as $sNext) {
            $this->_checkSequence($sNext);
        }
        return true;
    }

    // ----------------------------------------------------------------------
    //
    // ----------------------------------------------------------------------

    /**
     * helper function: dump found authentication
     * @return string
     */
    public function dump()
    {
        $sReturn = '';
        $sReturn .= '>>>>>>>>>> DUMP -- ' . __CLASS__ . ':' . PHP_EOL;
        $sReturn .= '------ auth' . PHP_EOL;
        if (isset($this->auth)) {
            $sReturn .= ''
                . 'authuser: ' . $this->auth->getAuthUser() . PHP_EOL
                . 'type: '     . $this->auth->getAuthType() . PHP_EOL
                . 'groups: '   . print_r($this->auth->getAuthUsergroups(), 1)
                . 'oAccess->auth->read() ' . print_r($this->auth->read(), 1)
                . PHP_EOL;
        }
        $sReturn .= PHP_EOL;
        $sReturn .= '------ user:' . PHP_EOL
            . 'oAccess->userid: ' . $this->getUserid() . PHP_EOL
            . 'oAccess->groups: ' . print_r($this->groups, 1) . PHP_EOL
            . PHP_EOL
            . '------ roles:' . PHP_EOL
            . 'oAccess->roles: ' . htmlentities(print_r($this->roles, 1)) . PHP_EOL
            . PHP_EOL;

        return $sReturn;
    }

    
    // ----------------------------------------------------------------------
    // USER FUNCTIONS :: DETECT AND LOAD
    // ----------------------------------------------------------------------
    /**
     * some magic stuff: automatically detect an authenticated user
     * the auth method must have the flag "isAutodect=true" to verify
     * the return of getUserid()
     * @return bool|string
     */
    public function detectUser()
    {
        $this->user = false;
        $this->groups = [];
        $this->roles = [];
        $sType = array_key_first($this->_aSequence);

        // walker START
        while ($sType[0] != '_') {
            $aSeqItem = $this->_aSequence[$sType];

            $sNext = $aSeqItem['na'];
            if ($aSeqItem['status'] != USAGE_DISABLED) {
                $this->setAuthType($sType);
                $sNext = $aSeqItem['error'];

                // echo "DEBUG: type $sType ".PHP_EOL;
                // if ($this->auth->isAutodect && $this->auth->getUserid())
                if ($this->auth->getUserid())
                 {
                    $sNext = $aSeqItem['ok'];
                    $this->user = $this->auth->getUserid();
                    $this->addGroupsAndRoles();
                }
            }

            $sType = $sNext;
        }

        return $this->user;
    }

    /**
     * set auth type: load a class for this type and put its instance to $this->auth
     * 
     * @param  {string}   $sType        name of the auth type to load
     * @param  {boolean}  $bDetectOnly  check existance of classfile without loading it.
     * @return boolean
     */
    public function setAuthType($sType, $bDetectOnly=false)
    {
        $this->auth = false;
        $this->_sUsertype = $sType;

        if (!isset($this->_aSequence[$sType])) {
            throw new Exception('ERROR: type [' . $sType . '] is not supported in ' . __METHOD__ . '.' . PHP_EOL);
        }
        $sClassfile=__DIR__ . '/ah-auth-' . $sType . '.class.php';
        if ($bDetectOnly){
            return file_exists($sClassfile);
        }
        require_once($sClassfile);
        $sClassname = 'axelhahn\ahAuth' . str_replace('-', '', ucfirst($sType));

        $this->auth = new $sClassname();
        return true;
    }

    // ----------------------------------------------------------------------
    // USER FUNCTIONS :: AFTER LOGIN
    // ----------------------------------------------------------------------

    /**
     * get generated uuid from authenticated user
     * @return string
     */
    public function getUserid()
    {
        // return $this->auth->getUserid();
        return $this->user;
    }
    /**
     * get generated uuid from authenticated user
     * @return string
     */
    public function getUsertype()
    {
        return $this->_sUsertype;
    }


    /**
     * function to call after authentication to set groups and roles
     */
    public function addGroupsAndRoles()
    {
        $this->_addGroups();
        $this->_addRoles();
        return true;
    }

    /**
     * set groups based on groups of a user and cfg_groupmapping
     */
    protected function _addGroups()
    {
        $this->groups = false;

        if (!$this->auth->getAuthUsergroups()) {
            return false;
        }

        $aAppgroups = [];

        $aMappings = include($this->_sDatadir . '/groups/cfg_groupmapping.php');
        $aSections2Scan = [
            '*',
            'type=' . $this->getUsertype()
        ];

        foreach ($aSections2Scan as $sSection) {
            // echo '- '.$sSection.PHP_EOL;
            if (isset($aMappings[$sSection])) {
                foreach ($aMappings[$sSection] as $sAuthGroupname => $sLocalGroup) {
                    // echo '    '.$sLocalGroup.PHP_EOL;
                    if (array_search($sAuthGroupname, $this->auth->getAuthUsergroups()) !== false) {
                        $aAppgroups[] = $sLocalGroup;
                    }
                }
            }
        }
        $this->groups = $aAppgroups;
        return true;
    }

    /**
     * set rols based on cfg_rolesmapping
     * - A non authenticated user gets the roles of __anonymous__
     * - a known user gets the roles of __authenticated__ plus those of its groups (if those exist)
     * @return boolean
     */
    protected function _addRoles()
    {
        $aMappings = include($this->_sDatadir . '/roles/cfg_rolesmapping.php');
        // print_r($aMappings);
        $aRoles = $aMappings['__anonymous__'];
        if ($this->getUserid()) {
            $aRoles = [];
            $aGroups = [];
            $aGroups[] = '__authenticated__';
            $aGroups[] = $this->auth->getUserid();
            foreach ($this->groups as $sGroup) {
                $aGroups[] = '@' . $sGroup;
            }

            foreach ($aGroups as $sGroupname) {
                if (isset($aMappings[$sGroupname])) {
                    foreach ($aMappings[$sGroupname] as $sRole => $bEnable) {
                        if ($bEnable) {
                            $aRoles[$sRole] = $bEnable;
                        } else {
                            if (isset($aRoles[$sRole])) {
                                unset($aRoles[$sRole]);
                            }
                        }
                    }
                }
            }
        }
        $this->roles = array_keys($aRoles);
        return true;
    }

    /**
     * get a flat array with all found roles in cfg_rolesmapping.php
     * @return array
     */
    public function getDefinedRoles()
    {
        $aReturn = [];
        $aMappings = include($this->_sDatadir . '/roles/cfg_rolesmapping.php');
        foreach ($aMappings as $aRoles) {
            foreach (array_keys($aRoles) as $sRolename) {
                $aReturn[$sRolename] = 1;
            }
        }
        return array_keys($aReturn);
    }
    // ----------------------------------------------------------------------
    // USER FUNCTIONS :: LOGOUT
    // ----------------------------------------------------------------------

    /**
     * logout current user
     * @return boolean
     */
    public function logout(){
        if (!$this->user){
            return false; // no user is logged in
        }

        echo "DEBUG: ".__METHOD__.PHP_EOL;
        $this->auth->closeSession();
        $this->auth=false;

        $this->user = false;
        $this->groups = [];
        $this->roles = [];
        return true;
    }

}
