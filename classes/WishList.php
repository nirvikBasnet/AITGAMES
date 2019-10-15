<?php
namespace aitsydney;
use aitsydney\Database;
use \Exception;
class WishList extends Database {
  private $response = array();
  private $errors = array();
  
  public function __construct(){
    parent::__construct();
  }
  private function getUserAuthStatus(){
    if( session_status() == PHP_SESSION_NONE ){
      session_start();
    }
    return ( isset($_SESSION['auth']) ) ? $_SESSION['auth'] : false;
  }
  public function addItem( $product_id ){
    try{
      if( !$this -> getUserAuthStatus() ){
        throw new Exception('user not authenticated');
      }
      else{
        // get user's list
        $wish_id = $this -> getWishListId( $this -> getUserAuthStatus() );
        //add item to the list
        if(!$wish_id){
          throw new Exception('list cannot be found');
        }
      }
    }
    catch( Exception $exc ){
      $this -> errors['list'] = $exc -> getMessage();
    }
    // query to insert item
    $add_query = "
    INSERT INTO wishlist_item
    (wishlist_id,product_id,created)
    VALUES
    (?, ?, NOW() )
    ";
    // database stuff
    try{
      $statement = $this -> connection -> prepare( $add_query );
      if(!$statement){
        throw new Exception('query error');
      }
      if(!$statement -> bind_param('ii', $wish_id, $product_id ) ){
        throw new Exception('cannot bind parameter');
      }
      if(!$statement -> execute() ){
        throw new Exception('cannot execute ' . __LINE__ );
      }
    }
    catch( Exception $exc ){
      $this -> errors['database'] = $exc -> getMessage();
    }
    //check for other errors
    try{
      //if the item is already in the database
      if( $this -> connection -> errno == '1062' ){
        throw new Exception('item already in list');
      }
      // if there are other errors
      elseif( $this -> connection -> errno ){
        throw new Exception('error inserting item');
      }
    }
    catch( Exception $exc ){
      $this -> errors['insert'] = $exc -> getMessage();
      
    }
    if( count($this -> errors) == 0 ){
      return true;
    }
    else{
      return false;
    }
  }
  private function getWishListId( $account_id, $createnew = true ){
    //find user's wishlist or create a new one
    $find_query = "SELECT wishlist_id FROM wishlist WHERE account_id = UNHEX( ? )";
    try{
      $statement = $this -> connection -> prepare( $find_query );
      if(!$statement){
        throw new Exception('query error');
      }
      if(!$statement -> bind_param('s', $account_id ) ){
        throw new Exception('cannot bind parameter');
      }
      if(!$statement -> execute() ){
        throw new Exception('cannot bind parameter');
      }
      $result = $statement -> get_result();
    }
    catch( Exception $exc ){
      $this -> errors['database'] = $exc -> getMessage();
      $this -> response['success'] = false;
      $this -> response['errors'] = $this -> errors;
      return false;
    }
    // check result
    if( $result -> num_rows == 0 && $createnew == true ){
      //user does not have a wishlist so we create it
      $wishlist_id = $this -> createWishList( $account_id );
    }
    else{
      $row = $result -> fetch_assoc();
      $wishlist_id = $row['wishlist_id'];
    }
    return $wishlist_id;
  }
  private function createWishList( $account_id ){
    $create_query = "
    INSERT INTO wishlist 
    (account_id,created,active)
    VALUES
    ( UNHEX(?), NOW(), 1 )";
    try{
      $statement = $this -> connection -> prepare($create_query);
      if(!$statement){
        throw new Exception('query error');
      }
      if(!$statement -> bind_param('s', $account_id ) ){
        throw new Exception('cannot bind parameter');
      }
      if(!$statement -> execute() ){
        throw new Exception('cannot bind parameter');
      }
      return $this -> connection -> insert_id;
    }
    catch( Exception $exc ){
      return false;
    }
  }
  public function getWishListTotal(){
    $get_query = "
    SELECT 
    COUNT(product_id) AS total
    FROM wishlist_item
    WHERE wishlist_id = ?
    ";
    //get the account id
    $account_id = $this -> getUserAuthStatus();
    if( !$account_id ){
      return false;
    }
    //get the wishlist id but not create new one
    $wishlist_id = $this -> getWishListId( $account_id, false );
    if( !$wishlist_id){
      return false;
    }
    $statement = $this -> connection -> prepare( $get_query );
    $statement -> bind_param('i', $wishlist_id );
    $statement -> execute();
    $result = $statement -> get_result();
    $row = $result -> fetch_assoc();
    return $row['total'];
  }
  
  public function getWishListItems(){
    $items_query = "
    SELECT
    @product_id := wishlist_item.product_id AS product_id,
    ( SELECT @image_id := product_image.image_id FROM product_image WHERE product_image.product_id = @product_id LIMIT 1 ) AS image_id,
    ( SELECT image_file_name FROM image WHERE image.image_id = @image_id ) AS image,
    product.name,
    product.price,
    product.description
    FROM 
    wishlist_item
    INNER JOIN product
    ON wishlist_item.product_id = product.product_id
    WHERE wishlist_item.wishlist_id = ?
    ";
    //get the account id
    $account_id = $this -> getUserAuthStatus();
    if( !$account_id ){
      return false;
    }
    //get the wishlist id but not create new one
    $wishlist_id = $this -> getWishListId( $account_id, false );
    if( !$wishlist_id){
      return false;
    }
    $statement = $this -> connection -> prepare( $items_query );
    $statement -> bind_param('i', $wishlist_id );
    $statement -> execute();
    $result = $statement -> get_result();
    $data = array();
    while( $row = $result -> fetch_assoc() ){
      array_push( $data, $row );
    }
    return $data;
  }
  public function removeItem($product_id){
    $delete_query = "
    DELETE FROM wishlist_item WHERE product_id = ? AND wishlist_id = ?
    ";
    //get the account id
    $account_id = $this -> getUserAuthStatus();
    if( !$account_id ){
      return false;
    }
    //get the wishlist id but not create new one
    $wishlist_id = $this -> getWishListId( $account_id, false );
    if( !$wishlist_id){
      return false;
    }
    $statement = $this -> connection -> prepare( $delete_query );
    $statement -> bind_param('ii', $product_id, $wishlist_id );
    if( $statement -> execute() ){
      return true;
    }
    else{
      return false;
    }
  }
}
?>