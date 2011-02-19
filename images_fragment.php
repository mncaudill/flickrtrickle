<?php
    require 'include/init.php';

    if(!isset($_SESSION['user'])) exit;

    loadlib('flickr');

    $user = $_SESSION['user'];

    $clean_page = intval($_GET['page']);
    $page_num = $clean_page ? $clean_page : 1;

    // Show next trickle photos
    const FETCHED_PHOTOS = 20;

    $rsp = flickr_get_trickle_photos($user, FETCHED_PHOTOS, $page_num);

    if($rsp['ok']) {
        $photos = $rsp['rsp']['photos']['photo'];
        if($photos) {
            $show_previous_link = $page_num > 1;

            $show_next_link = false;
            $photos_seen = ($page_num - 1) * FETCHED_PHOTOS + count($photos);
            if($photos_seen < $rsp['rsp']['photos']['total']) {
                $show_next_link = true;
            }

            // Split photos into groups of 5
            $rows = array_chunk($photos, 5);

            print "<table id=\"photos\">";
            foreach($rows as $row) {
                print "<tr>";

                $count = 0;
                foreach($row as $photo) {
                    $url = "http://www.flickr.com/photos/{$photo['owner']}/{$photo['id']}/";
                    print "<td><img class=\"imagebank-image\" id='image-{$photo['id']}' width='{$photo['width_sq']}' height='{$photo['height_sq']}' src='{$photo['url_sq']}'/></td>";

                    $count++;
                }

                while($count < 5) {
                    print "<td></td>";
                    $count++;
                }

                print "</tr>";
            }
            print "</table>";

            if($show_previous_link) {
                print "<a style=\"display:block;float:left;\" class=\"prev\" href=\"#\">&lt;&lt;&lt;Previous</a>";
            }

            if($show_next_link) {
                print "<a style=\"display:block;float:right;\" class=\"next\" href=\"#\">Next&gt;&gt;&gt;</a>";
            }

        } else {
            print "<strong>No FlickrTrickle photos were found. To add them, add the tag \"<em>flickrtrickle</em>\" to your private photos.</strong>";
        }
    } else {
        print $rsp['message']; 
    }
