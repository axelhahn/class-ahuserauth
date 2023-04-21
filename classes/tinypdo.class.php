<?php

/**
 * TINYPDO
 *
 * @author Axel
 */
class tinypdo
{

    /**
     * object of pdo database instance
     * @var object
     */
    private $_db;

    /**
     * optional: single tablename for an item on a single table
     * @var object
     */
    private $_table;

    /**
     * flag: show mysql errors and debug information?
     * @var boolean
     */
    protected $_bShowErrors = false;

    /**
     * last error
     * @var string
     */
    protected $_sLastError = false;

    // ----------------------------------------------------------------------
    // CONSTRUCTOR
    // ----------------------------------------------------------------------

    /**
     * constructor - sets internal environment variables and checks existence 
     * of the database
     * @param  string $sProject  project ID
     * @return boolean
     */
    public function __construct($aDb = false)
    {
        if (is_array($aDb)) {
            $this->connect($aDb);
        }
        return true;
    }
    // ----------------------------------------------------------------------
    // INIT
    // ----------------------------------------------------------------------

    /**
     * create a database connection
     * see https://www.php.net/manual/de/pdo.construct.php
     * 
     * sqlite:[filename]
     * mysql:dbname=testdb;host=127.0.0.1
     * mysql:dbname=testdb;host=127.0.0.1;port=3333
     * mysql:dbname=testdb;unix_socket=/path/to/socket
     * 
     * uri:file:///usr/local/dbconnect
     * 
     * @param  array  $aDb  database connection
     *                      to create DSN;
     *                       - dsn            {string}  DSN string to connect to
     *                         or one/ some of these:
     *                          - type        {string}
     *                          - file        {string}
     *                          - dbname      {string}
     *                          - host        {string}
     *                          - port        {integer}
     *                          - unix_socket {string}
     *                       optional:
     *                       - user           {string}
     *                       - password       {string}
     *                       - options        {array}
     */
    public function connect($aDb)
    {
        $sDsn = isset($aDb['dsn']) ? $aDb['dsn'] : false;
        $sDsnParams = '';
        foreach (['host', 'port', 'unix_socket', 'charset'] as $dsnparam) {
            $sDsnParams .= (isset($aDb[$dsnparam])  ? ';' . $dsnparam . '=' . $aDb[$dsnparam] : '');
        }
        if (!$sDsn) {
            $sDsn = ''
                . (isset($aDb['type'])   ? $aDb['type']               : 'NOTYPE') . ':'
                . (isset($aDb['file'])   ? 'file://' . $aDb['file']   : '')
                . (isset($aDb['dbname']) ? 'dbname=' . $aDb['dbname'] : '')
                . $sDsnParams;
        }

        $sUsername = (isset($aDb['user'])     ? $aDb['user']       : null);
        $sPassword = (isset($aDb['password']) ? $aDb['password']   : null);
        $aOptions = (isset($aDb['options'])   ? $aDb['options']    : []);

        try {
            $this->_db = new PDO($sDsn, $sUsername, $sPassword, $aOptions);
            if (isset($aDb['table'])) {
                $this->setTable($aDb['table']);
            }
        } catch (PDOException $e) {
            $this->_sLastError = $e->getMessage();
            if ($this->_bShowErrors) {
                $this->_wd('PDO ERROR: ' . $e->getMessage());
            }
            return false;
        }
    }
    // ----------------------------------------------------------------------
    // PRIVATE FUNCTIONS
    // ----------------------------------------------------------------------

    /**
     * write debug output if enabled by flag
     * @param  string  $s  string to show
     */
    protected function _wd($s)
    {
        if ($this->_bShowErrors) {
            echo 'DEBUG: ' . __CLASS__ . ' - ' . $s . PHP_EOL;
        }
        return true;
    }
    /**
     * execute a sql statement
     * @param  string  $sSql   sql statement
     * @param  array   $aData  array with data items; if present prepare statement will be executed 
     * @return database object
     */
    public function makeQuery($sSql, $aData = [])
    {
        // $this->_log(__FUNCTION__."($sSql)");
        // echo "<pre>$sSql</pre>"; echo count($aData) ? '<pre>replacement data for prepared statement: ' . print_r($aData, 1) . '</pre>' : '';

        try {
            if (is_array($aData) && count($aData)) {
                $result = $this->_db->prepare($sSql);
                $result->execute($aData);
            } else {
                $result = $this->_db->query($sSql);
            }
        } catch (PDOException $e) {
            $this->_sLastError = $e->getMessage();
            if ($this->_bShowErrors) {
                $this->_wd('PDO ERROR: ' . $e->getMessage());
            }
            return false;
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get last PDO error
     * @return string
     */
    public function getError()
    {
        return $this->_sLastError;
    }
    // ----------------------------------------------------------------------
    // SETTER
    // ----------------------------------------------------------------------

    /**
     * enable/ disable debug; database error is visible on enabled debug only
     * @param  string|bool  $bNewValue  new debug mode; empty|false = off; any value=true
     * @return boolean
     */
    public function setDebug($bNewValue)
    {
        return $this->_bShowErrors = !!$bNewValue;
    }

    /**
     * enable/ disable debug; database error is visible on enabled debug only
     * @param  string|bool  $bNewValue  new debug mode; empty|false = off; any value=true
     * @return boolean
     */
    public function setTable($sTable)
    {
        return $this->_table = $sTable;
    }

    // ----------------------------------------------------------------------
    // ANNOUNCEMENT ITEMS - CRUD ACTIONS
    // ----------------------------------------------------------------------

    /**
     * create a new entry for a announcement
     * @param  array  $aItem  new announcement data
     * @return array
     */
    public function create($aItem)
    {
        $aItem['time'] = str_replace('T', ' ', $aItem['time']) . ':00';
        $aItem['timeupdate'] = date("Y-m-d H:i:s");
        $sSql = 'INSERT INTO `' . $this->_table . '` (`' . implode('`, `', array_keys($aItem)) . '`) VALUES (:' . implode(', :', array_keys($aItem)) . ');';
        return $this->makeQuery($sSql, $aItem);
    }

    /**
     * create a new entry for a announcement
     * @param  array  $aItem  new announcement data
     * @return array
     */
    public function read($iId)
    {
        $sSql = 'SELECT * from `' . $this->_table . '` WHERE `id`=' . (int)$iId;
        $aData = $this->makeQuery($sSql);
        return isset($aData[0]) ? $aData[0] : false;
    }

    /**
     * update entry; the field "id" is required to identify a single row in the table
     * @param  array  $aItem  data with fields to modify
     * @return array
     */
    public function update($aItem)
    {
        $aItem['timeupdate'] = date("Y-m-d H:i:s");
        $sSql = '';
        foreach (array_keys($aItem) as $sCol) {
            $sSql .= ($sSql ? ', ' : '') . "`$sCol` = :$sCol";
        }
        $sSql = 'UPDATE `' . $this->_table . '` ' . 'SET ' . $sSql . ' WHERE `id` = :id';
        return $this->makeQuery($sSql, $aItem);
    }

    /**
     * delete entry by a given id
     * @param  integer  $iId   id of the entry to delete
     * @return array
     */
    public function delete($iId)
    {
        $sSql = 'DELETE from `' . $this->_table . '` WHERE `id`=' . (int)$iId;
        return $this->makeQuery($sSql);
    }
}
