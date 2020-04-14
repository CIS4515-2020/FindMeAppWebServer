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
        'user_id',
        'name',
        'description'
    ];
    
    protected $cols = [
        'user_id' => 'i',
        'name' => 's',
        'description' => 's',
        'lost' => 'i'
    ];
    
    function __construct( $attributes = array() ){
        $this->setAttributes( $attributes );
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
                    $item->original = $row;
                    $item->attributes = $row;
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
    
}