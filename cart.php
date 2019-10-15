<?php
require('vendor/autoload.php');
//initialise user's wishlist
use aitsydney\WishList;
$wish_list = new WishList();
//initialise user's cart total
use aitsydney\ShoppingCart;
$cart = new ShoppingCart();

if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset( $_GET['delete'] ) ){
  $product_id = $_GET['delete'];
  $cart_id = $_GET['cart_id'];
  $delete = $cart -> removeItem( $cart_id, $product_id );
}
else{
  $delete = '';
}
if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset( $_GET['checkout'] ) ){
  print_r($_GET);
}

// get the total wishlist items for the navigation
$wish_total = $wish_list -> getWishListTotal();
// get the total cart items for the page
$cart_total = $cart -> getCartTotal();
// get the cart_id
$cart_id = $cart -> getCartId();
// get the cart items
$cart_items = $cart -> getCartItems( $cart_id );
// $wish_items = $wish_list -> getWishListItems();
// create navigation
use aitsydney\Navigation;
$nav = new Navigation();
$navigation = $nav -> getNavigation();
//create twig loader for templates
$loader = new Twig_Loader_Filesystem('templates');
//create twig environment and pass the loader
$twig = new Twig_Environment($loader);
//call a twig template
$template = $twig -> load('cart.twig');
//output the template and pass the data
echo $template -> render( array(
  'delete' => $delete,
  'wish_count' => $wish_total,
  'cart_count' => $cart_total,
  'cart_items' => $cart_items,
  'navigation' => $navigation,
  'title' => "Shopping Cart"
) );
?>