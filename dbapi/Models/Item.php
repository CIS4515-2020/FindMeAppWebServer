<?php

namespace dbapi\Models;

require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Utilities/MySqlHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/Model.php';
    
use Exception;
use mysqli_stmt;
use dbapi\Utilities\MySqlHandler;

class Item extends Model
{
    protected $table = 'item';
    protected $key = 'item_id';
    
    protected $fillable = [
        'item_id',
        'user_id',
        'name',
        'description'
    ];
    
    protected $cols = [
        'item_id' => 'i',
        'user_id' => 'i',
        'name' => 's',
        'description' => 's',
        'lost' => 'i'
    ];
    
    function __construct( $attributes = array() ){
        $this->setAttributes( $attributes );
    }
    
    public function getName(){
        return $this->attributes['name'];
    }
    
    public function getDescription(){
        return $this->attributes['description'];
    }
    
    public function getUserId(){
        return $this->attributes['user_id'];
    }
    
    public function setName( $name ){
        $this->attributes['name'] = $name;
    }
    
    public function setDescription( $val ){
        $this->attributes['description'] = $val;
    }
    
    public function setLost( $val ){
        if( $val == 'true' ){
            $val = 1;
        }elseif( $val == 'false' ){
            $val = 0;
        }
        $this->attributes['lost'] = $val;
    }
    
    //get items by user
    //throws exception
    public static function scopeByUser( $user_id ){
        
        $items = [];
        
        try{
            $query = "SELECT * FROM `item` WHERE user_id = ?";
            $argTypes = "i";
            $args = [];
            $args[] = &$argTypes;
            $args[] = &$user_id;
            $row = [];
            $dbHandler = new MySqlHandler();
            if( ! $dbHandler->dbConnected() ){
                throw new Exception('DB connection failed');
            }
            $select = $dbHandler->executePreparedQuery( $query, $args, $row );
            if( $select !== null && $select instanceof mysqli_stmt && $select->num_rows > 0 ){
                while( $select->fetch() ){
                    $item = new Item();
                    $values = $dbHandler->derefrence_array($row);
                    $item->original = $values;
                    $item->attributes = $values;
                    $items[] = $item;
                }
            
                $select->close();
            }
            $dbHandler->close();
        }catch (Exception $e){
            unset($dbHandler);
            if( $e->getMessage == 'DB connection failed' ){
                throw new Exception( $e->getMessage() );
            }
        }
        
        return $items;
    }
    
    //get next available item id
    //reserve id
    public static function nextId(){
        $next_id = 0;
        
        try{
            $query = "SELECT MAX(ic.item_id) + 1 AS next_id FROM (
                    	SELECT item_id FROM item 
                        UNION
                        SELECT reserved_item_id AS item_id FROM reserved_item_id
                    ) ic";
            
            $dbHandler = new MySqlHandler();
            if( ! $dbHandler->dbConnected() ){
                throw new Exception('DB connection failed');
            }
            
            $select = $dbHandler->executeQuery( $query );
            if( !is_null($select) ){
                while( $row = $select->fetch_assoc() ){
                    $next_id = $row['next_id'];
                }
                $select->close();
                
                $query = "INSERT INTO reserved_item_id (reserved_item_id) VALUES ($next_id)";
                $inserted_rows = $dbHandler->executeScalarQuery($query);
            }
            
            $dbHandler->close();
            
        }catch (Exception $e){
            unset($dbHandler);
            if( $e->getMessage == 'DB connection failed' ){
                throw new Exception( $e->getMessage() );
            }
        }
        
        return $next_id;
    }
    
}