<?php

    // Assumes properly escaped strings
    function db_connect() {
        global $cfg;

        $dbh = mysql_connect($cfg['db_host'], $cfg['db_user'], $cfg['db_pass']);

        if(!$dbh) {
            die("Couldn't connect to DB: " . mysql_error());
        }

        mysql_select_db($cfg['db_name']);
        mysql_set_charset('utf8', $dbh);
        return $dbh;
    }

    function db_query($query) {
        $db = db_connect();    

        $results = array();
        if ($result = mysql_query($query, $db)) {
            while($row = mysql_fetch_assoc($result)) {
                $results[] = $row;
            }
            return array('ok' => 1,
                        'rows' => $results
                    );
        } else {
            db_error();
        }
    }

    function db_insert($query) {
        $db = db_connect();
        if(mysql_query($query, $db)) {
            return array(
                'ok' => 1,
            );
        } else {
            db_error();   
        }
    }

    function db_last_insert_id($query) {
        $db = db_connect();
        return mysql_insert_id($db);
    }

    function db_error() {
        print mysql_error();
        exit;
    }

    
