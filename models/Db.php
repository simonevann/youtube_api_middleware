<?php
/**
 * Classe base per la connessione al database
 */
class Db extends PDO {
    private $uname;
    private $passwd;
    private $hostname;
    private $dbname;
    
    public function __construct()
    {
        $ini = parse_ini_file('./config.ini');
        $this->uname = $ini['db_user'];
        $this->passwd = $ini['db_password'];
        $this->hostname = $ini['db_host'];
        $this->dbname = $ini['db_name'];
        try {
            parent::__construct('mysql:host=' . $this->hostname . ';dbname=' . $this->dbname, $this->uname, $this->passwd);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
   }
