<?php
function start_process() {
    exec("killall cloudreve");
    exec("./cloudreve  > ./cloudreve.log &");
    while(!@file_get_contents("http://127.0.0.1:5212")) {
        sleep(1);
    }
}
function check_process() {
    if (!file_exists("./last_check")) {
        file_put_contents("./last_check", time());
        start_process();
        return;
    }
    $t = intval(file_get_contents("./last_check"));
    if (time() - $t > 10) {
        $c = @file_get_contents("http://127.0.0.1:5212");
        if (!$c) {
            start_process();
        }
        file_put_contents("./last_check", time());
    }
}
function get($path, $headers = []) {
    $curl = curl_init("http://127.0.0.1:5212".$path);
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_HEADERFUNCTION => function($ch, $ctx) {
            $h = str_replace(["\r", "\n"], "", $ctx);
            if (strlen($h) > 0 && stripos($h, "transfer-encoding") === false) {
                header($h);
            }
            return strlen($ctx);
        },
        CURLOPT_WRITEFUNCTION => function($ch, $data) {
            echo $data;
            ob_flush();
            flush();
            return strlen($data);
        }
    ]);
    $resp = curl_exec($curl);
    if (!$resp) {
        header("HTTP/1.1 500 Server Error");
        echo "Server error.";
    }
    die();
}
function post($path, $headers) {
    $fp = fsockopen("127.0.0.1", 5212, $errno, $errstr, 1);
    $input = fopen("php://input", "r");
    if (!$fp) {
        return false;
    } else {
        fwrite($fp, "POST $path HTTP/1.1\r\n$headers\r\nKeep-Alive: closed\r\n\r\n");
        @stream_copy_to_stream($input, $fp);
        $len = 0;
        $header = "";
        while (true) {
            $ctx = stream_get_line($fp, 1024, "\r\n");
            $h = str_replace(["\r", "\n"], "", $ctx);
            if (strlen($h) > 0 && stripos($h, "transfer-encoding") === false) {
                header($h);
                if (stripos($h, "Content-Length") !== false) {
                    $len = intval(trim(explode(":", $h)[1]));
                }
            }
            if ($h === "") break;
        }
        echo fgets($fp, $len);
        die();
    }
}
function delete($path, $headers = []) {
    $curl = curl_init("http://127.0.0.1:5212".$path);
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => file_get_contents("php://input"),
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_HEADERFUNCTION => function($ch, $ctx) {
            $h = str_replace(["\r", "\n"], "", $ctx);
            if (strlen($h) > 0 && stripos($h, "transfer-encoding") === false) {
                header($h);
            }
            return strlen($ctx);
        }
    ]);
    $resp = curl_exec($curl);
    if (!$resp) {
        header("HTTP/1.1 500 Server Error");
        echo "Server error.";
    }
    die();
}
function patch($path, $headers = []) {
    $curl = curl_init("http://127.0.0.1:5212".$path);
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => file_get_contents("php://input"),
        CURLOPT_CUSTOMREQUEST => "PATCH",
        CURLOPT_HEADERFUNCTION => function($ch, $ctx) {
            $h = str_replace(["\r", "\n"], "", $ctx);
            if (strlen($h) > 0 && stripos($h, "transfer-encoding") === false) {
                header($h);
            }
            return strlen($ctx);
        }
    ]);
    $resp = curl_exec($curl);
    if (!$resp) {
        header("HTTP/1.1 500 Server Error");
        echo "Server error.";
    }
    die();
}
function put($path, $headers = []) {
    $curl = curl_init("http://127.0.0.1:5212".$path);
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => file_get_contents("php://input"),
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_HEADERFUNCTION => function($ch, $ctx) {
            $h = str_replace(["\r", "\n"], "", $ctx);
            if (strlen($h) > 0 && stripos($h, "transfer-encoding") === false) {
                header($h);
            }
            return strlen($ctx);
        }
    ]);
    $resp = curl_exec($curl);
    if (!$resp) {
        header("HTTP/1.1 500 Server Error");
        echo "Server error.";
    }
    die();
}