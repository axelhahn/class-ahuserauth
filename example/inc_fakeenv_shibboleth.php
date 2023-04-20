<?php
// ----------------------------------------------------------------------
// 
// FAKE ENVIRONMENT for non shibboleth protected environments
// 
// ----------------------------------------------------------------------
    
$_SERVER['mail']='max.mustermann@example.com';

$_SERVER['affiliation']='member;staff';
$_SERVER['givenName']='Max (Devuser)';
$_SERVER['homeOrganization']='example.com';
$_SERVER['homeOrganizationType']='university';
$_SERVER['surname']='Mustermann';
$_SERVER['uniqueID']='hier-ist-eine-unique-id@example.com';
$_SERVER['isMemberOf']='com:example:department';
