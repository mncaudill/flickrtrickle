<?php
    require 'include/init.php';
    $instr = <<<__
        <p>
        If you want to "trickle" your photos into Flickr instead of dumping in dozens of pictures at one time when your contacts will only see at most 5 in their "Photos From" tab, this is your tool.</p>
        <p>Just upload your photos as private and add the tag "flickrtrickle" to them. Then visit this page and I'll pull your 5 oldest (by date posted) trickle photos. Hit the button and I'll update the date posted to the current time, remove the tag, and make the photo public. This way you can trickle in your photos as you see fit.</p>
__;
?>
<!doctype html>
<html>
    <head>
        <title>FlickrTrickle</title>
        <link rel="stylesheet" type="text/css" href="/style.css"/>
    </head>
    <body>
        <h1>FlickrTrickle</h1>
<?php
        if(isset($_SESSION['user'])) {
            loadlib('flickr');

            $user = $_SESSION['user'];

            print "Logged in as <a href='http://www.flickr.com/photos/{$user['nsid']}/'>{$user['username']}</a>. <a href='/logout.php'>Not you?</a>";
            print $instr;

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
                            $title = $title ? $title : "Untitled";


                            print "<tr>";
                            print "<td><input checked type='checkbox' name='photos[]' value='{$photo['id']}'/></td>";
                            print "<td><a href='$url'>$title</a></td>";
                            print "<td><a href='$url'><img style='margin:10px;' width='{$photo['width_t']}' height='{$photo['height_t']}' src='{$photo['url_t']}'/></a></td>";
                            print "</tr>";
                        }
                        print "</table>";
                        print "<input type='submit' name='Trickle!' value='Trickle!'/>";
                        print "</form>";
                    } else {
                        print "<strong>No FlickrTrickle photos were found. To add them, add the tag '<em>flickrtrickle</em>' to your private photos.</strong>";
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
            print $instr;
        }
?>
    <p style="font-size:small;">Created by <a href="http://nolancaudill.com">Nolan Caudill</a></p>
    </body>
</html>
