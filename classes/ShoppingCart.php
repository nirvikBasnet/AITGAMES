<?php

namespace aitsydney;

use aitsydney\Database;
use aitsydney\Query;
use \Exception;

class ShoppingCart extends Query{
  private $response = array();
  private $errors = array();
  private $account_id = null;

  public function __construct(){
    parent::__construct();
    $this -> account_id = ( $this -> getAuthStatus() ) ? $this -> getAuthStatus() : null;
    //combine session cart with the one in database
    if( $this -> account_id ){
      $this -> combineCartItems();
    }
  }

  private function getAuthStatus(){
    //check if session has started
    if( session_status() == PHP_SESSION_NONE ){
      session_start();
    }
    //check if user is authenticated return user's account_id or false
    return ( isset( $_SESSION['auth'] ) ) ? $_SESSION['auth'] : false;
  }

  private function combineCartItems(){
    if( $this -> account_id ){
      // find the user's items in database
      $cart_id = $this -> getCartId();
      $db_items = $this -> getCartItems( $cart_id );
      // find the user's items in SESSION
      if( is_array($_SESSION['cart']['items']) ){
        //for each item in the session, see if it's already in the database cart
        foreach( $_SESSION['cart']['items'] as $index => $item ){
          $item_index = array_search( $item['product_id'] , array_column($db_items, 'product_id') );
          $session_quantity = $item['quantity'];
          $product_id = $item['product_id'];
          if( $item_index !== false ){
            $db_quantity = $db_items[$item_index]['quantity'];
            $new_quantity = $db_quantity + $session_quantity;
            $update = $this -> updateCartItem( $cart_id, $item['product_id'], $new_quantity );
          }
          elseif( $item_index === false ){
            $add = $this -> addNewCartItem( $cart_id, $product_id , $session_quantity );
          }
        }
        //clear the session cart
        $_SESSION['cart']['items'] = array();
      }
      // add or update database items with the ones in session
       
    }
  }
  public function addItem( $product_id, $quantity ){
    $cart_id = $this -> getCartId();
    // try to add item to cart
    try{
      // if item is not in the cart
      $adding = $this -> addNewCartItem( $cart_id, $product_id, $quantity );
      
      // if adding fails, check if it's duplicate error (1062)
      if( $adding['success'] == false && $adding['errors']['error_no'] == '1062' ){
        //get the item in the cart so we know its current quantity
        $item_query = "
          SELECT product_id,quantity 
          FROM shopping_cart_item 
          WHERE product_id = ?
          AND cart_id = UNHEX( ? )
          AND active = 1
        ";
        $item = $this -> run( $item_query, array( $product_id, $cart_id ) );
        if( $item['success'] && $item['data'] ){
          //update the item
          $new_quantity = $item['data'][0]['quantity'] + $quantity;
          $update = $this -> updateCartItem( $cart_id, $product_id, $new_quantity);
        }
      }
      
    }
    catch( Exception $exc ){
      $msg = $exc -> getMessage();
    } 
  }

  public function removeItem( $cart_id, $product_id ){
    if( $this -> account_id ){
      $remove_query = "
      DELETE FROM shopping_cart_item 
      WHERE cart_id = UNHEX(?)
      AND product_id = ?
      ";
      $delete = $this -> run( $remove_query, array($cart_id,$product_id));
      return ($delete['success'] == true ) ? true : false;
    }
    else{
      //find the items in session cart
      foreach( $_SESSION['cart']['items'] as $index => $item ){
        if($item['product_id'] == $product_id){
          array_splice( $_SESSION['cart']['items'],$index,1 );
          break;
          return true;
        }
      }
      return false;
    }
  }

  public function getCartItems( $cart_id ){
    $response = array();
    if( $this -> account_id ){
      //get the items from database
      $get_items_query = "
        SELECT 
        @product_id := shopping_cart_item.product_id AS product_id,
        HEX( shopping_cart_item.cart_id ) AS cart_id,
        shopping_cart_item.quantity AS quantity,
        product.name,
        product.price,
        product.description,
        product_quantity.quantity AS available,
        ( SELECT @image_id := product_image.image_id FROM product_image WHERE product_image.product_id = @product_id LIMIT 1 ) AS image_id,
        ( SELECT image_file_name FROM image WHERE image.image_id = @image_id ) AS image
        FROM shopping_cart_item 
        INNER JOIN product
        ON shopping_cart_item.product_id = product.product_id
        INNER JOIN product_quantity
        ON product_quantity.product_id = product.product_id
        WHERE cart_id = UNHEX( ? );
      ";
      $cart_result = $this -> run( $get_items_query, array($cart_id) );
      $response['items'] = $cart_result['data'];
    }
    else{
      if( !isset( $_SESSION['cart']) ){
        $items = array();
        return $items;
      }
      if( isset( $_SESSION['cart']['items']) && is_array( $_SESSION['cart']['items'] ) ){
        // if id matches cart_id, return the items, otherwise an empty array
        //get images and product details for each product
        $query = "
        SELECT 
        @product_id := product.product_id AS product_id,
        product.name,
        product.description,
        product.price,
        product_quantity.quantity as available,
        ( SELECT @image_id := product_image.image_id FROM product_image WHERE product_image.product_id = @product_id LIMIT 1 ) AS image_id,
        ( SELECT image_file_name FROM image WHERE image.image_id = @image_id ) AS image
        FROM product
        INNER JOIN product_quantity
        ON product.product_id = product_quantity.product_id
        ";
        // build a query for each product
        $conditionals = array();
        $product_ids = array();
        $quantities = array();
        foreach( $_SESSION['cart']['items'] as $product ){
          array_push( $conditionals, "product.product_id=?" );
          array_push( $product_ids, $product['product_id'] );
          array_push( $quantities, $product['quantity'] );
        }
        $str = implode(" OR ", $conditionals );
        // add conditionals to query
        $query = $query . " WHERE " . $str;
        //run the query
        $cart_result = $this -> run( $query, $product_ids );
        $response['items'] = $cart_result['data'];
        //add quantity data to each result
        foreach( $response['items'] as $index => $item ){
          $response['items'][$index]['quantity'] = $quantities[$index];
        }
      }
    }
    //add total to the response
    if( is_array($response['items']) ){
      $total = 0;
      foreach( $response['items'] as $item ){
        $total = $total + ( $item['price'] * $item['quantity'] );
      }
      $response['total'] = $total;
    }
    return $response;
  }

