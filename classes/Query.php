<?php
namespace aitsydney;

use aitsydney\Database;

class Query extends Database{
  private $errors = array();
  private $type = '';

  public function __construct(){
    parent::__construct();
  }

    //check what kind of query it is
    $lower = trim( strtolower( $query ) );
    if( strpos( $lower , 'select' ) === 0 ){
      // it is a select query so result needs to be returned
      $this -> type = 'select';
    }
      $queryType = $this -> queryType( $query );
      if( $queryType['success'] == false ){
        $this -> errors['query'] = $queryType['error'];
        throw new Exception( $queryType['error'] );
      }
    }
    elseif( strpos( $lower , 'delete' ) === 0 ){
      $this -> type = 'delete';
    }

    //check for parameter errors
    try{
      if( $param_count != $q_count ){
        throw new Exception('mismatch count in parameters');
      }
    }
    catch( Exception $exc ){
      $this -> errors['parameters'] = $exc -> getMessage();
    }
    //check for database errors
    try{
      $statement = $this -> connection -> prepare($query);
      if(!$statement){
        throw new Exception('query error');
      }
      }
      // execute and check for errors
      if(!$statement -> execute() ){
        throw new Exception('execution error');
      }
    }
    catch( Exception $exc ){
      $this -> errors['query'] = $exc -> getMessage();
    }
    //check for mysql errors
    try{
      if( $this -> connection -> errno ){
        throw new Exception('mysql error '. $this -> connection -> errno )
      }
    }
    catch( Exception $exc ){
      // log error
      $this -> errors['database'] = $exc -> getMessage();
    }

    //respond to query
    if( $this -> type == 'insert' ){
      $this -> response['insert_id'] = $this -> connection -> insert_id;
      $this -> response['success'] = true;
    }
    if( $this -> type == 'update' || $this -> type == 'delete' ){
      $this -> response['success'] = true;
    }
    if( $this -> type == 'select' ){
      $result = $statement -> get_result();
      $data = array();
      while( $row = $result -> fetch_assoc() ){
        array_push( $data, $row );
      }
      $this -> response['data'] = $data;
      $this -> response['success'] = true;
    }
  }
}
?>