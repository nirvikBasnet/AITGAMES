<?php
require('vendor/autoload.php');

use aitSydney\Navigation;

//create twig loader
//$loader = new \Twig\Loader\FilesystemLoader('templates');
$loader = new Twig_Loader_Filesystem('templates');

//create twig environment
$twig = new Twig_Environment($loader);

//load a twig template
$template = $twig -> load('sell.twig');

//pass values to twig
echo $template -> render([
    'title' => 'Hello shop'
]);
?>