  public function updateCartItem( $cart_id, $product_id, $quantity ){
    // user is authenticated
    if( $this -> account_id ){
      $update_query = "
      UPDATE shopping_cart_item 
      SET quantity = ?
      WHERE cart_id = UNHEX( ? )
      AND product_id = ?
      ";
      $update = $this -> run( $update_query, array($quantity, $cart_id, $product_id ) );
      if( $update['success'] == true ){
        return true;
      }
      else{
        return false;
      }
    }
    // user is not authenticated
    else{
      //set the quantity of the item in session
      if( is_array( $_SESSION['cart']['items'] == false ) ){
        return false;
      }
      else{
        foreach( $_SESSION['cart']['items'] as $index => $item ){
          if( $item['product_id'] == $product_id ){
            $_SESSION['cart']['items'][$index]['quantity'] = $quantity;
          }
        }
      }
    }
  }

  public function getCartTotal(){
    if( $this -> account_id ){
      $cart_id = $this -> getCartId();
      $count_query = "
      SELECT count(product_id) AS total
      FROM shopping_cart_item
      WHERE cart_id = UNHEX( ? )
      ";
      $count = $this -> run( $count_query, array($cart_id) );
      return $count['data'][0]['total'];
    }
    else{
      $count = ( is_array($_SESSION['cart']['items']) ) 
      ? count( $_SESSION['cart']['items']) : 0 ;
      return $count;
    }
  }

  private function addNewCartItem( $cart_id, $product_id, $quantity ){
    $response = array();
    if( $this -> account_id ){
      $add_query = "
      INSERT INTO shopping_cart_item
      (cart_id, product_id, quantity)
      VALUES
      ( UNHEX( ? ), ?, ? )
      ";
      $add = $this -> run( $add_query, array($cart_id, $product_id, $quantity) );
      return $add;
    }
    else{
      // find if item is in SESSION cart already
      $item_in_cart = false;
      if( isset( $_SESSION['cart']['items']) && is_array( $_SESSION['cart']['items'] ) ){
        foreach( $_SESSION['cart']['items'] as $item ){
          if( $item['product_id'] == $product_id ){
            $new_quantity = $item['quantity'] + $quantity;
            $update = $this -> updateCartItem( $cart_id, $product_id, $new_quantity );
            $item_in_cart = true;
            break;
            return $update;
          }
        }
      }
      if( $item_in_cart == false && is_array( $_SESSION['cart']['items']) ){
        $push = array_push( $_SESSION['cart']['items'] , array( 'product_id' => $product_id, 'quantity' => $quantity) );
        $response['success'] = ($push) ? true: false;
      }
      return $response;
    }
  }

  public function getCartId( $create = true ){
    //if the user is authenticated, find cart or create in database
    if( $this -> account_id ){
      $find_cart_query = "
        SELECT HEX( cart_id ) as cart_id 
        FROM shopping_cart 
        WHERE active = 1 
        AND account_id = UNHEX( ? )
      ";
      //run is a method inherited from the Query class
      $find_cart = $this -> run( $find_cart_query, array($this -> account_id) );
      if( $find_cart['data'][0]['cart_id'] ){
        $cart_id = $find_cart['data'][0]['cart_id'];
      }
      elseif( $create == true ){
        $cart_id = $this -> createCart();
      }
    }
    //if user is not authenticated, find cart or create in session
    else{
      // find the user cart in session
      if( isset( $_SESSION['cart'] ) ){
        $cart_id = $_SESSION['cart']['id'];
      }
      elseif( $create == true ){
        $cart_id = $this -> createCart();
      }
    }
    return $cart_id;
  }

  private function createCart(){
    
    //if the user is authenticated, store in database
    if( $this -> account_id ){
      $new_cart_query = "
        INSERT INTO shopping_cart (cart_id,account_id)
        VALUES ( UNHEX(?) , UNHEX(?) )
      ";
      $cart_id = $this -> createCartId();
      $new_cart = $this -> run($new_cart_query, array($cart_id, $this -> account_id));
    }
    else{
      $cart_id = $this -> createCartId();
      //create cart in session
      $_SESSION['cart']['id'] = $cart_id;
      $_SESSION['cart']['items'] = array();
    }
    return $cart_id;
  }

  private function createCartId(){
    if( function_exists('random_bytes') ){
      $bytes = random_bytes(16);
    }
    else{
      $bytes = openssl_random_pseudo_bytes(16);
    }
    return bin2hex($bytes);
  }

  
}
?>