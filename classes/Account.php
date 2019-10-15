<?php
namespace aitsydney;
use aitsydney\Database;
use \Exception;
class Account extends Database{
  public function __construct(){
    parent::__construct();
  }
  public function register( $email, $password ){
    $query = "
      INSERT INTO account ( account_id, email, password, created, accessed, updated )
      VALUES ( UNHEX(?), ?, ?, NOW(), NOW(), NOW() )
    ";
    $register_errors = array();
    $response = array();
    if( strlen($password) < 8 ){
      $register_errors['password'] = "minimum 8 characters";
    }
    if( filter_var($email, FILTER_VALIDATE_EMAIL ) == false ){
      $register_errors['email'] = "email address not valid";
    }
    //if there are no errors with email and password
    if( count( $register_errors ) == 0 ){
      //hash the password
      $hash = password_hash( $password, PASSWORD_DEFAULT );
      $account_id = $this -> createAccountId();
      
      try{
        $statement = $this -> connection -> prepare( $query );
        if( $statement == false ){
          throw( new \Exception('query error') );
        }
        
        if( $statement -> bind_param('sss', $account_id , $email, $hash ) == false ){
          throw( new \Exception('cannot bind parameters') );
        }
        if( $statement -> execute() == false ){
          throw( new \Exception('failed to execute') );
        }
        else{
          // account is created in database
          
          $response['success'] = true;
        }
      }
      catch( Exception $exc ){
        error_log( $exc -> getMessage() );
      }
    }
    else{
      //return error messages
      $register_response['errors'] = $register_errors;
      $register_response['success'] = false;
      //return what the user wrote in the email field
      $register_response['email'] = $email;
    }
    return $register_response;
  }
  public function createAccountId(){
    if( function_exists('random_bytes') ){
      $bytes = random_bytes(16);
    }
    else{
      $bytes = openssl_random_pseudo_bytes(16);
    }
    return bin2hex($bytes);
  }
  private function setUserSession( $account_id ){
    if( session_status() == PHP_SESSION_NONE ){
      session_start();
    }
    $_SESSION['auth'] = $account_id;
  }
  public function logout(){
    if( session_status() == PHP_SESSION_NONE ){
      session_start();
    }
    unset( $_SESSION['auth'] );
  }
  public function login( $email, $password ){
    $response = array();
    $errors = array();
    $query = "
      SELECT HEX( account_id ) as account_id,email,password
      FROM account
      WHERE email = ?
    ";
    try{
      $statement = $this -> connection -> prepare( $query );
      if( $statement == false ){
        throw new Exception('query error');
      }
      if( $statement -> bind_param('s', $email ) == false ){
        throw new Exception('parameter error');
      }
      if( $statement -> execute() == false ){
        throw new Exception('execution error');
      }
    }
    catch( Exception $exc ){
      error_log( $exc -> getMessage() );
      $errors['system'] = 'We are sorry, something is terribly wrong';
      $response['errors'] = $errors;
      $response['success'] = false;
      return $response;
    }
    // check authentication errors
    try{
      $result = $statement -> get_result();
      $row = $result -> fetch_assoc();
      if( $result -> num_rows == 0 ){
        throw new Exception('credentials supplied do not match our system');
      }
      //check password
      if( password_verify( $password, $row['password']) == false ){
        // password supplied does not match hash in database
        throw new Exception('credentials supplied do not match our system');
      }
    }
    catch( Exception $exc ){
      $errors['account'] = $exc -> getMessage();
      $response['errors'] = $errors;
      $response['success'] = false;
      return $response;
    }
    if( count($errors) == 0 ){
      $response['success'] = true;
      $this -> setUserSession( $row['account_id']);
    }
    return $response;
  }
  
}
?>