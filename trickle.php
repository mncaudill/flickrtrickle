<!doctype html>
<html>
    <head>
        <title>FlickrTrickle</title>
        <link rel="stylesheet" type="text/css" href="/style.css"/>
    </head>
    <body>
        <h1>Step 2 of the Trickle Experience</h1>
<?php

    require 'include/init.php';

    $time = time();

    if(isset($_SESSION['user'])) {
        $token = $_SESSION['user']['token'];
        $photos = explode(',', $_POST['photos']);
        foreach($photos as $photo_id) {
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
                    'date_posted' => $time++,
                );
                flickr_api_call($args, true);

                // Remove tag
                $args = array(
                    'method' => 'flickr.photos.removeTag',
                    'tag_id' => $tag_id,
                    'auth_token' => $token,
                );
                flickr_api_call($args, true);

                // Make public
                $is_public = $_POST[$photo_id . '-pb'] ? 1 : 0;
                $is_family = ($_POST[$photo_id . '-fa'] || $_POST[$photo_id . '-frfa']) ? 1 : 0;
                $is_friend = ($_POST[$photo_id . '-fr'] || $_POST[$photo_id . '-frfa']) ? 1 : 0;
                $args = array(
                    'method' => 'flickr.photos.setPerms',
                    'photo_id' => $photo_id,
                    'is_public' => $is_public,
                    'is_friend' => $is_friend,
                    'is_family' => $is_family,
                    'auth_token' => $token,
                );
                flickr_api_call($args, true);

                $title = htmlspecialchars($photo['title']['_content']);
                $title = $title ? $title : "Untitled";
                $link = "<a href='http://www.flickr.com/photos/{$_SESSION['user']['nsid']}/$photo_id/'>$title</a>";
                print "Trickled photo $link<br>";
            }
        }
    } else {
        print "Nothing here for you!<br/><br/>";
    }

?>
        <br/><a href="/">Back to home</a>
<? include "include/inc_analytics.txt"; ?>
    </body>
</html>
