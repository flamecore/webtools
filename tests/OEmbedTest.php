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

use FlameCore\Webtools\OEmbed;

/**
 * Class OEmbedTest
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class OEmbedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OEmbed
     */
    protected $oembed;

    public function setUp()
    {
        $this->oembed = new OEmbed();
    }

    public function testDiscover()
    {
        $expected = array(
            'type' => 'photo',
            'title' => 'Shiprock',
            'author_name' => 'Wayne Pinkston',
            'author_url' => 'https://www.flickr.com/photos/pinks2000/',
            'width' => '240',
            'height' => '93',
            'url' => 'https://farm1.staticflickr.com/371/19289053552_f4218f1c7b_m.jpg',
            'thumbnail_url' => 'https://farm1.staticflickr.com/371/19289053552_f4218f1c7b_s.jpg',
            'thumbnail_width' => 75,
            'thumbnail_height' => 75,
            'html' => '<a data-flickr-embed="true" href="https://www.flickr.com/photos/pinks2000/19289053552/" title="Shiprock by Wayne Pinkston, on Flickr"><img src="https://farm1.staticflickr.com/371/19289053552_f4218f1c7b_m.jpg" width="240" height="93" alt="Shiprock"></a><script async src="//widgets.flickr.com/embedr/embedr.js" charset="utf-8"></script>',
            'version' => '1.0',
            'cache_age' => 3600,
            'provider_name' => 'Flickr',
            'provider_url' => 'https://www.flickr.com/',
        );

        $result = $this->oembed->discover('http://localhost:8000/oembed/discover.html');

        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals($expected, (array) $result);
    }

    public function testFetch()
    {
        $expected = array(
            'html' => '<iframe width="300" height="169" src="https://www.youtube.com/embed/qbtbZUmljDI?feature=oembed" frameborder="0" allowfullscreen></iframe>',
            'title' => 'Above & Beyond feat. Alex Vargas - "Sticky Fingers" (Official Music Video)',
            'thumbnail_height' => 360,
            'provider_name' => 'YouTube',
            'version' => '1.0',
            'author_url' => 'http://www.youtube.com/user/aboveandbeyondtv',
            'thumbnail_url' => 'https://i.ytimg.com/vi/qbtbZUmljDI/hqdefault.jpg',
            'thumbnail_width' => 480,
            'type' => 'video',
            'provider_url' => 'http://www.youtube.com/',
            'width' => 300,
            'author_name' => 'Above & Beyond',
            'height' => 169,
        );

        $result = $this->oembed->fetch('https://www.youtube.com/watch?v=qbtbZUmljDI', 'http://localhost:8000/oembed/youtube.json');

        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals($expected, (array) $result);
    }

    public function testGetHtml()
    {
        $expected = '<img src="https://farm1.staticflickr.com/371/19289053552_f4218f1c7b_m.jpg" alt="Shiprock" title="Shiprock" width="240" height="93" />';
        $this->assertEquals($expected, $this->oembed->getHtml('http://localhost:8000/oembed/discover.html'));

        $expected = '<iframe width="300" height="169" src="https://www.youtube.com/embed/qbtbZUmljDI?feature=oembed" frameborder="0" allowfullscreen></iframe>';
        $this->assertEquals($expected, $this->oembed->getHtml('https://www.youtube.com/watch?v=qbtbZUmljDI', 'http://localhost:8000/oembed/youtube.json'));
    }

    public function testToHtml()
    {
        $data = array(
            'type' => 'photo',
            'title' => 'Shiprock',
            'author_name' => 'Wayne Pinkston',
            'author_url' => 'https://www.flickr.com/photos/pinks2000/',
            'width' => '240',
            'height' => '93',
            'url' => 'https://farm1.staticflickr.com/371/19289053552_f4218f1c7b_m.jpg',
            'thumbnail_url' => 'https://farm1.staticflickr.com/371/19289053552_f4218f1c7b_s.jpg',
            'thumbnail_width' => 75,
            'thumbnail_height' => 75,
            'html' => '<a data-flickr-embed="true" href="https://www.flickr.com/photos/pinks2000/19289053552/" title="Shiprock by Wayne Pinkston, on Flickr"><img src="https://farm1.staticflickr.com/371/19289053552_f4218f1c7b_m.jpg" width="240" height="93" alt="Shiprock"></a><script async src="//widgets.flickr.com/embedr/embedr.js" charset="utf-8"></script>',
            'version' => '1.0',
            'cache_age' => 3600,
            'provider_name' => 'Flickr',
            'provider_url' => 'https://www.flickr.com/',
        );

        $expected = '<img src="https://farm1.staticflickr.com/371/19289053552_f4218f1c7b_m.jpg" alt="Shiprock" title="Shiprock" width="240" height="93" />';

        $this->assertEquals($expected, $this->oembed->toHtml($data));
        $this->assertEquals($expected, $this->oembed->toHtml((object) $data));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testToHtmlWithInvalidData()
    {
        $this->oembed->toHtml('test');
    }

    public function testSetTemplate()
    {
        $closure = function ($var) { return $var['html']; };

        $this->oembed->setTemplate('foo', '{html}');
        $this->oembed->setTemplate('bar', $closure);
        $this->oembed->setTemplate('rich', '<div>{html}</div>');

        $expected = array(
            'link'          => '<a href="{url}">{title}</a>',
            'link_notitle'  => '<a href="{url}">{url}</a>',
            'photo'         => '<img src="{url}" alt="{title}" title="{title}" width="{width}" height="{height}" />',
            'photo_notitle' => '<img src="{url}" width="{width}" height="{height}" />',
            'video'         => '{html}',
            'video_notitle' => '{html}',
            'rich'          => '<div>{html}</div>',
            'rich_notitle'  => '{html}',
            'foo'           => '{html}',
            'bar'           => $closure
        );

        $this->assertEquals($expected, $this->oembed->getTemplates());
    }
}
