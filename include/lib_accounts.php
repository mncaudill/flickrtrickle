<?php
    loadlib('db');

    function accounts_get_user_by_id($id) {
        $query = "SELECT * FROM Users WHERE id=$id";
        $rsp = db_query($query);
        if($rsp['ok']) {
            $user = $rsp['rows'][0];
            return $user;
        }

        return null;
    }

    function accounts_get_user_by_nsid($nsid) {
        $nsid_enc = addslashes($nsid);
        $query = "SELECT * FROM Users WHERE nsid='$nsid_enc'";

        $rsp = db_query($query);
        if($rsp['ok']) {
            return $rsp['rows'][0];
        }

        return null;
    }

    function accounts_create_user($nsid, $username, $token) {
        $nsid_enc = addslashes($nsid);
        $username_enc = addslashes($username);
        $token_enc = addslashes($token);

        $query = "INSERT INTO Users (nsid, username, token) VALUES ('$nsid_enc', '$username_enc', '$token_enc')";
        $query .= " ON DUPLICATE KEY UPDATE username='$username_enc', token='$token_enc'";

        return db_insert($query);
    }
