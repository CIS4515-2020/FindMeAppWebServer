<?php

    $page = ( isset($_GET["page"]) ? strtolower($_GET["page"]) : "" );
    $subpage = ( isset($_GET["subpage"]) ? $_GET["subpage"] : "" );
    $subpage2 = ( isset($_GET["subpage2"]) ? $_GET["subpage2"] : "" );

    switch( $page ){
        case 'login': include_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/login.php';
                    break;
        case 'register-user': include_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/register_user.php';
                    break;
        case 'item': include_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/item.php';
                    break;
        case 'found-item-message': include_once $_SERVER['DOCUMENT_ROOT'] . '/dbapi/found_item.php';
                    break;
        case 'found-item': include_once $_SERVER['DOCUMENT_ROOT'] . '/found_item.php';
                    break;
        case '': include_once 'index.html';
                    break;
        default: include_once '404.php'; break;
    }

?>


