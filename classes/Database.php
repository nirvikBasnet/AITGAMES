<?php
namespace aitSydney;

class Database{
    protected $connection;
    public function __construct(){
        $this -> connection = mysqli_connect('localhost','root','','aitgames');
    }
}
?>

