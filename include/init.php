<?php
    session_start();

    const FLICKR_API_KEY = "95b7614608b1684d4b5b628c53374100";
    const FLICKR_API_SECRET = "f15ca2e3560f2b6a";

    $cfg = array();

    $cfg['db_host'] = 'localhost';
    $cfg['db_user'] = 'root';
    $cfg['db_pass'] = '';
    $cfg['db_name'] = 'FlickrTrickle';

    function loadlib($lib) {
        $curr_dir = dirname(__FILE__);
        require_once "$curr_dir/lib_$lib.php";
    }
