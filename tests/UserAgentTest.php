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
 * @version  1.1
 * @link     http://www.flamecore.org
 * @license  ISC License <http://opensource.org/licenses/ISC>
 */

namespace FlameCore\Webtools\Tests;

use FlameCore\Webtools\UserAgent;

/**
 * Test class for UserAgent
 */
class UserAgentTest extends \PHPUnit_Framework_TestCase
{
    public function testBrowserUserAgentString()
    {
        $userAgentString = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2pre) Gecko/20100116 Ubuntu/9.10 (karmic) Namoroka/3.6pre';
        $userAgent = new UserAgent($userAgentString);

        $this->assertEquals('firefox', $userAgent->getBrowserName(), '$userAgent->getBrowserName() works');
        $this->assertEquals('3.6', $userAgent->getBrowserVersion(), '$userAgent->getBrowserVersion() works');
        $this->assertEquals('Linux', $userAgent->getOperatingSystem(), '$userAgent->getOperatingSystem() works');
        $this->assertEquals('gecko', $userAgent->getBrowserEngine(), '$userAgent->getEngine() works');
        $this->assertEquals(false, $userAgent->isUnknown(), 'User agent is not unknown');
        $this->assertEquals(false, $userAgent->isBot(), 'User agent is not a bot');
    }

    public function testBotUserAgentString()
    {
        $userAgentString = 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)';
        $userAgent = new UserAgent($userAgentString);

        $this->assertEquals('bingbot', $userAgent->getBrowserName(), '$userAgent->getBrowserName() works');
        $this->assertEquals('2.0', $userAgent->getBrowserVersion(), '$userAgent->getBrowserVersion() works');
        $this->assertEquals(null, $userAgent->getOperatingSystem(), '$userAgent->getOperatingSystem() works');
        $this->assertEquals(null, $userAgent->getBrowserEngine(), '$userAgent->getEngine() works');
        $this->assertEquals(false, $userAgent->isUnknown(), 'User agent is not unknown');
        $this->assertEquals(true, $userAgent->isBot(), 'User agent is a bot');
    }

    public function testMalformedUserAgentString()
    {
        $userAgent = new UserAgent('hmm...');

        $this->assertEquals(null, $userAgent->getBrowserName(), '$userAgent->getBrowserName() works');
        $this->assertEquals(null, $userAgent->getBrowserVersion(), '$userAgent->getBrowserVersion() works');
        $this->assertEquals(null, $userAgent->getOperatingSystem(), '$userAgent->getOperatingSystem() works');
        $this->assertEquals(null, $userAgent->getBrowserEngine(), '$userAgent->getEngine() works');
        $this->assertEquals(true, $userAgent->isUnknown(), 'User agent is unknown');
    }

    public function testToArray()
    {
        $userAgentString = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2pre) Gecko/20100116 Ubuntu/9.10 (karmic) Namoroka/3.6pre';
        $userAgent = new UserAgent($userAgentString);

        $expected = array(
            'browser_name'     => 'firefox',
            'browser_version'  => '3.6',
            'operating_system' => 'Linux',
            'browser_engine'   => 'gecko'
        );

        $result = $userAgent->toArray();

        $this->assertEquals($expected, $result);
    }
}
