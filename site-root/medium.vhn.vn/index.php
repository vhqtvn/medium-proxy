<?php

require __DIR__ . "/vendor/autoload.php";

use GuzzleHttp\Psr7;
use Laminas\Diactoros\ServerRequestFactory;
use Proxy\Proxy;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Proxy\Filter\RemoveEncodingFilter;
use Psr\Http\Message\RequestInterface;

// Create a PSR7 request based on the current browser request.
$request = ServerRequestFactory::fromGlobals();

// Create a guzzle client
$guzzle = new GuzzleHttp\Client();

// Create the proxy instance
$proxy = new Proxy(new class ($guzzle) extends GuzzleAdapter
{
    /**
     * @inheritdoc
     */
    public function send(RequestInterface $request)
    {
        return $this->client->send($request, ['allow_redirects' => false]);
    }
});
$proxy->filter(new RemoveEncodingFilter());

$host = "medium.com";
if (preg_match('@([0-9a-z-]+)-md.vhn.vn@', $_SERVER['HTTP_HOST'], $m)) {
    $host = $m[1] . ".medium.com";
}
// Forward the request and get the response.
$response = $proxy->forward($request)->to('https://' . $host);

$response = new Psr7\Response(
    $response->getStatusCode(),
    replace(['Access-Control-Allow-Origin' => '*'] + $response->getHeaders()),
    Psr7\stream_for(replace($response->getBody()->getContents()))
);

// Output response to the browser.
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);

function replace($body)
{
    if (is_array($body)) {
        $r = [];
        foreach ($body as $k => $v) $r[$k] = replace($v);
        return $r;
    }
    $body = preg_replace("@([0-9a-z-]+)\.medium\.com@ism", '$1-md.vhn.vn', $body);
    $body = preg_replace("@(https://)medium\.com@ism", '$1md.vhn.vn', $body);
    return $body;
}
