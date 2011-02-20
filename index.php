<?php
    require 'include/init.php';
    $is_pancakes = (isset($_SESSION['user']) && $_SESSION['user']['nsid'] == "8790317@N03") ? true : false;

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
        <link rel="stylesheet" type="text/css" href="/style.css"/>
<?
    // Something for Pancakes
    if($is_pancakes) {
?>
        <style type="text/css">
            body {
                background-color: pink;
                background-image: url(unicorn.jpg);
                background-position: 50% 0;
                background-repeat: no-repeat;
                color: blue;
                font-family: 'Comic Sans ms';
                font-size: 24px;
                width: 25%;
            }
        </style>
<?
        
    }
?>
    </head>
    <body>
        <div id="container">
        <h1>FlickrTrickle</h1>
<?php

        if(isset($_GET['success'])) {
            print '<h2>Success!</h2>';
        }

        if($is_pancakes) {
            print "<h2>Design inspired by Pancakes</h2>"; } 
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
<script type="text/javascript">
    CURR_PAGE = 1;
    $(document).ready(function(){
        function fetch_page(page_num) {
            $.ajax({
                url: "/images_fragment.php",
                data: "page=" + page_num,
                cache: false,
                success: function(msg){
                    $(image_bank).html(msg);

                    // Fade-out preselected ones
                    for(var i in images_to_post) {
                        if(images_to_post.hasOwnProperty(i)) {
                            $('#image-' + i).addClass('in-tray');
                        }
                    }
                }
            });

        }

        var images_to_post = {};
        var images_to_post_length = 0;

        var image_bank = document.getElementById('image-bank');
        var tray = null;
        var active_image = null;
        var active_image_id = 0;

        if(image_bank) {
            fetch_page(1);
            tray = $('#tray');
            active_image = $('#active-image');
        }

        $('.prev').live('click', function(){
            $(image_bank).html("Loading images...");
            CURR_PAGE--;
            fetch_page(CURR_PAGE);
            return false;
        });

        $('.next').live('click', function(){ 
            $(image_bank).html("Loading images...");
            CURR_PAGE++;
            fetch_page(CURR_PAGE);
            return false;
        });

        function make_image_active(image_id) {
            if(!image_id) return;

            active_image_id = image_id;
            perm = images_to_post[image_id].perm;
            $('input[name="perm"]').filter('[value="' + perm + '"]').click();

            $('#tray img').removeClass('active');
            $('#trayimage-' + active_image_id).addClass('active');
        }

        function enable_form() {
            $('input[name="perm"]').removeAttr('disabled');
            $('#remove').css('visibility', 'visible');
        }

        function disable_form() {
            $('input[name="perm"]').attr('disabled', 'disabled');
            $('#remove').css('visibility', 'hidden');
        }
        disable_form();

        $('#remove').click(function(){
            delete images_to_post[active_image_id];
            images_to_post_length--;
            if(images_to_post_length == 0) {
                disable_form();
            }
            $('#trayimage-' + active_image_id).remove();
            $('#image-' + active_image_id).removeClass('in-tray');

            id = 0;
            for(var i in images_to_post) {
                id = i;    
            }
            make_image_active(id);

            return false;
        });

        $('#tray img').live('click', function() {
            var photo_id = this.id.replace(/^trayimage-/, '');
            make_image_active(photo_id);
            return false;
        });

        $('input[name="perm"]').change(function(){
            images_to_post[active_image_id].perm = $(this).val();
        });

        $('.imagebank-image').live('click', function(){ 
            var photo_id = this.id.replace(/^image-/, '');
            if(images_to_post_length < 5 && !(photo_id in images_to_post)) {
                image_src = $(this).attr('src');
                $(this).addClass('in-tray');
                tray.append('<img id="trayimage-' + photo_id + '" src="' + image_src + '"/>');

                images_to_post[photo_id] = {perm: 'pb', src: image_src.replace(/_s.jpg$/, '_m.jpg')}; 
                enable_form();
                make_image_active(photo_id);
                images_to_post_length++;
            }
            return false;
        });

        $('#trickle-it').click(function(){

            var data = {};
            photo_string = '';
            count = 0;
            $(this).text('Trickling...');

            for(var i in images_to_post) {
                if(images_to_post.hasOwnProperty(i)) {
                    if(count) {
                        photo_string += ',';
                    }
                    count++;
                    photo_string += i;
                    data[i + '-' + images_to_post[i].perm] = 1;
                }
            }
            data.photos = photo_string;

            $.ajax({
                type: 'POST',
                url: "/trickle.php",
                data: data,
                cache: false,
                success: function(){ 
                    window.location = 'http://' + window.location.hostname + '/?success=1'
                }
            });
            return false;
        });

    });
</script>


<? include "include/inc_analytics.txt" ?>
    </div>
    </body>
</html>
