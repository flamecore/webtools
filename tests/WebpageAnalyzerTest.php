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

namespace FlameCore\Webtools\Tests;

use FlameCore\Webtools\WebpageAnalyzer;

/**
 * Test class for WebpageAnalyzer
 */
class WebpageAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $analyzer = new WebpageAnalyzer('http://www.sueddeutsche.de/digital/news-zu-games-konsolen-und-apps-computer-baut-eigenstaendig-super-mario-levels-nach-1.2081489');

        $this->assertEquals('Games-News: Computer baut allein "Super Mario" nach - Digital - Süddeutsche.de', $analyzer->getTitle());
        $this->assertEquals('Ein Programm sieht "Let\'s Play"-Videos und entwickelt das Game weiter', $analyzer->getDescription());

        $expected = array(
            array(
                'url' => 'http://polpix.sueddeutsche.com/polopoly_fs/1.2537021.1435215668!/httpImage/image.jpg_gen/derivatives/640x360/image.jpg',
                'width' => 411,
                'height' => 359,
                'area' => 147549,
            )
        );

        $this->assertEquals($expected, $analyzer->getImages());
    }
}
