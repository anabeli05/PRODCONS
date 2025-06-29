<?php

namespace SendGrid\Test;

use PHPUnit\Framework\TestCase;
use SendGrid\Client;
use SendGrid\Exception\InvalidRequest;

class ClientTest extends TestCase
{
    /** @var MockClient */
    private $client;
    /** @var string */
    private $host;
    /** @var array */
    private $headers;

    protected function setUp(): void
    {
        $this->host = 'https://localhost:4010';
        $this->headers = [
            'Content-Type: application/json',
            'Authorization: Bearer SG.XXXX'
        ];
        $this->client = new MockClient($this->host, $this->headers, '/v3');
    }

    public function testConstructor()
    {
        $this->assertEquals($this->host, $this->client->getHost());
        $this->assertEquals($this->headers, $this->client->getHeaders());
        $this->assertEquals('/v3', $this->client->getVersion());
        $this->assertEquals([], $this->client->getPath());
        $this->assertEquals([], $this->client->getCurlOptions());
    }

    public function test_()
    {
        $client = new MockClient($this->host, $this->headers, '/v3');
        $client->setCurlOptions(['foo' => 'bar']);
        $client = $client->_('test');

        $this->assertEquals(['test'], $client->getPath());
        $this->assertEquals(['foo' => 'bar'], $client->getCurlOptions());
    }

    public function test__call()
    {
        $client = $this->client->get();
        $this->assertEquals('https://localhost:4010/v3/', $client->url);

        $queryParams = ['limit' => 100, 'offset' => 0];
        $client = $this->client->get(null, $queryParams);
        $this->assertEquals('https://localhost:4010/v3/?limit=100&offset=0', $client->url);

        $requestBody = ['name' => 'A New Hope'];
        $client = $this->client->get($requestBody);
        $this->assertEquals($requestBody, $client->requestBody);

        $requestHeaders = ['X-Mock: 200'];
        $client = $this->client->get(null, null, $requestHeaders);
        $this->assertEquals($requestHeaders, $client->requestHeaders);

        $client = $this->client->version('/v4');
        $this->assertEquals('/v4', $client->getVersion());

        $client = $this->client->path_to_endpoint();
        $this->assertEquals(['path_to_endpoint'], $client->getPath());
        $client = $client->one_more_segment();
        $this->assertEquals(['path_to_endpoint', 'one_more_segment'], $client->getPath());
    }

    public function testGetHost()
    {
        $client = new Client('https://localhost:4010');
        $this->assertSame('https://localhost:4010', $client->getHost());
    }

    public function testSetHost()
    {
        $client = new Client('https://localhost:4010');
        $client->setHost("https://api.test.com");
        $this->assertSame('https://api.test.com', $client->getHost());
    }

    public function testGetHeaders()
    {
        $client = new Client(
            'https://localhost:4010',
            ['Content-Type: application/json', 'Authorization: Bearer SG.XXXX']
        );
        $this->assertSame(['Content-Type: application/json', 'Authorization: Bearer SG.XXXX'], $client->getHeaders());

        $client2 = new Client('https://localhost:4010');
        $this->assertSame([], $client2->getHeaders());
    }

    public function testGetVersion()
    {
        $client = new Client('https://localhost:4010', [], '/v3');
        $this->assertSame('/v3', $client->getVersion());

        $client = new Client('https://localhost:4010');
        $this->assertNull($client->getVersion());
    }

    public function testGetPath()
    {
        $client = new Client('https://localhost:4010', [], null, ['/foo/bar']);
        $this->assertSame(['/foo/bar'], $client->getPath());

        $client = new Client('https://localhost:4010');
        $this->assertSame([], $client->getPath());
    }

    public function testGetCurlOptions()
    {
        $client = new Client('https://localhost:4010');
        $client->setCurlOptions([CURLOPT_PROXY => '127.0.0.1:8080']);
        $this->assertSame([CURLOPT_PROXY => '127.0.0.1:8080'], $client->getCurlOptions());

        $client = new Client('https://localhost:4010');
        $this->assertSame([], $client->getCurlOptions());
    }

    public function testCurlMulti()
    {
        $client = new Client('https://localhost:4010');
        $client->setIsConcurrentRequest(true);
        $client->get(['name' => 'A New Hope']);
        $client->get(null, null, ['X-Mock: 200']);
        $client->get(null, ['limit' => 100, 'offset' => 0]);

        // returns 3 response object
        $this->assertCount(3, $client->send());
    }

    public function testCreateCurlOptionsWithMethodOnly()
    {
        $client = new Client('https://localhost:4010');

        $result = $this->callMethod($client, 'createCurlOptions', ['get']);

        $this->assertEquals([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FAILONERROR => false,
            CURLOPT_HTTPHEADER => []
        ], $result);
    }

    public function testCreateCurlOptionsWithBody()
    {
        $client = new Client('https://localhost:4010', ['User-Agent: Custom-Client 1.0']);
        $client->setCurlOptions([
            CURLOPT_ENCODING => 'utf-8'
        ]);

        $body = ['foo' => 'bar'];

        $result = $this->callMethod($client, 'createCurlOptions', ['post', $body]);

        $this->assertEquals([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FAILONERROR => false,
            CURLOPT_ENCODING => 'utf-8',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => [
                'User-Agent: Custom-Client 1.0',
                'Content-Type: application/json'
            ]
        ], $result);
    }

    public function testCreateCurlOptionsWithBodyAndHeaders()
    {
        $client = new Client('https://localhost:4010', ['User-Agent: Custom-Client 1.0']);
        $client->setCurlOptions([
            CURLOPT_ENCODING => 'utf-8'
        ]);

        $body = ['foo' => 'bar'];
        $headers = ['Accept-Encoding: gzip'];

        $result = $this->callMethod($client, 'createCurlOptions', ['post', $body, $headers]);

        $this->assertEquals([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FAILONERROR => false,
            CURLOPT_ENCODING => 'utf-8',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => [
                'User-Agent: Custom-Client 1.0',
                'Accept-Encoding: gzip',
                'Content-Type: application/json'
            ]
        ], $result);
    }


    public function testThrowExceptionOnInvalidCall()
    {
        $this->expectException(InvalidRequest::class);

        $client = new Client('invalid://url', ['User-Agent: Custom-Client 1.0']);
        $client->get();
    }

    public function testMakeRequestWithUntrustedRootCert()
    {
        $this->expectException(InvalidRequest::class);
        $this->expectExceptionMessageMatches('/certificate/i');

        $client = new Client('https://untrusted-root.badssl.com/');
        $client->makeRequest('GET', 'https://untrusted-root.badssl.com/');
    }

    public function testFormRepeatUrlArgs()
    {
        $client = new Client('https://localhost:4010');

        $testParams = [
            'thing' => 'stuff',
            'foo' => [
                'bar',
                'bat',
                'baz',
            ],
        ];
        $result = $this->callMethod($client, 'buildUrl', [$testParams]);
        $this->assertEquals('https://localhost:4010/?thing=stuff&foo=bar&foo=bat&foo=baz', $result);
    }

    /**
     * @param object $obj
     * @param string $name
     * @param array $args
     * @return mixed
     */
    private function callMethod($obj, $name, $args = [])
    {
        try {
            $class = new \ReflectionClass($obj);
        } catch (\ReflectionException $e) {
            return null;
        }
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}
