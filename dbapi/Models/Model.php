<?php

namespace dbapi\Models;

require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Utilities/MySqlHandler.php';
    
use Exception;
use JsonSerializable;
use mysqli_stmt;
use dbapi\Utilities\MySqlHandler;

class Model implements JsonSerializable
{
    protected $table = '';
    protected $key = '';
    
    protected $fillable = [];
    
    protected $cols = [];
    
    protected $original = [];
    
    protected $attributes = [];
    
    public function jsonSerialize() {
        if( $this instanceof User ){
            $attr = $this->attributes;
            unset($attr['password']);
            return $attr;
        }
        return $this->attributes;
    }
    
    protected function setAttributes( $arr = array() ){
        foreach( $this->fillable as $attr ){
            if( array_key_exists($attr, $arr) ){
                $this->attributes[$attr] = $arr[$attr];
            }
        }
    }
    
    public function getAttributes(){
        if( $this instanceof User ){
            $attr = $this->attributes;
            unset($attr['password']);
            return $attr;
        }
        return $this->attributes;
    }
    
    //find model by id
    //throws exception
    public static function find( $id ){
        
        try{
            $child = get_called_class();
            $model = new $child();
            
            $query = "select * from `" .$model->table. "` where `".$model->key."` = ? limit 1";
            $argTypes = "i";
            $args = array();
            $args[] = &$argTypes;
            $args[] = &$id;
            $row = [];

            $dbHandler = new MySqlHandler();
            if( ! $dbHandler->dbConnected() ){
                throw new Exception('DB connection failed');
            }
            $select = $dbHandler->executePreparedQuery( $query, $args, $row );
            if( $select !== null && $select instanceof mysqli_stmt && $select->num_rows === 1 ){
                $select->fetch();
                $select->close();
                
                $model->original = $row;
                $model->attributes = $row;
            }else{
                $model = null;
            }
            
            $dbHandler->close();
        }catch (Exception $e){
            unset($dbHandler);
            unset($model);
            throw new Exception( $e->getMessage );
        }
        
        return $model;
    }
    
    //delete model
    //returns bool
    //throws exception
    public static function delete( $id ){
        
        $result = false;
        
        try{
            $child = get_called_class();
            $model = new $child();
            
            $query = "delete from `" .$model->table. "` where `".$model->key."` = ?";
            $argTypes = "i";
            $args = array();
            $args[] = &$argTypes;
            $args[] = &$id;
            $row = [];

            $dbHandler = new MySqlHandler();
            if( ! $dbHandler->dbConnected() ){
                throw new Exception('DB connection failed');
            }
            $affected_rows = $dbHandler->executePreparedScalarQuery( $query, $args );
            if( $affected_rows > 0 ){
                $result = true;
            }
            unset($model);
            $dbHandler->close();
        }catch (Exception $e){
            unset($dbHandler);
            unset($model);
            throw new Exception( $e->getMessage );
        }
        
        return $result;
    }

    //insert model into database
    //return boolean
    //throw exception
    public function insert(){
        
        $result = false;
        
        try{
            $cols = '';
            $arg_types = '';
            $cols = [];
            $bind = [];
            $args = [];
            $args[] = &$arg_types;
            foreach( $this->fillable as $attr ){
                if( array_key_exists($attr, $this->attributes) && $this->attributes[$attr] != '' ){
                    $arg_types .= $this->cols[$attr];
                    $args[] = &$this->attributes[$attr];
                    $bind[] = '?';
                    $cols[] = '`' . $attr . '`';
                }
            }
            
            $query = 'insert into `' . $this->table . '` ';
            $query .= ' (' . implode(',', $cols) . ') ';
            $query .= ' values (' . implode(',',$bind) . ')';
            
            $dbHandler = new MySqlHandler();
            if( ! $dbHandler->dbConnected() ){
                throw new Exception('DB connection failed');
            }
            $affected_rows = $dbHandler->executePreparedScalarQuery( $query, $args );
            if( $affected_rows === 1 ){
                
                $result = true;
                
            }else{
                if( $dbHandler->errorno === MySqlHandler::$DUPLICATE_ERROR ){
                    throw new Exception('Duplicate entry.');
                }
            }
            
            $dbHandler->close();
        
        }catch (Exception $e){
            unset($dbHandler);
            if( $e->getMessage == 'DB connection failed' || $e->getMessage == 'Duplicate entry.' ){
                throw new Exception( $e->getMessage() );
            }
        }
        
        $this->attributes[$this->key] = $dbHandler->getLastInsertId();
        
        return $result;
    }
    
    //checks if changes have been made to model
    //return bool
    public function isDirty(){
        $dirty = false;
        
        foreach( $this->attributes as $attr => $value ){
            if( $this->original[$attr] !== $value ){
                $dirty = true;
                break;
            }
        }
        
        return $dirty;
    }
    
    //save model
    //return bool
    //throws exception
    public function save(){
        $result = false;
        
        try{
            $cols = '';
            $arg_types = '';
            $cols = [];
            $args = [];
            $args[] = &$arg_types;
            foreach( $this->attributes as $attr => $value ){
                if( $this->original[$attr] !== $value ){
                    $arg_types .= $this->cols[$attr];
                    $args[] = &$this->attributes[$attr];
                    $cols[] = '`' . $attr . '` = ?';
                }
            }
            
            if( count($args) > 1 ){
                $query = 'update `' . $this->table . '` set ';
                $query .= ' ' . implode(',', $cols) . ' ';
                $query .= ' where ' . $this->key . ' = ?';
                $args[] = &$this->original[$this->key];
                $arg_types .= 'i';
                
                $dbHandler = new MySqlHandler();
                if( ! $dbHandler->dbConnected() ){
                    throw new Exception('DB connection failed');
                }
                $affected_rows = $dbHandler->executePreparedScalarQuery( $query, $args );
                if( $affected_rows === 1 ){
                    
                    $result = true;
                    
                }
                
                $dbHandler->close();
            }else{
                throw new Exception('No changes made');
            }
        
        }catch (Exception $e){
            unset($dbHandler);
            if( $e->getMessage == 'DB connection failed' || $e->getMessage == 'No changes made' ){
                throw new Exception( $e->getMessage() );
            }
        }
        
        return $result;
    }
    
}