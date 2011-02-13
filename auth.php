<?php
    require 'include/init.php';

    loadlib('flickr');

    $frob = $_GET['frob'];

    if(!$frob) {
        print "What are you doing here?";
        exit;
    }

    // Give Flickr frob to get auth token
    $args = array(
        'method' => 'flickr.auth.getToken',
        'frob' => $frob,
    );

    $rsp = flickr_api_call($args, true);

    if($rsp['ok'] && $rsp['rsp']['stat']['ok']) {
        $rsp = $rsp['rsp'];

        $nsid = $rsp['auth']['user']['nsid'];
        $username = $rsp['auth']['user']['username'];
        $token = $rsp['auth']['token']['_content'];

        $_SESSION['user'] = array(
            'nsid' => $nsid,
            'username' => $username,
            'token' => $token,
        );

    } else {
        header('Location: /?error=1');
        exit;
    }

    header('Location: /');
    exit;
