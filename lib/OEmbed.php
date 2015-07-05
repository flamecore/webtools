<?php
/**
 * FlameCore Webtools
 * Copyright (C) 2015 IceFlame.net
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
 * @version  2.0
 * @link     http://www.flamecore.org
 * @license  http://opensource.org/licenses/ISC ISC License
 */

namespace FlameCore\Webtools;

/**
 * The OEmbed class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 * @author   Fabian Pimminger
 */
class OEmbed
{
    /**
     * The default arguments
     *
     * @var array
     */
    protected $args = array(
        'maxwidth'  => 300,
        'maxheight' => 150
    );

    /**
     * List of defined templates
     *
     * @var array
     */
    protected $templates = array(
        'link'          => '<a href="{url}">{title}</a>',
        'link_notitle'  => '<a href="{url}">{url}</a>',
        'photo'         => '<img src="{url}" alt="{title}" title="{title}" width="{width}" height="{height}" />',
        'photo_notitle' => '<img src="{url}" width="{width}" height="{height}" />',
        'video'         => '{html}',
        'video_notitle' => '{html}',
        'rich'          => '{html}',
        'rich_notitle'  => '{html}'
    );

    /**
     * The HttpClient instance to use
     *
     * @var \FlameCore\Webtools\HttpClient
     */
    protected $http;

    /**
     * @var array
     */
    private $formats = array();

    /**
     * Creates an OEmbed object.
     *
     * @param array $args An array of default arguments
     * @param \FlameCore\Webtools\HttpClient $http The HttpClient instance to use
     */
    public function __construct(array $args = null, HttpClient $http = null)
    {
        if ($args !== null) {
            $this->args = array_merge($this->args, $args);
        }

        $this->http = $http ?: new HttpClient();

        $this->formats = array(
            'application/json' => [$this, 'parseJson'],
            'text/xml' => [$this, 'parseXml']
        );
    }

    /**
     * Tries to fetch data for the given content URL using discovery.
     *
     * @param string $url The content URL
     * @param array $args An array of optional extra arguments
     * @return object|false Returns the request data as an object, or FALSE on failure.
     */
    public function discover($url, array $args = [])
    {
        $url = trim($url);
        $html = HtmlExplorer::fromWeb($url, $this->http);

        $typePattern = '#^('.join('|', array_keys($this->formats)).')\+oembed$#';

        $nodes = $html->findTags('link');
        foreach ($nodes as $node) {
            $rel = strtolower(trim($node->getAttribute('rel')));
            $type = strtolower(trim($node->getAttribute('type')));

            if (in_array($rel, ['alternate', 'alternative']) && preg_match($typePattern, $type, $matches)) {
                $url = trim($node->getAttribute('href'));
                $result = $this->queryProvider($url, null, null, $args);
                if ($result->success) {
                    $parser = $this->formats[$matches[1]];
                    return $parser($result->data);
                }
            }
        }

        return false;
    }

    /**
     * Fetches the data for the given content URL.
     *
     * @param string $url The content URL
     * @param string $endpoint The endpoint URL
     * @param array $args An array of optional extra arguments
     * @return object|false Returns the request data as an object, or FALSE on failure.
     */
    public function fetch($url, $endpoint, array $args = [])
    {
        $url = trim($url);
        $endpoint = trim($endpoint);

        foreach ($this->formats as $type => $parser) {
            $type = explode('/', $type);
            $result = $this->queryProvider($endpoint, $url, $type[1], $args);
            if ($result->success) {
                return $parser($result->data);
            }
        }

        return false;
    }

    /**
     * Returns the HTML representation for the given content URL.
     *
     * @param string $url The content URL
     * @param string $endpoint The endpoint URL, set to NULL to use discovery
     * @param array $args An array of optional extra arguments
     * @return string|false Returns the HTML code, or FALSE on failure.
     */
    public function getHtml($url, $endpoint = null, array $args = [])
    {
        if ($endpoint !== null) {
            $data = $this->fetch($url, $endpoint, $args);
        } else {
            $data = $this->discover($url, $args);
        }

        if (!$data) {
            return false;
        }

        return $this->toHtml($data);
    }

