<?php
$method = $_SERVER["REQUEST_METHOD"];
include_once "./function.php";
check_process();
$path = "/";
if(isset($_SERVER["PATH_INFO"])) {
    $path = $_SERVER["PATH_INFO"];
}
if(isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] !== "") {
    $path .= "?" . $_SERVER["QUERY_STRING"];
}
$headers = [];
$pre_header = apache_request_headers();
if ($pre_header) {
    foreach ($pre_header as $k => $v) {
        if(strtolower($k) == "accept-encoding") continue;
        if(strtolower($k) == "keep-alive") continue;
        $headers[] = $k . ": " . $v;
    }
}
switch($method) {
    case "GET":
        get($path, $headers);
        break;
    case "POST":
        post($path, implode("\r\n", $headers));
        break;
    case "DELETE":
        delete($path, $headers);
        break;
    case "PATCH":
        patch($path, $headers);
        break;
    case "PUT":
        put($path, $headers);
        break;    
    default:
        header("HTTP/1.1 500 Server Error");
        die("Unsupported request method \"$method\"");
}
?>