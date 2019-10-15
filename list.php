<?php
require('vendor/autoload.php');
//get user's wishlist total
use aitsydney\WishList;
$wish_list = new WishList();

use aitsydney\ShoppingCart;
$cart = new ShoppingCart();
$cart_total = $cart -> getCartTotal();

if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset( $_GET['action'] ) ){
  $product_id = $_GET['product_id'];
  if( $_GET['action'] == 'delete' ){
    $delete = $wish_list -> removeItem( $product_id );
  }
}
// get the total wishlist items for the navigation
$wish_total = $wish_list -> getWishListTotal();
// get the wishlist items for the page
$wish_items = $wish_list -> getWishListItems();
// create navigation
use aitsydney\Navigation;
$nav = new Navigation();
$navigation = $nav -> getNavigation();
//create twig loader for templates
$loader = new Twig_Loader_Filesystem('templates');
//create twig environment and pass the loader
$twig = new Twig_Environment($loader);
//call a twig template
$template = $twig -> load('wishlist.twig');
//output the template and pass the data
echo $template -> render( array(
  'navigation' => $navigation,
  'wish_count' => $wish_total,
  'cart_count' => $cart_total,
  'wish_items' => $wish_items,
  'title' => "Wish List"
) );
?>