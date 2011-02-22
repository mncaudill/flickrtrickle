<?php
    require 'include/init.php';

    $instr = <<<__
        <p>
        If you want to "trickle" your photos into Flickr instead of dumping in dozens of pictures at one time when your contacts will only see at most 5 in their "Photos From" tab, this is your tool.</p>
        <p><strong>Instructions:</strong> Just upload your photos as private and add the tag "flickrtrickle" to them. Then visit this page and I'll pull your 5 oldest (by date posted) trickle photos. Hit the button and I'll update the date posted to the current time, remove the tag, and make the photo public. This way you can trickle in your photos as you see fit.</p>
__;
?>
<!doctype html>
<html>
    <head>
        <title>FlickrTrickle</title>
        <link rel="stylesheet" type="text/css" href="/style.css?v=1"/>
    </head>
    <body>
        <div id="container">
        <h1>FlickrTrickle</h1>
<?php

        if(isset($_GET['success'])) {
            print '<h2>Success!</h2>';
        }

        if(isset($_SESSION['user'])) {
            loadlib('flickr');

            $user = $_SESSION['user'];
            $username = htmlspecialchars($user['username']);

            print "Logged in as <a href='http://www.flickr.com/photos/{$user['nsid']}/'>{$username}</a>. <a href='/logout.php'>Not you?</a><br><br>";


            print '<div id="lefty-loosey">';
            print '<div id="tray"></div>';
            print "<div id=\"image-bank\" style=\"clear:both;\">Loading images...</div>";
            print '</div>';

            print '<div id="righty-tighty">';
            print '<a href="#" id="trickle-it">Trickle!</a>';
            print '<div style="font-size:12px;padding:15px 0;border-top:1px solid black;border-bottom:1px solid black;">';
            print '<input type="radio" name="perm" value="pb"/>Public<br/>';
            print '<input type="radio" name="perm" value="ff"/>Friends & Family<br/>';
            print '<input type="radio" name="perm" value="fr"/>Friends<br/>';
            print '<input type="radio" name="perm" value="fa"/>Family<br/>';
            print '<a id="remove" style="color:red;" href="#">Remove</a>';
            print '</div>';
            print '<p style="font-size:11px;"><strong>Tips: </strong>The timeline as it displays here is how it will show on Flickr. Please feel free to drag and drop images in the tray.</p>';
            print "<p style='clear:both;font-size:small;'>Created by <a href='http://nolancaudill.com'>Nolan Caudill</a></p>";
            print '</div>';
            print '</div>';

        } else {

            loadlib('flickr');
            print "<a href='" . flickr_get_auth_url() . "'>Log In</a>";
            print $instr;
        }
?>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=JS_FILE?>?v=1"></script>

<? include "include/inc_analytics.txt" ?>
    </div>
    </body>
</html>
