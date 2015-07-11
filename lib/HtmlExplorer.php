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
 * The HtmlExplorer class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class HtmlExplorer
{
    /**
     * The DOMDocument
     *
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * Creates a HtmlExplorer object.
     *
     * @param string $html The HTML source to be parsed
     */
    public function __construct($html)
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $success = $dom->loadHTML($html);

        if (!$success) {
            $errors = libxml_get_errors();
            $lastError = array_shift($errors);

            throw new \RuntimeException(sprintf('Could not load HTML: %s at line %d column %d.', trim($lastError->message), $lastError->line, $lastError->column));
        }

        $this->dom = $dom;
    }

    /**
     * Finds an element by ID.
     *
     * @param string $elementId The element ID
     * @return \DOMElement|false
     */
    public function find($elementId)
    {
        return $this->dom->getElementById($elementId) ?: false;
    }

    /**
     * Finds all elements with given tag name.
     *
     * @param string $tagName The tag name
     * @return \DOMNodeList
     */
    public function findTags($tagName)
    {
        return $this->dom->getElementsByTagName($tagName);
    }

    /**
     * Finds the first element with given tag name.
     *
     * @param string $tagName The tag name
     * @return \DOMElement|false
     */
    public function findFirstTag($tagName)
    {
        $nodes = $this->findTags($tagName);

        return $nodes->length > 0 ? $nodes->item(0) : false;
    }

    /**
     * Returns the DOM of the HTML.
     *
     * @return \DOMDocument
     */
    public function getDOM()
    {
        return $this->dom;
    }

    /**
     * Returns the SimpleXML version of the DOM.
     *
     * @param string $class Return an object of the specified class. That class should extend the SimpleXMLElement class.
     * @return \SimpleXMLElement
     */
    public function getSimpleXML($class = null)
    {
        return simplexml_import_dom($this->getDOM(), $class);
    }

    /**
     * Loads HTML from webpage.
     *
     * @param string $url The URL of the webpage
     * @param \FlameCore\Webtools\HttpClient $http The HttpClient instance to use
     * @return self
     * @throws \RuntimeException if the URL could not be loaded.
     */
    public static function fromWeb($url, HttpClient $http = null)
    {
        $http = $http ?: new HttpClient();

        $request = $http->get($url);

        if (!$request->success) {
            throw new \RuntimeException(sprintf('The URL "%s" could not be loaded.', $url));
        }

        return new static($request->data);
    }

    /**
     * Loads HTML from file.
     *
     * @param string $filename The name of the file
     * @return self
     * @throws \LogicException if the file could not be opened.
     */
    public static function fromFile($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new \LogicException(sprintf('The file "%s" does not exist or is not readable.', $filename));
        }

        $html = file_get_contents($filename);

        return new static($html);
    }
}
