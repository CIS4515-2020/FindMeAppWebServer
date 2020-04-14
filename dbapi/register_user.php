<?php

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
       //die('post resquest only.');
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Utilities/MySqlHandler.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Utilities/Hash.php';
    
    use dbapi\Utilities\MySqlHandler;
    use dbapi\Utilities\Hash;
    
    $result = ['register' => 'failed', 'error' => ''];
    
    try{
    
        $dbHandler = new MySqlHandler();
        
        if( ! $dbHandler->dbConnected() ){
            $result['error'] = 'Database connection could not be established.';
            throw new Exception('DB connection failed');
        }
        
        $username = $_REQUEST['username'];
        $pass = $_REQUEST['password'];
        $email = $_REQUEST['email'];
        $fname = $_REQUEST['fname'];
        $lname = $_REQUEST['lname'];
        
        $hash = Hash::makePass( $pass );
        
        $query = 'INSERT INTO `user` (username,email,password';
        $values = ' VALUES (?,?,?';
        $arg_types = 'sss';
        $args = [];
        $args[] = &$arg_types;
        $args[] = &$username;
        $args[] = &$email;
        $args[] = &$hash;
        
        if( ! empty($fname) ){
            $query .= ',fname';
            $values .= ',?';
            $arg_types .= 's';
            $args[] = &$fname;
        }
        
        if( ! empty($lname) ){
            $query .= ',lname';
            $values .= ',?';
            $arg_types .= 's';
            $args[] = &$lname;
        }
        
        $query .= ')' . $values . ')';
        
        $affected_rows = $dbHandler->executePreparedScalarQuery( $query, $args );
        if( $affected_rows === 1 ){
            
            $result['register'] = 'success';
            $result['user_id'] = $dbHandler->getLastInsertId();
            
        }else{
            if( $dbHandler->errorno === MySqlHandler::$DUPLICATE_ERROR ){
                if( strpos($dbHandler->mysqliError,'username') !== false ){
                    $result['error'] = 'Username taken.';
                }else if( strpos($dbHandler->mysqliError,'email') !== false ){
                    $result['error'] = 'Email already in use.';
                }
            }else{
                $result['error'] = 'User registration failed.';
            }
            throw new Exception('registration failed');
        }
        
        $dbHandler->close();
    
    }catch( Exception $e ){
        unset($dbHandler);
        $result['exception'] = $e->getMessage();
    }
    
    echo json_encode($result);
    exit;

?>