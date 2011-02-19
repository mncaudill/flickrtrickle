<?php
    session_start();

    $cfg = array();

    $cfg['db_host'] = 'localhost';
    $cfg['db_user'] = 'root';
    $cfg['db_pass'] = '';
    $cfg['db_name'] = 'FlickrTrickle';

    function loadlib($lib) {
        $curr_dir = dirname(__FILE__);
        require_once "$curr_dir/lib_$lib.php";
    }
    
    // Determine env
    $hostname = $_SERVER['HTTP_HOST'];
    switch($hostname) {
        case 'flickrtrickle.localhost':
            include 'dev_config.php';
            break;
        default:
            include 'prod_config.php';
            break;
    }
