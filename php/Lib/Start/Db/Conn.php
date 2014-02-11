<?php

namespace Start\Db;

use o;
use PDO;

/* CONN
 * TODO: In construction!
 * Do you have any idea? 
 * prbr@ymail.com
 */

class Conn {

    private $conn = null;
    private $type = null;
    private $host = null;
    private $database = null;
    private $user = null;
    private $password = null;
    private $charset = null;
    private $sql = '';
    //node for Result class
    private $result = null;

    function __construct($alias = null){
        $dt = o::db();
        $alias = (isset($dt[$alias])) ? $alias : $dt['default'];        
        $this->type = $alias;
        foreach($dt[$alias] as $k=> $v){
            $this->$k = $v;
        }
    }

    //Database conector
    function db(){
        if($this->conn == null){
            switch($this->type){
                case 'sqlite': $dsn = 'sqlite:'.$this->database;
                    break;
                case 'mysql' : $dsn = 'mysql:dbname='.$this->database.';host='.$this->host.';charset=UTF8';
                    break;
            }
            $this->conn = new PDO($dsn,$this->user,$this->password);
        }
        if(!is_object($this->conn))
                trigger_error('I can not connect to the database',E_USER_ERROR);
        return $this->conn;
    }

    //Running simple SQL QUERY
    function xquery($sql = '',$parms = array()){
        if($sql == '') return false;
        $this->sql = $sql;
        $all = $this->db()->query($sql);
        return $all->fetchAll();
    }

    function query($sql,$parms = array()){
        $this->sql = $sql;
        $sth = $this->db()->prepare($sql);
        $sth->execute($parms);

        return $this->result = $sth->fetchAll(PDO::FETCH_CLASS,"Start\Db\Result");
    }

    /**
     * CLEAR results data!
     */
    function clear(){
        $this->sql = '';
        $this->result = null;
    }

    /** GET
     * Get column named in all Result class
     * @param type $parm Name of the requested column
     * @return boolean|array Array of contents or false
     */
    function get($parm){
        if(is_array($this->result)){
            foreach($this->result as $v){
                $o[] = $v->get($parm);
            }
            return $o;
        }
        return false;
    }

    /**
     * GetLine 
     * @param type $num Line number or null for all lines
     * @return boolean|mixed Return false or array of objects Result or object Result.
     */
    function getLine($num = 'all'){
        if(is_array($this->result)){
            if($num === 'all') return $this->result;
            if(isset($this->result[$num])) return $this->result[$num];
        }
        return false;
    }

    //TODO
    function update(){
        
    }

    //TODO
    function insert(){
        
    }

    //TODO
    function delete(){
        
    }

    //Creating DataBaseResult
    function createDb(){

        $pdo = $this->db();

        //Criando o banco de dados
        $pdo->exec('CREATE DATABASE IF NOT EXISTS start 
					DEFAULT CHARACTER SET utf8 
					COLLATE utf8_general_ci');

        $pdo->exec('CREATE TABLE IF NOT EXISTS users (
					  ID int(10) unsigned NOT NULL COMMENT \'indice\',
					  ACCESS int(10) unsigned NOT NULL COMMENT \'Número de acessos\',
					  D1 int(10) unsigned NOT NULL,
					  D2 int(10) unsigned NOT NULL,
					  D3 int(10) unsigned NOT NULL,
					  D4 int(10) unsigned NOT NULL,
					  D5 int(10) unsigned NOT NULL,
					  D6 int(10) unsigned NOT NULL,
					  PRIMARY KEY (ID)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'tabela de exemplo\'');
    }

}
