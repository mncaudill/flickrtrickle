<?php
    require 'include/init.php';
?>
<!doctype html>
<html>
    <head>
        <title>FlickrTrickle</title>
    </head>
    <body>
        <h1>FlickrTrickle</h1>
<?php
        if(isset($_SESSION['user'])) {
            loadlib('flickr');

            $user = $_SESSION['user'];

            print "Logged in as <a href='http://www.flickr.com/photos/{$user['nsid']}/'>{$user['username']}</a>. <a href='/logout.php'>Not you?</a>";
            print "<br><br>";

            // Show next trickle photos
            $rsp = flickr_get_trickle_photos($user);
            if($rsp['ok']) {
                $rsp = $rsp['rsp'];
                if($rsp['stat'] == 'ok') {
                    $photos = $rsp['photos']['photo'];
                    if($photos) {
                        print "<form method='post' action='/trickle'>";
                        print "<table>";
                        foreach($photos as $photo) {
                            $url = "http://www.flickr.com/photos/{$photo['owner']}/{$photo['id']}/";
                            $title = htmlspecialchars($photo['title'], ENT_COMPAT, 'utf-8');

                            print "<tr>";
                            print "<td><input checked type='checkbox' name='photos[]' value='{$photo['id']}'/></td>";
                            print "<td><a href='$url'>$title</a></td>";
                            print "<td><a href='$url'><img width='{$photo['width_t']}' height='{$photo['height_t']}' src='{$photo['url_t']}'/></a></td>";
                            print "</tr>";
                        }
                        print "</table>";
                        print "<input type='submit' name='Trickle!'/>";
                        print "</form>";
                    } else {
                        print "No FlickrTrickle photos were found. To add them, add the tag 'flickrtrickle' to your private photos.";
                    }
                } else {
                    print $rsp['message']; 
                }
            } else {
                print "Having API difficulties at the moment...<br>";
            }
        } else {
            loadlib('flickr');
            print "<a href='" . flickr_get_auth_url() . "'>Log In</a>";
        }
?>
    </body>
</html>
