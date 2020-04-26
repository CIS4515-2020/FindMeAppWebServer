<?php

namespace dbapi\Utilities;

require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/Item.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/Models/FoundItemMessage.php';

use dbapi\Models\Item;
use dbapi\Models\FoundItemMessage;

class Firebase
{
    const URL = 'https://fcm.googleapis.com/fcm/send';
    const ACCESS_KEY = 'AAAAYRQMJVE:APA91bHEgM7dY5RJUWXDiu-8xzxFNYsmsloGC1GDxRqtZ0BPcDuS8u30vrfGAqevNE3vE6o2i_JCTl_o3icJXMJWYt8TXbdE53Ws-Y7phPbT68ks3VhLBciIc5Pyel-sYT8H7sPlKeu5';

    //send notification to user devices
    //make POST resquest to firebase cloud messaging REST api
    public static function sendCloudMessage( Item $item, FoundItemMessage $found_item, $verbose = false ){

        $topic = "/topics/" . $item->getUserId();
    
        $ch = curl_init();
        
        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization:key=' . self::ACCESS_KEY;
        
        //set default message and truncate if necessary
        $message = $found_item->getMessage();
        if( empty($message) ){
            $message = 'Click to open app';
        }
        if( strlen($message) > 64 ){
            $message = substr($message, 0, 64) . '...';
        }
        
        //populate data to send
        $data = [
          'to' => $topic,
          'data' => ['found_item_id' => $found_item->getId(),
            'item_id' => $item->getId(),
            'user_id' => $item->getUserId(),
            'title' => 'Item found: ' . $item->getName(),
            'message' => $message]
        ];
        $payload = json_encode($data);
        
        curl_setopt($ch, CURLOPT_URL, self::URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        //setup logging network communication if verbose is true
        if( $verbose ){
            $fverbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $fverbose);
            echo "<pre>Payload:\n" . $payload . '</pre><br>';
        }
        
        $result = curl_exec($ch);
        
        //check if call to api was successful
        if ($result === FALSE || strpos($result,"error") !== FALSE || curl_getinfo($ch, CURLINFO_RESPONSE_CODE) !== 200 ) {
            
            if( $verbose ){
                echo '<pre>' . $result . '</pre>';
                printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                   htmlspecialchars(curl_error($ch)));
            }
            
            $result = false;
        }
        
        //print curl network communication data if verbose is true
        if( $verbose ){
            rewind($fverbose);
            $verboseLog = stream_get_contents($fverbose);
            echo '<pre>' . $result . '</pre>';
            echo "<br><pre>Verbose information:\n", htmlspecialchars($verboseLog), "\n</pre>";
        }
        
        curl_close($ch);
        return $result;
    }
}

?>