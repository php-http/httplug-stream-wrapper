<?php

namespace Http\StreamWrapper\Tests;

use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use Http\StreamWrapper\StreamWrapper;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $response = new Response(200, [], 'foobar');
        $client = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMock();
        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        StreamWrapper::enable($client);
        $body = file_get_contents('http://foobar.com');

        $this->assertEquals('foobar', $body);
    }
}
