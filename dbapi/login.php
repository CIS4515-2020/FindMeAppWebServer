<?php

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
       //die('post resquest only.');
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/User.php';
    
    use dbapi\Utilities\MySqlHandler;
    use dbapi\Models\User;
    
    $result = ['result' => 'failed', 'error' => ''];
    
    try{
    
        $user = $_REQUEST['username'];
        $pass = $_REQUEST['password'];
        
        $user = User::scopeByUsername( $user );
        if( !is_null($user) ){
            
            if( $user->verifyPassword($pass) ){
                $result['result'] = 'success';
                $result['data'] = [$user];
            }else{
                $result['error'] = 'Invalid credentials.';
            }
            
        }else{
            $result['error'] = 'Invalid credentials.';
            throw new Exception('Login failed');
        }
    
    }catch( Exception $e ){
        $result['exception'] = $e->getMessage();
    }
    
    echo json_encode($result);
    exit;

?>