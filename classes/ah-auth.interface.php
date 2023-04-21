<?php
namespace axelhahn;

interface ahAuthInterface{
    public function create($sUser, $sPassword);
    public function read();
    public function update($aNewData);
    public function delete();

    public function authenticate($sUser, $sPassword);
    public function list();
    public function set($sUser);
}