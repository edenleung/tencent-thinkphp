<?php

define('TEXT_REG', '#\.html.*|\.js.*|\.css.*|\.html.*#');
define('BINARY_REG', '#\.gif.*|\.jpg.*|\.png.*|\.jepg.*|\.swf.*|\.bmp.*|\.ico.*#');

/**
 * handler static files
 */
function handlerStatic($path)
{
    $filename = __DIR__ . "/public/" . $path;
    $handle   = fopen($filename, "r");

    $contents = '';
    if(filesize($filename) > 0) {
        $contents = fread($handle, filesize($filename));
    }

    fclose($handle);

    $headers = [];

    switch(pathinfo($filename, PATHINFO_EXTENSION)) {
        case 'css':
            $headers['Content-Type'] = 'text/css';
            break;
        case 'svg':
            $headers['Content-Type'] = 'image/svg+xml';
            break;
        default:
            $headers['Content-Type'] = mime_content_type($filename);
            break;
    }

    if (substr($headers['Content-Type'], 0, 4) === 'text') {
        $base64Encode = false;
        $body = $contents;
    } else {
        $base64Encode = true;
        $body = base64_encode($contents);
    }

    return [
        "isBase64Encoded" => $base64Encode,
        "statusCode" => 200,
        "headers" => $headers,
        "body" => $body,
    ];
}

function handler($event, $context)
{
    require __DIR__ . '/vendor/autoload.php';

    // init path
    $path = '/' == $event->path ? '' : ltrim($event->path, '/');
    echo "+++++++".$path;

    $filename = __DIR__ . "/public/" . $path;
    if (file_exists($filename) && (preg_match(TEXT_REG, $path) || preg_match(BINARY_REG, $path))) {
        return handlerStatic($path);
    }

    $headers = $event->headers ?? [];
    $headers = json_decode(json_encode($headers), true);

    $_GET = $event->queryString ?? [];
    $_GET = json_decode(json_encode($_GET), true);

    if(!empty($event->body)) {
        $_POSTbody = explode("&", $event->body);
        foreach ($_POSTbody as $postvalues) {
            $tmp=explode("=", $postvalues);
            $_POST[$tmp[0]] = $tmp[1];
        }
    }

    $app = new \think\App();
    // set runtime path to in /tmp, because only /tmp is writable for cloud
    $app->setRuntimePath('/tmp/runtime' . DIRECTORY_SEPARATOR);

    $http = $app->http;

    // make request
    $request = $app->make('request', [], true);
    $request->setPathinfo($path);
    $request->setMethod($event->httpMethod);
    $request->withHeader($headers);

    // get response
    $response = $http->run($request);
    $http->end($response);

    $body = $response->getContent();
    $contentType = $response->getHeader('Content-Type');

    return [
        'isBase64Encoded' => false,
        'statusCode' => $response->getCode(),
        'headers' => [
            'Content-Type' => $contentType
        ],
        'body' => $body
    ];
}
