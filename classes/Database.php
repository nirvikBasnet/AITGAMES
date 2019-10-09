<?php

namespace aitSydney;



class Database{

    protected $connection;

    public function __construct(){

        $this -> connection = mysqli_connect(getenv('dbhost'),getenv('dbuser'),'123456','aitgames');
        echo getenv('dbpass');
//comment
    }

}

?>