<?php
namespace aitsydney;

use aitsydney\Database;

class ShoppingCart extends Database{
  private $response = array();
  private $errors = array();

  public function __construct(){
    parent::__construct();
  }
  private function getUserAuthStatus(){
    if( session_status() == PHP_SESSION_NONE ){
      session_start();
    }
    return ( isset($_SESSION['auth'] ) ) ? $_SESSION['auth'] : false ;
  }

  private function getUserCartId( $account_id, $createnew = false ){
    //get the user's cart id or return a new one
  }

  // add an item to the cart
  public function setItem( $product_id, $quantity ){

  }
}
?>