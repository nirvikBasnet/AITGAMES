<?php
namespace aitsydney;
use aitsydney\Product;
class ProductDetail extends Product{
  public $product_detail = array();
  public function __construct(){
    parent::__construct();
  }
    //get product by id
    public function getProductDetail( $id ){
        $query = "
            SELECT
            product.product_id,
            product.name,
            product.description,
            product.price,
            product_quantity.quantity
            FROM product
            INNER JOIN product_quantity
            ON product.product_id = product_quantity.product_id
            WHERE product.product_id = ?
        ";
        $statement = $this -> connection -> prepare( $query );
        $statement -> bind_param( 'i', $id );
        if( $statement -> execute() ){
            // $product_detail = array();
            $result = $statement -> get_result();
            $row = $result -> fetch_assoc();
            $this -> product_detail['product'] = $row;
            $this -> product_detail['images'] = $this -> getProductImages( $id );
            return $this -> product_detail;
        }
    }
  private function getProductImages( $id ){
    $images_query = "
      SELECT 
      product_image.image_id,
      image_file_name
      FROM product_image
      INNER JOIN image
      ON product_image.image_id = image.image_id
      WHERE product_id = ?
    ";
    $statement = $this -> connection -> prepare( $images_query );
    $statement -> bind_param( 'i' , $id );
    $product_images = array();
    if( $statement -> execute() ){
      $result = $statement -> get_result();
      while( $row = $result -> fetch_assoc() ){
        array_push( $product_images , $row );
      }
    }
    return $product_images;
  }
}
?>