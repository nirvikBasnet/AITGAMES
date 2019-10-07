<?php
namespace aitsydney;

use aitsydney\Database;

class Category extends Database{
  public $categories = array();

  public function __construct(){
    parent::__construct();
  }

  public function getCategories(){
    $category_query = "
      SELECT category_id,
      category_name
      FROM category
      WHERE active = 1
    ";
    $statement = $this -> connection -> prepare( $category_query );
    try{
      if( $statement -> execute() == false ){
        throw( new Exception('Query failed to execute') );
      }
      else{
        $result = $statement -> get_result();
        $category_items = array();
        while( $row = $result -> fetch_assoc() ){
          array_push( $category_items , $row );
        }
        $this -> categories['items'] = $category_items;
        $this -> categories['active'] = $this -> getActive();
      }
      return $this -> categories;
    }
    catch( Exception $exc ){
      echo $exc -> getMessage();
    }
  }

  private function getActive(){
    //get the active category from $_GET
    if( isset( $_GET['category_id'] ) ){
      return $_GET['category_id'];
    }
    else{
      return null;
    }
  }
}

?>