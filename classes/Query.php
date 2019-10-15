<?php
namespace aitsydney;

use aitsydney\Database;
use \Exception;

class Query extends Database{
  private $errors = array();
  private $response = array();
  
  public function __construct(){
    parent::__construct();
  }

  public function run( String $query, Array $params = array() ){
    //clear errors for each run
    $this -> errors = array();
    //check for errors
    try{
      // make sure query containing ? has the right number of parameters
      $paramCheck = $this -> checkParamCount( $query, $params );
      if( $paramCheck['success'] == false ){
        $this -> errors['parameter'] = $paramCheck['error'];
        throw new Exception( $paramCheck['error'] );
      }
      $queryType = $this -> queryType( $query );
      if( $queryType['success'] == false ){
        $this -> errors['query'] = $queryType['error'];
        throw new Exception( $queryType['error'] );
      }
    }
    catch( Exception $exc ){
      return $this -> respond( false );
    }
    // run the query
    try{
      $statement = $this -> connection -> prepare( $query );
      if( !$statement ){
        $this -> errors['query'] = $this -> connection -> error;
        throw new Exception( $this -> connection -> error );
      }
      if( count($params) > 0 ){
        $param_string = $this -> buildParamString($params);
        if( !$param_string ){
          $this -> errors['parameter'] = 'error building parameter string';
          throw new Exception( $this -> errors['parameter'] );
        }
        if( !$statement -> bind_param($param_string, ...$params) ){
          $this -> errors['binding'] = 'error binding parameters';
          throw new Exception( $this -> errors['binding'] );
        }
      }
      if( !$statement -> execute() ){
        $this -> errors['execute'] = 'error executing';
        $this -> errors['error_no'] = $this -> connection -> errno;
        $this -> errors['message'] = $this -> connection -> error;
        throw new Exception('error executing' );
      }
    }
    catch( Exception $exc ){
      return $this -> respond( false );
    }
    // select query needs data returned
    
    if( $queryType['type'] == 'select'){
      $data = array();
      $result = $statement -> get_result();
      while( $row = $result -> fetch_assoc() ){
        array_push( $data, $row );
      }
      return $this -> respond( true, $data );
    }
    if( $queryType['type'] == 'insert' ){
      $this -> response['id'] = $this -> connection -> insert_id;
    }
    
    return $this -> respond( true );
  }

  private function respond( Bool $success, Array $data = array() ){
    $this -> response['success'] = $success;
    if( count($this -> errors) > 0 ){
      $this -> response['success'] = false;
      $this -> response['errors'] = $this -> errors;
    }
    if( count($data) >= 0 ){
      $this -> response['count'] = count( $data );
      $this -> response['data'] = $data;
    }
    return $this -> response;
  }

  private function queryType( $query ){
    // check the type of query, returns select, insert, update or delete
    // remove spaces before and after string and convert to lowercase
    // in case the query is "Select" or "sEleCt", etc
    $response = array();
    $query_types = array('select','insert','update','delete');
    $lower = strtolower( trim($query) );
    $count = count($query_types);
    $result_type = null;
    for( $i=0 ; $i < $count; $i++ ){
      if( strpos( $lower, $query_types[$i] ) === 0 ){
        $result_type = $query_types[$i];
      }
    }
    if( !$result_type ){
      $response['success'] = false;
      $response['error'] = 'unknown query type';
    }
    else{
      $response['success'] = true;
      $response['type'] = $result_type;
    }
    return $response;
  }

  private function checkParamCount( String $query, Array $params=array() ){
    $response = array();
    //count query parameters and the number of ? in the query
    // count number of ? in the query
    $slot_count = substr_count( $query, '?' );
    // count how many parameters received
    $param_count = count( $params );
    try{
      if( $slot_count !== $param_count ){
        $err_string = "need $slot_count parameters but $param_count supplied";
        throw new Exception($err_string);
      }
      else{
        $response['success'] = true;
      }
    }
    catch( Exception $exc ){
      $response['success'] = false;
      $response['error'] = $exc -> getMessage();
    }
      return $response;
  }

  private function buildParamString(Array $params){
    $param_count = count( $params );
    //check the types of parameters
    $param_types = array();
    if( $param_count > 0 ){
      foreach( $params as $param ){
        if( is_int($param) ){
          array_push( $param_types, 'i');
        }
        elseif( is_string($param) ){
          array_push( $param_types, 's');
        }
        elseif( is_double($param) ){
          array_push( $param_types, 'd');
        }
        else{
          array_push( $param_types, 'b');
        }
      }
      $param_string = implode('' , $param_types );

      return $param_string;
    }
    else{
      return false;
    }
  }
  
}
?>