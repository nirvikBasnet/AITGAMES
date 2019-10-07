<?php
require('vendor/autoload.php');

use aitsydney\Navigation;

//get user's wishlist total
use aitsydney\WishList;

$wish = new WishList();

if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset( $_GET['add'] ) ){
    $product_id = $_GET['product_id'];
    //if 'add' == 'list' means the wishlist button has been clicked
    if( $_GET['add'] == 'list' ){
        $add = $wish -> addItem($product_id);
    }
}

$wish_total = $wish -> getWishListTotal();

$nav = new Navigation();
$navigation = $nav -> getNavigation();

use aitsydney\ProductDetail;

//get the product id from request
if( isset( $_GET['product_id']) == false ){
    echo "incorrect parameter";
    exit();
}

//initialise ProductDetail class
$pd = new ProductDetail();
$detail = $pd -> getProductDetail( $_GET['product_id'] );

//create the view using Twig
$loader = new Twig_Loader_Filesystem('templates');
//create twig environment and pass the loader
$twig = new Twig_Environment($loader);
//call a twig template
$template = $twig -> load('detail.twig');
//output the template and pass the data

echo $template -> render( array(
    'navigation' => $navigation,
    'wish' => $wish_total,
    'detail' => $detail,
    'title' => $detail['product']['name']
) );
?>