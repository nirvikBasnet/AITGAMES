<?php
namespace aitsydney;
class Database{
    private $user;
    private $password;
    private $host;
    private $db;
    protected $connection;
    protected function __construct(){
        $this -> getConfig();
        $this -> connection = mysqli_connect(
            $this -> host,
            $this -> user,
            $this -> password,
            $this -> db
        );
    }
    private function getConfig(){
        $this -> user = getenv('dbuser');
        $this -> password = getenv('dbpass');
        $this -> host = getenv('dbhost');
        $this -> db = getenv('dbname');
    }
}
?>