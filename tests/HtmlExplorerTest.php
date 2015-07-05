<?php
/**
 * Webtools Library
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
 * @version  1.2
 * @link     http://www.flamecore.org
 * @license  ISC License <http://opensource.org/licenses/ISC>
 */

namespace FlameCore\Webtools\Tests;

use FlameCore\Webtools\HtmlExplorer;

/**
 * Test class for WebpageAnalyzer
 */
class HtmlExplorerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FlameCore\Webtools\HtmlExplorer
     */
    protected $html;

    public function setUp()
    {
        $html = <<<HTML
<!DOCTYPE HTML>
<html>
    <head>
        <title>test</title>
    </head>
    <body>
        <h1 id="foo">test</h1>
        <p>test</p>
        <p>test</p>
    </body>
</html>
HTML;

        $this->html = new HtmlExplorer($html);
    }

    public function testFind()
    {
        $result = $this->html->find('foo');

        $this->assertInstanceOf('DOMElement', $result);
    }

    public function testFindTags()
    {
        $result = $this->html->findTags('p');

        $this->assertInstanceOf('DOMNodeList', $result);
        $this->assertEquals(2, $result->length);
    }

    public function testFindFirstTag()
    {
        $result = $this->html->findFirstTag('p');

        $this->assertInstanceOf('DOMElement', $result);
    }

    public function testGetDOM()
    {
        $result = $this->html->getDOM();

        $this->assertInstanceOf('DOMDocument', $result);
    }
}
