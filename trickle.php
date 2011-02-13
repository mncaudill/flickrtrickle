<?php

    require 'include/init.php';
    print '<pre>';

    $time = time();

    if(isset($_SESSION['user'])) {
        foreach($_POST['photos'] as $photo) {
            loadlib('flickr');
            $args = array(
                'method' => 'flickr.photos.getInfo',
                'photo_id' => $photo,
                'auth_token' => $_SESSION['user']['token'],
            );
            $rsp = flickr_api_call($args, true);
            if($rsp['ok'] && $rsp['rsp']['stat'] == 'ok') {
                $rsp = $rsp['rsp'];
                $tags = $rsp['photo']['tags']['tag'];

                $tag_id = false;
                foreach($tags as $tag) {
                    if($tag['_content'] == 'flickrtrickle') {
                        $tag_id = $tag['id'];
                        break;
                    }
                }

                // Update date
                $args = array(
                    'method' => 'flickr.photos.setDates',
                    'auth_token' => $_SESSION['user']['token'],
                    'photo_id' => $photo,
                    'date_posted' => $time++,
                );
                flickr_api_call($args, true);

                // Remove tag
                $args = array(
                    'method' => 'flickr.photos.removeTag',
                    'tag_id' => $tag_id,
                    'auth_token' => $_SESSION['user']['token'],
                );
                flickr_api_call($args, true);

                // Make public
                $args = array(
                    'method' => 'flickr.photos.setPerms',
                    'photo_id' => $photo,
                    'is_public' => 1,
                    'is_friend' => 0,
                    'is_family' => 0,
                    'auth_token' => $_SESSION['user']['token'],
                );
                $rsp = flickr_api_call($args, true);
                print_r($rsp);
                print "Made it public.<br/>";
            }
        }
    }
