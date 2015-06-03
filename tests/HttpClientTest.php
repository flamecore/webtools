<?php
/**
 * Webtools Library
 * Copyright (C) 2014 IceFlame.net
 *
 * Permission to use, copy, modify, and/or distribute this software for
 * any purpose with or without fee is hereby granted, provided that the
 * above copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE
 * FOR ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY
 * DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER
 * IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING
 * OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 *
 * @package  FlameCore\Webtools
 * @version  1.2
 * @link     http://www.flamecore.org
 * @license  ISC License <http://opensource.org/licenses/ISC>
 */

namespace FlameCore\Webtools\Tests;

use FlameCore\Webtools\HttpClient;

/**
 * Test class for HttpClient
 */
class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FlameCore\Webtools\HttpClient
     */
    protected $http;

    public function setUp()
    {
        if (!fsockopen('httpbin.org', 80, $errno, $errstr, 30)) {
            $this->markTestSkipped('HTTP test server seems to be offline.');
        }

        $this->http = new HttpClient();
    }

    public function testPutFileHandle()
    {
        $result = $this->http->putFile('http://httpbin.org/put', fopen(__FILE__, 'r'));

        $info = $this->examineResult($result);

        $this->assertEquals(file_get_contents(__FILE__), $info->data);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPutFileHandleException()
    {
        $this->http->putFile('http://httpbin.org/put', curl_init());
    }

    public function testPutFile()
    {
        $result = $this->http->putFile('http://httpbin.org/put', __FILE__);

        $info = $this->examineResult($result);

        $this->assertEquals(file_get_contents(__FILE__), $info->data);
    }

    /**
     * @expectedException \LogicException
     */
    public function testPutFileException()
    {
        $this->http->putFile('http://httpbin.org/put', 'foo.txt');
    }

    public function testPut()
    {
        $result = $this->http->put('http://httpbin.org/put', ['foo' => 'bar']);

        $info = $this->examineResult($result);

        $this->assertEquals(['foo' => 'bar'], (array) $info->form);
    }

    public function testPost()
    {
        $result = $this->http->post('http://httpbin.org/post', 'foo=bar');

        $info = $this->examineResult($result);

        $this->assertEquals(['foo' => 'bar'], (array) $info->form);
    }

    public function testGet()
    {
        $result = $this->http->get('http://httpbin.org/get?foo=bar');

        $info = $this->examineResult($result);

        $this->assertEquals(['foo' => 'bar'], (array) $info->args);
        $this->assertEquals('Mozilla/5.0 (compatible; FlameCore Webtools/1.2)', $info->headers->{'User-Agent'});
    }

    public function testRequestWithData()
    {
        $result = $this->http->request('PATCH', 'http://httpbin.org/patch', ['foo' => 'bar']);

        $info = $this->examineResult($result);

        $this->assertEquals(['foo' => 'bar'], (array) $info->form);
    }

    public function testRequestWithoutData()
    {
        $result = $this->http->request('DELETE', 'http://httpbin.org/delete');

        $info = $this->examineResult($result);

        $this->assertEmpty((array) $info->form);
    }

    public function testSetHeader()
    {
        $http = new HttpClient();
        $http->setHeader('X-Foo', 'bar');

        $result = $http->get('http://httpbin.org/headers');

        $info = $this->examineResult($result);

        $this->assertInternalType('array', $http->getHeaders());
        $this->assertArrayHasKey('X-Foo', $http->getHeaders());
        $this->assertObjectHasAttribute('X-Foo', $info->headers);
        $this->assertEquals('bar', $info->headers->{'X-Foo'});
    }

    public function testSetHeaders()
    {
        $http = new HttpClient();
        $http->setHeaders(['X-Foo' => 'bar']);

        $result = $http->get('http://httpbin.org/headers', ['X-Bar' => 'baz']);

        $info = $this->examineResult($result);

        // ->getHeaders()
        $this->assertInternalType('array', $http->getHeaders());
        $this->assertArrayHasKey('X-Foo', $http->getHeaders());

        // Headers set by ->setHeaders()
        $this->assertObjectHasAttribute('X-Foo', $info->headers);
        $this->assertEquals('bar', $info->headers->{'X-Foo'});

        // Headers set by extra
        $this->assertObjectHasAttribute('X-Bar', $info->headers);
        $this->assertEquals('baz', $info->headers->{'X-Bar'});
    }

    public function testSetUserAgent()
    {
        $uastring = 'TestUA/1.0 (fake; FlameCore Webtools/1.2)';
        $http = new HttpClient($uastring);
        $http->setUserAgent($uastring);

        $result = $http->get('http://httpbin.org/user-agent');

        $info = $this->examineResult($result);

        $this->assertEquals($uastring, $info->{'user-agent'});
    }

    public function testSetEncoding()
    {
        $http = new HttpClient();
        $http->setEncoding(HttpClient::ENCODING_GZIP);

        $result = $http->get('http://httpbin.org/gzip');

        $info = $this->examineResult($result);

        $this->assertObjectHasAttribute('Accept-Encoding', $info->headers);
        $this->assertEquals('gzip', $info->headers->{'Accept-Encoding'});
    }

    public function testAcceptCookies()
    {
        $this->http->acceptCookies();
        $result = $this->http->get('http://httpbin.org/cookies/set?foo=bar');

        $info = $this->examineResult($result);

        $this->assertEquals(['foo' => 'bar'], (array) $info->cookies);
    }

    public function testError()
    {
        $http = new HttpClient();
        $result = $http->get('http://httpbin.org/status/404');

        $this->assertFalse($result->success);
        $this->assertEquals(404, $result->http_code);
    }

    public function testFail()
    {
        $http = new HttpClient();
        $result = $http->get('http://nowhere.test.flamecore.org');

        $this->assertFalse($result->success);
    }

    /**
     * @param \stdClass $result
     * @return \stdClass
     */
    private function examineResult(\stdClass $result)
    {
        $this->assertTrue($result->success);

        return $result->success ? json_decode($result->data) : new \stdClass();
    }
}
