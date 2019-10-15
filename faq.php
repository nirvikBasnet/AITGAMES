<?php
require('vendor/autoload.php');

//get user's wishlist total
use aitsydney\WishList;

$wish = new WishList();
$wish_total = $wish -> getWishListTotal();

//create an instance of Product class
use aitsydney\Product;

$p = new Product();
$products = $p -> getProducts();

//create categories
use aitsydney\Category;

$cat = new Category();
$categories = $cat -> getCategories();

//create navigation
use aitsydney\Navigation;

$nav = new Navigation();
$navigation = $nav -> getNavigation();

//create twig loader for templates
$loader = new Twig_Loader_Filesystem('templates');

//create twig environment
$twig = new Twig_Environment($loader);

//load a twig template
$template = $twig -> load('faq.twig');

echo $template -> render( array(
    'categories' => $categories,
    'wish' => $wish_total,
    'navigation' => $navigation,
    'products' => $products,
    'title' => 'Frequently Asked Question'
) );
?>