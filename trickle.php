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
        foreach($_POST['photos'] as $photo_id) {
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
                $args = array(
                    'method' => 'flickr.photos.setPerms',
                    'photo_id' => $photo_id,
                    'is_public' => 1,
                    'is_friend' => 0,
                    'is_family' => 0,
                    'auth_token' => $token,
                );
                flickr_api_call($args, true);

                $title = htmlspecialchars($photo['title']['_content'], ENT_COMPAT, 'utf-8');
                $title = $title ? $title : "Untitled";
                $link = "<a href='http://www.flickr.com/photos/{$_SESSION['user']['nsid']}/$photo_id/'>$title</a>";
                print "Trickled photo $link<br>";
            }
        }
    } else {
        print "Nothing here for you!<br/><br/>";
    }

?>
        <a href="/">Back to home</a>
    </body>
</html
