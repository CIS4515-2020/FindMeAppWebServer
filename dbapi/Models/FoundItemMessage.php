<?php

namespace dbapi\Models;

require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Utilities/MySqlHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/Model.php';
    
use Exception;
use mysqli_stmt;
use dbapi\Utilities\MySqlHandler;

class FoundItemMessage extends Model
{
    protected $table = 'found_item_message';
    protected $key = 'found_item_id';
    
    protected $fillable = [
        'item_id',
        'lat',
        'lng',
        'message'
    ];
    
    protected $cols = [
        'item_id' => 'i',
        'lat' => 'd',
        'lng' => 'd',
        'message' => 's'
    ];
    
    function __construct( $attributes = array() ){
        $this->setAttributes( $attributes );
    }
    
    public function getId(){
        return $this->attributes[$this->key];
    }
    
    public function getItemId(){
        return $this->attributes['item_id'];
    }
    
    public function getMessage(){
        return $this->attributes['message'];
    }
    
    public function setCoordinates( $lat, $lng ){
        $this->attributes['lat'] = $lat;
        $this->attributes['lng'] = $lng;
    }
    
    public function setMessaage( $val ){
        $this->attributes['message'] = $val;
    }
    
    //get found item message by item id
    //returns array of FoundItemMessage
    //throws exception
    public static function scopeByItem( $item_id ){
        
        $items = [];
        
        try{
            $query = "SELECT * FROM `found_item_message` WHERE item_id = ? ORDER BY found_on DESC";
            $argTypes = "i";
            $args = [];
            $args[] = &$argTypes;
            $args[] = &$item_id;
            $row = [];
            $dbHandler = new MySqlHandler();
            if( ! $dbHandler->dbConnected() ){
                throw new Exception('DB connection failed');
            }
            $select = $dbHandler->executePreparedQuery( $query, $args, $row );
            if( $select !== null && $select instanceof mysqli_stmt && $select->num_rows > 0 ){
                while( $select->fetch() ){
                    $item = new self();
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
    
}