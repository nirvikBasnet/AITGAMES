<?php
require('vendor/autoload.php');
use aitsydney\Search;
if( isset($_GET['query']) ){
  $search = new Search();
  $result = $search -> getSearchResult();
}
else{
  $result = '';
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
$navigation = $nav -> getNavigation();
//create twig loader for templates
$loader = new Twig_Loader_Filesystem('templates');
//create twig environment and pass the loader
$twig = new Twig_Environment($loader);
//call a twig template
$template = $twig -> load('search.twig');
//output the template and pass the data
echo $template -> render( array(
  'result' => $result,
  'navigation' => $navigation,
  'wish_count' => $wish_total,
  'cart_count' => $cart_total,
  'login'=> $login,
    'loggedin'=> $loggedin,
  'title' => "Search Result for " . $result['query']
) );
?>