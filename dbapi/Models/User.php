<?php

namespace dbapi\Models;

require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Utilities/MySqlHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Utilities/Hash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/Model.php';
    
use Exception;
use mysqli_stmt;
use dbapi\Utilities\Hash;
use dbapi\Utilities\MySqlHandler;

class User extends Model
{
    protected $table = 'user';
    protected $key = 'user_id';
    
    protected $fillable = [
        'username',
        'email',
        'fname',
        'lname'
    ];
    
    protected $cols = [
        'username' => 's',
        'email' => 's',
        'fname' => 's',
        'lname' => 's'
    ];
    
    function __construct( $attributes = array() ){
        $this->setAttributes( $attributes );
    }
    
    public function setPassword( $pass ){
        $this->attributes['password'] = Hash::makePass( $pass );
    }
    
    public function verifyPassword( $pass ){
        return Hash::verify($pass, $this->attributes['password']);
    }
    
    //get user by username
    //throws exception
    public static function scopeByUsername( $username ){
        
        $user = null;
        
        try{
            $query = "select * from `user` WHERE username = ? limit 1";
            $argTypes = "s";
            $args = [];
            $args[] = &$argTypes;
            $args[] = &$username;
            $row = [];
            $dbHandler = new MySqlHandler();
            if( ! $dbHandler->dbConnected() ){
                throw new Exception('DB connection failed');
            }
            $select = $dbHandler->executePreparedQuery( $query, $args, $row );
            if( $select !== null && $select instanceof mysqli_stmt && $select->num_rows === 1 ){
                $select->fetch();
                $select->close();
                
                $user = new User();
                $user->original = $row;
                $user->attributes = $row;
            }
            $dbHandler->close();
        }catch (Exception $e){
            unset($dbHandler);
            if( $e->getMessage == 'DB connection failed' ){
                throw new Exception( $e->getMessage() );
            }
        }
        
        return $user;
    }
    
}