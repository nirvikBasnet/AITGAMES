<?php
require('vendor/autoload.php');
// create account
use aitsydney\Account;
if( $_SERVER['REQUEST_METHOD']=='POST' ){
  $email = $_POST['email'];
  $password = $_POST['password'];
  //create an instance of account class
  $acc = new Account();
  $register = $acc -> register( $email, $password );
  print_r( $register );
}
else{
  $register = '';
}
if( $_SERVER['REQUEST_METHOD']=='POST' ){
  $email = $_POST['email'];
  $password = $_POST['password'];
  //create an instance of account class
  $acc = new Account();
  $login = $acc -> login( $email, $password );
}
else{
  $login='';
}

if(isset($_SESSION['auth'])){$loggedin=true;}else{$loggedin=false;}

use aitsydney\WishList;
$wish_list = new WishList();
$wish_total = $wish_list -> getWishListTotal();
use aitsydney\ShoppingCart;
$cart = new ShoppingCart();
$cart_total = $cart -> getCartTotal();
// create navigation
use aitsydney\Navigation;
$nav = new Navigation();
$nav_items = $nav -> getNavigation();
// create twig loader for templates
$loader = new Twig_Loader_Filesystem('templates');
// create twig environment and pass the loader
$twig = new Twig_Environment($loader);
// call a twig template
$template = $twig -> load('register.twig');
//pass values to twig
echo $template -> render([
    'wish_count' => $wish_total,
    'cart_count' => $cart_total,
    'navigation' => $nav_items,
    'login'=> $login,
    'loggedin'=> $loggedin,
    'title' => 'Register for an account',
    'response' => $register
]);
?>