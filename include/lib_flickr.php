<?php

    function flickr_api_call($args, $sign = false) {
        $args['api_key'] = FLICKR_API_KEY;
        $args['format'] = 'json';
        $args['nojsoncallback'] = 1;

        if($sign) {
            $args['api_sig'] = _flickr_sign($args);
        }

        $url = "http://api.flickr.com/services/rest/?";
        $url .= http_build_query($args);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $rsp = curl_exec($ch);

        if(!curl_errno($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($http_code == 200) {
                $rsp = array('ok' => 1, 'rsp' => json_decode($rsp, true));
            } 
        }

        if(!$rsp) {
            $rsp = array('ok' => 0, 'message' => 'Connection problems');
        }

        curl_close($ch);

        return $rsp;
    }

    function flickr_get_trickle_photos($user, $num = 5) {
        $args = array(
            'method' => 'flickr.photos.search',
            'user_id' => $user['nsid'],
            'auth_token' => $user['token'],
            'tags' => 'flickrtrickle',
            'privacy_filter' => 5,
            'sort' => 'date-posted-asc', // Grabbing the oldest
            'per_page' => $num,
            'extras' => 'url_t,tags',
        );
        $rsp = flickr_api_call($args, true);
        return $rsp;
    }

    function flickr_trickle_photo($user, $photo_id) {
        $args = array(
            'auth_token' => $user['token'],
        );
    }

    function _flickr_sign($args) {
        ksort($args);

        $secret_string = FLICKR_API_SECRET;
        foreach($args as $k => $v) {
            $secret_string .= $k . $v;
        }

        return md5($secret_string);
    }

    function flickr_get_auth_url() {
        $args = array(
            'api_key' => FLICKR_API_KEY,
            'perms' => 'write',
        );

        $api_sig = _flickr_sign($args);
        $key = FLICKR_API_KEY;
        return "http://flickr.com/services/auth/?api_key={$key}&perms=write&api_sig=$api_sig";
    }

