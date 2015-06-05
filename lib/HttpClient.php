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
 * @version  1.0
 * @link     http://www.flamecore.org
 * @license  ISC License <http://opensource.org/licenses/ISC>
 */

namespace FlameCore\Webtools;

/**
 * The HttpClient class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class HttpClient
{
    const ENCODING_ALL = '';
    const ENCODING_GZIP = 'gzip';
    const ENCODING_DEFLATE = 'deflate';
    const ENCODING_IDENTITY = 'identity';

    const AUTH_BASIC = CURLAUTH_BASIC;
    const AUTH_NTLM = CURLAUTH_NTLM;

    const PROXY_HTTP = CURLPROXY_HTTP;
    const PROXY_SOCKS5 = CURLPROXY_SOCKS5;

    /**
     * The headers
     *
     * @var array
     */
    protected $headers = array();

    /**
     * The user agent string
     *
     * @var string
     */
    protected $useragent = 'Mozilla/5.0 (compatible; FlameCore Webtools/1.0)';

    /**
     * The timeout in seconds
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * The accepted encoding
     *
     * @var string
     */
    protected $encoding = self::ENCODING_ALL;

    /**
     * The curl handle
     *
     * @var resource
     */
    protected $handle;

    /**
     * Creates a HttpClient object.
     */
    public function __construct()
    {
        $this->handle = curl_init();

        $this->headers = array(
            'Accept' => 'text/html, application/xhtml+xml, application/xml',
            'Accept-Charset' => 'UTF-8',
            'Connection' => 'Keep-Alive',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
        );
    }

    /**
     * Destructs the object.
     *
     * @return void
     */
    public function __destruct()
    {
        curl_close($this->handle);
    }

    /**
     * Executes a GET request.
     *
     * @param string $url The URL to make the request to
     * @param array $headers Optional extra headers
     * @return object Returns an object containing the response information.
     */
    public function get($url, array $headers = array())
    {
        curl_setopt($this->handle, CURLOPT_HTTPGET, true);

        return $this->execute($url, $headers);
    }

    /**
     * Executes a POST request.
     *
     * @param string $url The URL to make the request to
     * @param array|string $data The full data to post in the operation
     * @param array $headers Optional extra headers
     * @return object Returns an object containing the response information.
     */
    public function post($url, $data, array $headers = array())
    {
        curl_setopt($this->handle, CURLOPT_POST, true);
        curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data);

        return $this->execute($url, $headers);
    }

    /**
     * Gets all defined headers.
     *
     * @return array Returns the headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Sets a header.
     *
     * @param string $name The name of the header
     * @param string $value The value of the header
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * Sets multiple headers.
     *
     * @param array $headers The headers to set
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Gets the user agent string.
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->useragent;
    }

    /**
     * Sets the user agent string.
     *
     * @param string $useragent The new user agent string
     */
    public function setUserAgent($useragent)
    {
        $this->useragent = (string) $useragent;
    }

    /**
     * Gets the timeout.
     *
     * @return int Returns the timeout in seconds.
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Sets the timeout.
     *
     * @param int $timeout The new timeout in seconds
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int) $timeout;
    }

    /**
     * Gets the encoding.
     *
     * @return string Returns the encoding.
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Sets the encoding.
     *
     * @param string $encoding The encoding to use. This can be any of the HttpClient::ENCODING_* constants.
     */
    public function setEncoding($encoding)
    {
        if (!in_array($encoding, array(self::ENCODING_IDENTITY, self::ENCODING_DEFLATE, self::ENCODING_GZIP, self::ENCODING_ALL))) {
            throw new \InvalidArgumentException('The encoding must be one of: HttpClient::ENCODING_IDENTITY, HttpClient::ENCODING_DEFLATE, HttpClient::ENCODING_GZIP, HttpClient::ENCODING_ALL.');
        }

        $this->encoding = $encoding;
    }

    /**
     * Enables the use of cookies.
     *
     * @param string $jarfile The full path to the file where cookies are saved (optional)
     * @throws \InvalidArgumentException if the given parameter is invalid.
     * @throws \LogicException if the cookie file could not be opened.
     */
    public function acceptCookies($jarfile = null)
    {
        $jarfile = $jarfile ? (string) $jarfile : sys_get_temp_dir().DIRECTORY_SEPARATOR.'cookies.txt';

        if (empty($jarfile)) {
            throw new \InvalidArgumentException('The $jarfile parameter must be a non-empty string.');
        }

        if (!file_exists($jarfile) && !touch($jarfile)) {
            throw new \LogicException(sprintf('Cookie file "%s" could not be opened. Make sure that the directory is writable.', $jarfile));
        }

        curl_setopt($this->handle, CURLOPT_COOKIEFILE, $jarfile);
        curl_setopt($this->handle, CURLOPT_COOKIEJAR, $jarfile);
    }

    /**
     * Enables the use of a proxy.
     *
     * @param string $proxy The proxy to use. Use "@" to separate credentials and address.
     * @param int $type The type of proxy. This can be one of: HttpClient::PROXY_HTTP (default), HttpClient::PROXY_SOCKS5.
     * @param int $auth The HTTP authentication method(s) to use for the proxy connection.
     *   This can be one of: HttpClient::AUTH_BASIC (default), HttpClient::AUTH_NTLM.
     * @throws \InvalidArgumentException if the given parameter is invalid.
     */
    public function useProxy($proxy, $type = self::PROXY_HTTP, $auth = self::AUTH_BASIC)
    {
        if (!is_string($proxy) || empty($proxy)) {
            throw new \InvalidArgumentException('The $proxy parameter must be a non-empty string.');
        }

        if (!in_array($type, array(self::PROXY_HTTP, self::PROXY_SOCKS5))) {
            throw new \InvalidArgumentException('The $type parameter must be one of: HttpClient::PROXY_HTTP, HttpClient::PROXY_SOCKS5.');
        }

        if (!in_array($auth, array(self::AUTH_BASIC, self::AUTH_NTLM))) {
            throw new \InvalidArgumentException('The $auth parameter must be one of: HttpClient::AUTH_BASIC, HttpClient::AUTH_NTLM.');
        }

        if (strpos($proxy, '@') !== false) {
            list($proxyCredentials, $proxyAddress) = explode('@', $proxy, 2);
            curl_setopt($this->handle, CURLOPT_PROXY, $proxyAddress);
            curl_setopt($this->handle, CURLOPT_PROXYUSERPWD, $proxyCredentials);
        } else {
            curl_setopt($this->handle, CURLOPT_PROXY, $proxy);
        }
    }

    /**
     * Executes a request.
     *
     * @param string $url The URL to fetch
     * @param array $headers Optional extra headers
     * @return object Returns an object containing the response information.
     */
    protected function execute($url, array $headers = array())
    {
        curl_setopt($this->handle, CURLOPT_URL, $url);

        curl_setopt($this->handle, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($this->handle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->handle, CURLOPT_ENCODING, $this->encoding);

        $curlheaders = array();

        $headers = array_merge($this->headers, $headers);
        foreach ($headers as $headerName => $headerValue) {
            $curlheaders[] = "$headerName: $headerValue";
        }

        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $curlheaders);

        curl_setopt($this->handle, CURLOPT_HEADER, false);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);

        if ($data = curl_exec($this->handle)) {
            $info = curl_getinfo($this->handle);
            if ($info['http_code'] >= 200 && $info['http_code'] < 300) {
                $info['success'] = true;
                $info['data'] = $data;
            } else {
                $info['success'] = false;
            }
        } else {
            $info = array();
            $info['success'] = false;
            $info['error'] = curl_errno($this->handle);
            $info['error_text'] = curl_error($this->handle);
        }

        return (object) $info;
    }
}
