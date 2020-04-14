<?php

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
       //die('post resquest only.');
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/Item.php';
    
    use dbapi\Utilities\MySqlHandler;
    use dbapi\Models\Item;
    
    $action = $subpage;
    
    switch( $action ){
        case 'add': addItem(); break;
        case 'list': listItems(); break;
        case 'edit': editItem(); break;
        case 'delete': deleteItem(); break;
        default: header("Location: /404.html");
    }
    
    //handle add item request
    function addItem(){
        $result = ['result' => 'failed', 'error' => ''];
        
        $user_id = (isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null);
        $name = (isset($_REQUEST['name']) ? $_REQUEST['name'] : null);
        $description = (isset($_REQUEST['description']) ? $_REQUEST['description'] : null);
        
        $attr = [
            'user_id' => $user_id,
            'name' => $name,
            'description' => $description
        ];
        
        try{
            $item = new Item( $attr );
            if( $item->insert() ){
                $result['result'] = 'success';
            }
        }catch (Exception $e){
            $result['exception'] = $e->getMessage();
        }
        
        echo json_encode($result);
    }
    
    //handle list items request
    function listItems(){
        $user_id = (isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null);
        
        try{
            
            if( is_null($user_id) || empty($user_id) ){
                throw new Exception('Item id missing.');
            }
            
            $items = Item::scopeByUser($user_id);
            $result['item'] = $items;
            $result['result'] = 'success';
        }catch (Exception $e){
            $result['exception'] = $e->getMessage();
        }
        
        echo json_encode($result);
    }
    
    //handle edit item request
    function editItem(){
        $result = ['result' => 'failed', 'error' => ''];
        
        $item_id = (isset($_REQUEST['item_id']) ? $_REQUEST['item_id'] : null);
        $name = (isset($_REQUEST['name']) ? $_REQUEST['name'] : null);
        $description = (isset($_REQUEST['description']) ? $_REQUEST['description'] : null);
        $lost = (isset($_REQUEST['lost']) ? $_REQUEST['lost'] : null);
        
        try{
            $item = Item::find( $item_id );
            if( is_null($item) ){
                $result['error'] = 'Item not found.';
                throw new Exception('Item not found.');
            }
            
            if( ! empty($name) ){
                $item->setName( $name );
            }
            if( ! is_null($description) ){
                $item->setDescription( $description );
            }
            if( !is_null($lost) && !empty($lost) ){
                $item->setLost($lost);
            }
            if( $item->isDirty() ){
                if( $item->save() ){
                    $result['result'] = 'success';
                }else{
                    $result['error'] = 'Could not save changes.';
                }
            }else{
                $result['error'] = 'No changes made.';
            }
        }catch (Exception $e){
            $result['exception'] = $e->getMessage();
        }
        
        echo json_encode($result);
    }
    
    //handle delete item request
    function deleteItem(){
        $result = ['result' => 'failed', 'error' => ''];
        
        $item_id = (isset($_REQUEST['item_id']) ? $_REQUEST['item_id'] : null);
        
        try{
            
            if( is_null($item_id) || empty($item_id) ){
                $result['error'] = 'Item not found.';
                throw new Exception('Item id not provided.');
            }
            
            if( Item::delete($item_id) ){
                $result['result'] = 'success';
            }
        }catch (Exception $e){
            $result['exception'] = $e->getMessage();
        }
        
        echo json_encode($result);
    }

?>