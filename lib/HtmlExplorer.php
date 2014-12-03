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
        $dom->loadHTML($html);

        $this->dom = $dom;
    }

    /**
     * Finds an element by ID.
     *
     * @param string $elementId The element ID
     * @return \DOMElement|bool
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
     * @return \DOMNode|bool
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
}
