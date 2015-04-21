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

use FlameCore\Webtools\UserAgentStringParser;

/**
 * Test class for UserAgentStringParser
 */
class UserAgentStringParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParserWithDesktopBrowsers()
    {
        $testData = array(
            // Namoroka Ubuntu
            'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2pre) Gecko/20100116 Ubuntu/9.10 (karmic) Namoroka/3.6pre'
            => array('firefox', '3.6', 'Linux', 'gecko'),

            // Namoroka Mac
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2) Gecko/20100105 Firefox/3.6'
            => array('firefox', '3.6', 'Mac OS X', 'gecko'),

            // Chrome Mac
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; fr-fr) AppleWebKit/531.21.8 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.10'
            => array('chrome', '4.0', 'Mac OS X', 'webkit'),

            // Safari Mac
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; fr-fr) AppleWebKit/531.21.8 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.10'
            => array('safari', '4.0', 'Mac OS X', 'webkit'),

            // Opera 9 Windows
            'Opera/9.61 (Windows NT 6.0; U; en) Presto/2.1.1'
            => array('opera', '9.61', 'Windows Vista', 'presto'),

            // Opera 10 Windows
            'Opera/9.80 (Windows NT 5.1; U; en) Presto/2.2.15 Version/10.10'
            => array('opera', '10.10', 'Windows XP', 'presto'),

            // Firefox Linux
            'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.17) Gecko/2010010604 Linux Mint/7 (Gloria) Firefox/3.0.17'
            => array('firefox', '3.0', 'Linux', 'gecko'),

            // Firefox Windows
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-GB; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7 GTB6 (.NET CLR 3.5.30729)'
            => array('firefox', '3.5', 'Windows 7', 'gecko'),

            // Firefox OSX
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.8) Gecko/20100202 Firefox/3.5.8'
            => array('firefox', '3.5', 'Mac OS X', 'gecko'),

            // Chrome Linux
            'Mozilla/5.0 (X11; U; Linux i686; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.0.249.43 Safari/532.5'
            => array('chrome', '4.0', 'Linux', 'webkit'),

            // Minefield Mac
            'Gecko 20100113Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.3a1pre) Gecko/20100113 Minefield/3.7a1pre'
            => array('firefox', '3.7', 'Mac OS X', 'gecko'),

            // IE 6 Windows
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; DigExt)'
            => array('msie', '6.0', 'Windows 2000', 'trident'),

            // IE 7 Windows
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/4.0; GTB6; SLCC1; .NET CLR 2.0.50727; OfficeLiveConnector.1.3; OfficeLivePatch.0.0; .NET CLR 3.5.30729; InfoPath.2; .NET CLR 3.0.30729; MSOffice 12)'
            => array('msie', '7.0', 'Windows Vista', 'trident'),

            // IE 11 Windows
            'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko'
            => array('msie', '11.0', 'Windows 7', 'trident')
        );

        $this->doTest($testData);
    }

    public function testParserWithMobileBrowsers()
    {
        $testData = array(
            // iPhone
            'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_1_2 like Mac OS X; de-de) AppleWebKit/528.18 (KHTML, like Gecko) Mobile/7D11'
            => array('applewebkit', '528.18', 'iPhone', 'webkit'),

            // Motorola Xoom
            'Mozilla/5.0 (Linux; U; Android 3.0; en-us; Xoom Build/HRI39) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13'
            => array('safari', '4.0', 'Android 3.0', 'webkit'),

            // Samsung Galaxy Tab
            'Mozilla/5.0 (Linux U Android 2.2 es-es GT-P1000 Build/FROYO) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1'
            => array('safari', '4.0', 'Android 2.2', 'webkit'),

            // Google Nexus
            'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1'
            => array('safari', '4.0', 'Android 2.2', 'webkit'),

            // HTC Desire
            'Mozilla/5.0 (Linux; U; Android 2.1-update1; de-de; HTC Desire 1.19.161.5 Build/ERE27) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17'
            => array('safari', '4.0', 'Android 2.1', 'webkit'),

            // Android Gingerbread
            'Mozilla/5.0 (Linux; U; Android 2.3.6; ru-ru; GT-B5512 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1'
            => array('safari', '4.0', 'Android 2.3.6', 'webkit'),

            // Nexus 7
            'Mozilla/5.0 (Linux; Android 4.1.1; Nexus 7 Build/JRO03D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166  Safari/535.19'
            => array('chrome', '18.0', 'Android 4.1.1', 'webkit'),

            // iPad
            'Mozilla/5.0 (iPad; CPU OS 6_1_3 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10B329 Safari/8536.25'
            => array('safari', '6.0', 'iPad', 'webkit')
        );

        $this->doTest($testData);
    }

    public function testParserWithBots()
    {
        $testData = array(
            // Google Bot
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
            => array('googlebot', '2.1', null, null),

            // Bing Bot
            'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'
            => array('bingbot', '2.0', null, null),

            // MSN Bot
            'msnbot/2.0b (+http://search.msn.com/msnbot.htm)'
            => array('msnbot', '2.0', null, null),

            // Yahoo Bot
            'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)'
            => array('yahoobot', null, null, null),

            // Facebook
            'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)'
            => array('facebookbot', '1.1', null, null),

            // Feedfetcher Google
            'Feedfetcher-Google; (+http://www.google.com/feedfetcher.html; 2 subscribers; feed-id=6924676383167400434)'
            => array(null, null, null, null),

            // Speedy Spider
            'Speedy Spider (http://www.entireweb.com/about/search_tech/speedy_spider/)'
            => array(null, null, null, null)
        );

        $this->doTest($testData);
    }

    private function doTest(array $testData)
    {
        $parser = new UserAgentStringParser();

        foreach($testData as $string => $data)
        {
            $expected = array(
                'string'           => $parser->cleanUserAgentString($string),
                'browser_name'     => $data[0],
                'browser_version'  => $data[1],
                'operating_system' => $data[2],
                'browser_engine'   => $data[3]
            );

            $result = $parser->parse($string);

            $this->assertEquals($expected, $result, $string.' -> '.implode(', ', $result));
        }
    }
}