    /**
     * Generates an HTML code from the given data.
     *
     * @param object|array $data The media data
     * @return string|false Returns the HTML code, or FALSE on failure.
     */
    public function toHtml($data)
    {
        if (!is_object($data) && !is_array($data)) {
            throw new \InvalidArgumentException('The $data parameter takes only an object or an array.');
        }

        $data = (array) $data;
        $type = (string) $data['type'];

        switch ($type) {
            case 'photo':
                if (empty($data['width']) || empty($data['height'])) {
                    return false;
                }
                // no break

            case 'link':
                if (empty($data['url']) || !$this->isSafeUrl($data['url'])) {
                    return false;
                }
                break;

            case 'video':
            case 'rich':
                if (empty($data['html']) || !$this->isSafeHtml($data['html'])) {
                    return false;
                }
                break;

            default:
                return false;
        }

        return $this->render($type, $data);
    }

    /**
     * Queries the oEmbed endpoint of the provider.
     *
     * @param string $endpoint The endpoint URL
     * @param string $url The content URL, set to NULL if the endpoint URL already contains this parameter
     * @param string $format The request format, set to NULL if the endpoint URL already contains this parameter
     * @param array $args An array of optional extra arguments
     * @return string Returns the raw request data.
     */
    public function queryProvider($endpoint, $url = null, $format = 'json', array $args = [])
    {
        if ($url !== null) {
            if ($format === null) {
                throw new \InvalidArgumentException('The request format must be defined in manual mode.');
            }

            $args['url'] = (string) $url;
            $args['format'] = (string) $format;
            $concat = '?';
        } else {
            $concat = parse_url($endpoint, PHP_URL_QUERY) != '' ? '&' : '?';
        }

        $query = http_build_query(array_replace($this->args, $args));

        return $this->http->get($endpoint.$concat.$query);
    }

    /**
     * Returns the list of defined templates.
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Defines a template for the given media type.
     *
     * @param string $type The media type
     * @param string|callable $template The template
     */
    public function setTemplate($type, $template)
    {
        if (!is_callable($template)) {
            $template = (string) $template;
        }

        $this->templates[$type] = $template;
    }

    /**
     * Determines whether the given URL is safe.
     *
     * @param string $url The URL
     * @return bool
     */
    protected function isSafeUrl($url)
    {
        return (bool) preg_match('|^https?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }

    /**
     * Determines whether the given HTML code is safe.
     *
     * @param string $html The HTML code
     * @return bool
     */
    protected function isSafeHtml($html)
    {
        return true;
    }

    /**
     * Escapes the given string for use in HTML.
     *
     * @param string $string The string to escape
     * @return string
     */
    protected function escapeHtml($string)
    {
        return htmlentities($string, ENT_COMPAT, 'UTF-8', false);
    }

    /**
     * Renders the HTML for the given media type.
     *
     * @param string $type The media type
     * @param array $data The media data
     * @return string
     */
    protected function render($type, array $data = [])
    {
        $templateName = empty($data['title']) ? $type.'_notitle' : $type;
        $template = $this->templates[$templateName];

        if (is_callable($template)) {
            return $template($data);
        } else {
            $replace = array();

            foreach ($data as $key => $value) {
                $replace['{'.$key.'}'] = $key != 'html' ? $this->escapeHtml($value) : (string) $value;
            }

            return strtr($template, $replace);
        }
    }

    /**
     * Parses a JSON response.
     *
     * @param string $data The JSON data
     * @return \stdClass|false
     */
    protected function parseJson($data)
    {
        $result = json_decode(trim($data), false);

        return json_last_error() == JSON_ERROR_NONE ? $result : false;
    }

    /**
     * Parses an XML response.
     *
     * @param string $data The XML data
     * @return \SimpleXMLElement|false
     */
    protected function parseXml($data)
    {
        if (function_exists('simplexml_load_string')) {
            return simplexml_load_string(trim($data));
        }

        return false;
    }
}
