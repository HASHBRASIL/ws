<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 29/11/15
 * Time: 16:52
 */
class Base
{
    protected $dbh;

    function __construct()
    {
        $dbh = new PDO( 'pgsql:host=hash.cs72ftezvham.sa-east-1.rds.amazonaws.com;port=5432;dbname=hash;user=titaniumtech;password=TitaniuM2016#$%' );
        $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $this->dbh = $dbh;
    }

//    function formatArrayToString(array $arrayData)
//    {
//        $resultData = "'" . implode("', '", $arrayData) . "''";
//
//        return $resultData;
//    }
//
//    function formatStringToString(array $stringData)
//    {
//        $resultData = explode(',', $stringData);
//    }


}