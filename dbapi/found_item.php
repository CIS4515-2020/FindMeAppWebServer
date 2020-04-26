<?php

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
       //die('post resquest only.');
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/Item.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/FoundItemMessage.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Utilities/Firebase.php';
    
    use dbapi\Utilities\FireBase;
    use dbapi\Models\Item;
    use dbapi\Models\FoundItemMessage;
    
    $action = $subpage;
    
    switch( $action ){
        case 'add': addItem(); break;
        case 'list': listItems(); break;
        case 'edit': editItem(); break;
        case 'delete': deleteItem(); break;
        default: header("Location: /404.php");
    }
    
    //handle add item request
    function addItem(){
        $result = ['result' => 'failed', 'error' => ''];
        
        $item_id = (isset($_REQUEST['item_id']) ? $_REQUEST['item_id'] : null);
        $lat = (isset($_REQUEST['lat']) ? $_REQUEST['lat'] : null);
        $lng = (isset($_REQUEST['lng']) ? $_REQUEST['lng'] : null);
        $message = (isset($_REQUEST['message']) ? $_REQUEST['message'] : '');
        
        $attr = [
            'item_id' => $item_id,
            'lat' => $lat,
            'lng' => $lng,
            'message' => $message
        ];
        
        try{
            
            if( is_null($item_id) || empty($item_id) ){
                throw new Exception('Item id not provided.');
            }
            
            if( (empty($lat) || empty($lng)) && empty($message) ){
                throw new Exception('Missing required attributes.');
            }
            
            
            $fitem = new FoundItemMessage( $attr );
            if( $fitem->insert() ){
                $result['result'] = 'success';
                $result['data'] = [$fitem];
                $item = Item::find($item_id);
                FireBase::sendCloudMessage( $item, $fitem );
            }
        }catch (Exception $e){
            $result['exception'] = $e->getMessage();
        }
        
        echo json_encode($result);
    }
    
    //handle list found item messages
    function listItems(){
        $item_id = (isset($_REQUEST['item_id']) ? $_REQUEST['item_id'] : null);
        
        try{
            
            if( is_null($item_id) || empty($item_id) ){
                throw new Exception('Item id missing.');
            }
            
            $item_messages = FoundItemMessage::scopeByItem($item_id);
            $result['data'] = $item_messages;
            $result['result'] = 'success';
        }catch (Exception $e){
            $result['exception'] = $e->getMessage();
        }
        
        echo json_encode($result);
    }
    
    //handle edit item request
    function editItem(){
        $result = ['result' => 'failed', 'error' => ''];
        
        //no edit functionality currently
        echo json_encode($result);
    }
    
    //handle delete item request
    function deleteItem(){
        $result = ['result' => 'failed', 'error' => ''];
        
        $found_item_id = (isset($_REQUEST['found_item_id']) ? $_REQUEST['found_item_id'] : null);
        
        try{
            
            if( is_null($found_item_id) || empty($found_item_id) ){
                throw new Exception('Found item message id not provided.');
            }
            
            if( FoundItemMessage::delete($found_item_id) ){
                $result['result'] = 'success';
            }
        }catch (Exception $e){
            $result['exception'] = $e->getMessage();
        }
        
        echo json_encode($result);
    }

?>