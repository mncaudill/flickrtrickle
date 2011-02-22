<?php

    require 'include/init.php';

    $time = time();

    if(isset($_SESSION['user'])) {
        $token = $_SESSION['user']['token'];
        $photos = explode(',', $_POST['ids']);
        $perms = explode(',' , $_POST['perms']);
        $count = 0;
        foreach($photos as $photo_id) {
            $perm = $perms[$count];
            loadlib('flickr');
            $args = array(
                'method' => 'flickr.photos.getInfo',
                'photo_id' => $photo_id,
                'auth_token' => $token,
            );
            $rsp = flickr_api_call($args, true);
            if($rsp['ok'] && $rsp['rsp']['stat'] == 'ok') {
                $photo = $rsp['rsp']['photo'];
                $tags = $photo['tags']['tag'];

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
                    'auth_token' => $token,
                    'photo_id' => $photo_id,
                    'date_posted' => $time,
                );
                flickr_api_call($args, true);
                $time += 2;

                // Remove tag
                $args = array(
                    'method' => 'flickr.photos.removeTag',
                    'tag_id' => $tag_id,
                    'auth_token' => $token,
                );
                flickr_api_call($args, true);

                // Make public
                $is_public = $perm == 'pb' ? 1 : 0;
                $is_family = ($perm == 'ff' || $perm == 'fa') ? 1 : 0;
                $is_friend = ($perm == 'ff' || $perm == 'fr') ? 1 : 0;
                $args = array(
                    'method' => 'flickr.photos.setPerms',
                    'photo_id' => $photo_id,
                    'is_public' => $is_public,
                    'is_friend' => $is_friend,
                    'is_family' => $is_family,
                    'auth_token' => $token,
                );
                flickr_api_call($args, true);
            }

            $count++;
        }

        print json_encode(array('ok' => 1, 'msg' => 'success'));
    } else {
        print "Nothing here for you!<br/><br/>";
    }
