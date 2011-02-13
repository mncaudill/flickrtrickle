<?php
    require 'include/init.php';
?>
<!doctype html>
<html>
    <head>
        <title>FlickrTrickle</title>
    </head>
    <body>
        <h1>FlickTrickle</h1>
<?php
        if(isset($_SESSION['user'])) {
            loadlib('flickr');

            $user = $_SESSION['user'];

            print "Logged in as <a href='http://www.flickr.com/photos/{$user['nsid']}/'>{$user['username']}</a>. <a href='/logout.php'>Not you?</a>";

            // Show next trickle photos
            $rsp = flickr_get_trickle_photos($user);
            if($rsp['ok']) {
                $rsp = $rsp['rsp'];
                if($rsp['stat'] == 'ok') {
                    $photos = $rsp['photos']['photo'];
                    print "<form method='post' action='/trickle'>";
                    print "<table>";
                    foreach($photos as $photo) {
                        $url = "http://www.flickr.com/photos/{$photo['owner']}/{$photo['id']}/";

                        print "<tr>";
                        print "<td><input checked type='checkbox' name='photos[]' value='{$photo['id']}'/></td>";
                        print "<td><a href='$url'>{$photo['title']}</a></td>";
                        print "<td><a href='$url'><img width='{$photo['width_s']}' height='{$photo['height_s']}' src='{$photo['url_s']}'/></a></td>";
                        print "</tr>";
                    }
                    print "</table>";
                    print "<input type='submit' name='Trickle!'/>";
                    print "</form>";
                } else {
                    print $rsp['message']; 
                }
            } else {
                print "Having API difficulties at the moment...<br>";
            }
        } else {
            loadlib('flickr');
            $auth_url = flickr_get_auth_url();
            print "<a href='" . $auth_url . "'>Log In</a>";
        }
?>
    </body>
</html>
