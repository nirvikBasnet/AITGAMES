<?php
namespace aitsydney;
use aitsydney\Database;
class Navigation extends Database{
    public $isAuthenticated = false;
    private $min_level = 0;
    private $max_level = 1;
    public $navigation = array();
    public function __construct(){
        parent::__construct();
        $this -> initSession();
        if( isset($_SESSION['auth']) ){
            $this -> isAuthenticated = true;
            $this -> min_level = 1;
            $this -> max_level = 2;
        }
    }
    private function initSession(){
        if( session_status() == PHP_SESSION_NONE ){
            session_start();
        }
    }
    public function getNavigation(){
        $nav_query = "
            SELECT 
            name,
            url,
            menu,
            content
            FROM page
            WHERE level >= ?
            AND level <= ?
            AND active = 1
            ORDER BY menu_order ASC
        ";
        $statement = $this -> connection -> prepare( $nav_query );
        $statement -> bind_param( 'ii', $this -> min_level, $this -> max_level );
        try{
            if( $statement -> execute() == false ){
                throw( new Exception('Query error') );
            }
            else{
                $result = $statement -> get_result();
                $items = array();
                while( $row = $result -> fetch_assoc() ){
                    array_push( $items, $row );
                }
                $this -> navigation['items'] = $items;
                $this -> navigation['active'] = basename( $_SERVER['PHP_SELF'] );
            }
            return $this -> navigation;
        }
        catch( Exception $e ){
            echo $e -> getMessage();
        }
    }
}
?>