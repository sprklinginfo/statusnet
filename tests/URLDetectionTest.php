<?php

if (isset($_SERVER) && array_key_exists('REQUEST_METHOD', $_SERVER)) {
    print "This script must be run from the command line\n";
    exit();
}

define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));
define('STATUSNET', true);

require_once INSTALLDIR . '/lib/common.php';

class URLDetectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     *
     */
    public function testProduction($content, $expected)
    {
        $rendered = common_render_text($content);
        $this->assertEquals($expected, $rendered);
    }

    static public function provider()
    {
        return array(
                     array('not a link :: no way',
                           'not a link :: no way'),
                     array('http://127.0.0.1',
                           '<a href="http://127.0.0.1/" rel="external">http://127.0.0.1</a>'),
                     array('127.0.0.1',
                           '<a href="http://127.0.0.1/" rel="external">127.0.0.1</a>'),
                     array('127.0.0.1:99',
                           '<a href="http://127.0.0.1:99/" rel="external">127.0.0.1:99</a>'),
                     array('127.0.0.1/Name:test.php',
                           '<a href="http://127.0.0.1/Name:test.php" rel="external">127.0.0.1/Name:test.php</a>'),
                     array('127.0.0.1/~test',
                           '<a href="http://127.0.0.1/~test" rel="external">127.0.0.1/~test</a>'),
                     array('127.0.0.1/+test',
                           '<a href="http://127.0.0.1/+test" rel="external">127.0.0.1/+test</a>'),
                     array('127.0.0.1/$test',
                           '<a href="http://127.0.0.1/$test" rel="external">127.0.0.1/$test</a>'),
                     array('127.0.0.1/\'test',
                           '<a href="http://127.0.0.1/\'test" rel="external">127.0.0.1/\'test</a>'),
                     array('127.0.0.1/"test',
                           '<a href="http://127.0.0.1/&quot;test" rel="external">127.0.0.1/&quot;test</a>'),
                     array('127.0.0.1/-test',
                           '<a href="http://127.0.0.1/-test" rel="external">127.0.0.1/-test</a>'),
                     array('127.0.0.1/_test',
                           '<a href="http://127.0.0.1/_test" rel="external">127.0.0.1/_test</a>'),
                     array('127.0.0.1/!test',
                           '<a href="http://127.0.0.1/!test" rel="external">127.0.0.1/!test</a>'),
                     array('127.0.0.1/*test',
                           '<a href="http://127.0.0.1/*test" rel="external">127.0.0.1/*test</a>'),
                     array('127.0.0.1/test%20stuff',
                           '<a href="http://127.0.0.1/test%20stuff" rel="external">127.0.0.1/test%20stuff</a>'),
                     array('http://[::1]:99/test.php',
                           '<a href="http://[::1]:99/test.php" rel="external">http://[::1]:99/test.php</a>'),
                     array('http://::1/test.php',
                           '<a href="http://::1/test.php" rel="external">http://::1/test.php</a>'),
                     array('http://::1',
                           '<a href="http://::1/" rel="external">http://::1</a>'),
                     array('2001:4978:1b5:0:21d:e0ff:fe66:59ab/test.php',
                           '<a href="http://2001:4978:1b5:0:21d:e0ff:fe66:59ab/test.php" rel="external">2001:4978:1b5:0:21d:e0ff:fe66:59ab/test.php</a>'),
                     array('[2001:4978:1b5:0:21d:e0ff:fe66:59ab]:99/test.php',
                           '<a href="http://[2001:4978:1b5:0:21d:e0ff:fe66:59ab]:99/test.php" rel="external">[2001:4978:1b5:0:21d:e0ff:fe66:59ab]:99/test.php</a>'),
                     array('2001:4978:1b5:0:21d:e0ff:fe66:59ab',
                           '<a href="http://2001:4978:1b5:0:21d:e0ff:fe66:59ab/" rel="external">2001:4978:1b5:0:21d:e0ff:fe66:59ab</a>'),
                     array('http://127.0.0.1',
                           '<a href="http://127.0.0.1/" rel="external">http://127.0.0.1</a>'),
                     array('example.com',
                           '<a href="http://example.com/" rel="external">example.com</a>'),
                     array('example.com',
                           '<a href="http://example.com/" rel="external">example.com</a>'),
                     array('http://example.com',
                           '<a href="http://example.com/" rel="external">http://example.com</a>'),
                     array('http://example.com.',
                           '<a href="http://example.com/" rel="external">http://example.com</a>.'),
                     array('/var/lib/example.so',
                           '/var/lib/example.so'),
                     array('example',
                           'example'),
                     array('user@example.com',
                           '<a href="mailto:user@example.com" rel="external">user@example.com</a>'),
                     array('user_name+other@example.com',
                           '<a href="mailto:user_name+other@example.com" rel="external">user_name+other@example.com</a>'),
                     array('mailto:user@example.com',
                           '<a href="mailto:user@example.com" rel="external">mailto:user@example.com</a>'),
                     array('mailto:user@example.com?subject=test',
                           '<a href="mailto:user@example.com?subject=test" rel="external">mailto:user@example.com?subject=test</a>'),
                     array('#example',
                           '#<span class="tag"><a href="' . common_local_url('tag', array('tag' => common_canonical_tag('example'))) . '" rel="tag">example</a></span>'),
                     array('#example.com',
                           '#<span class="tag"><a href="' . common_local_url('tag', array('tag' => common_canonical_tag('example.com'))) . '" rel="tag">example.com</a></span>'),
                     array('#.net',
                           '#<span class="tag"><a href="' . common_local_url('tag', array('tag' => common_canonical_tag('.net'))) . '" rel="tag">.net</a></span>'),
                     array('http://example',
                           '<a href="http://example/" rel="external">http://example</a>'),
                     array('http://3xampl3',
                           '<a href="http://3xampl3/" rel="external">http://3xampl3</a>'),
                     array('http://example/',
                           '<a href="http://example/" rel="external">http://example/</a>'),
                     array('http://example/path',
                           '<a href="http://example/path" rel="external">http://example/path</a>'),
                     array('http://example.com',
                           '<a href="http://example.com/" rel="external">http://example.com</a>'),
                     array('https://example.com',
                           '<a href="https://example.com/" rel="external">https://example.com</a>'),
                     array('ftp://example.com',
                           '<a href="ftp://example.com/" rel="external">ftp://example.com</a>'),
                     array('ftps://example.com',
                           '<a href="ftps://example.com/" rel="external">ftps://example.com</a>'),
                     array('http://user@example.com',
                           '<a href="http://user@example.com/" rel="external">http://user@example.com</a>'),
                     array('http://user:pass@example.com',
                           '<a href="http://user:pass@example.com/" rel="external">http://user:pass@example.com</a>'),
                     array('http://example.com:8080',
                           '<a href="http://example.com:8080/" rel="external">http://example.com:8080</a>'),
                     array('http://example.com:8080/test.php',
                           '<a href="http://example.com:8080/test.php" rel="external">http://example.com:8080/test.php</a>'),
                     array('example.com:8080/test.php',
                           '<a href="http://example.com:8080/test.php" rel="external">example.com:8080/test.php</a>'),
                     array('http://www.example.com',
                           '<a href="http://www.example.com/" rel="external">http://www.example.com</a>'),
                     array('http://example.com/',
                           '<a href="http://example.com/" rel="external">http://example.com/</a>'),
                     array('http://example.com/path',
                           '<a href="http://example.com/path" rel="external">http://example.com/path</a>'),
                     array('http://example.com/path.html',
                           '<a href="http://example.com/path.html" rel="external">http://example.com/path.html</a>'),
                     array('http://example.com/path.html#fragment',
                           '<a href="http://example.com/path.html#fragment" rel="external">http://example.com/path.html#fragment</a>'),
                     array('http://example.com/path.php?foo=bar&bar=foo',
                           '<a href="http://example.com/path.php?foo=bar&amp;bar=foo" rel="external">http://example.com/path.php?foo=bar&amp;bar=foo</a>'),
                     array('http://example.com.',
                           '<a href="http://example.com/" rel="external">http://example.com</a>.'),
                     array('http://müllärör.de',
                           '<a href="http://m&#xFC;ll&#xE4;r&#xF6;r.de/" rel="external">http://müllärör.de</a>'),
                     array('http://ﺱﺲﺷ.com',
                           '<a href="http://&#xFEB1;&#xFEB2;&#xFEB7;.com/" rel="external">http://ﺱﺲﺷ.com</a>'),
                     array('http://сделаткартинки.com',
                           '<a href="http://&#x441;&#x434;&#x435;&#x43B;&#x430;&#x442;&#x43A;&#x430;&#x440;&#x442;&#x438;&#x43D;&#x43A;&#x438;.com/" rel="external">http://сделаткартинки.com</a>'),
                     array('http://tūdaliņ.lv',
                           '<a href="http://t&#x16B;dali&#x146;.lv/" rel="external">http://tūdaliņ.lv</a>'),
                     array('http://brændendekærlighed.com',
                           '<a href="http://br&#xE6;ndendek&#xE6;rlighed.com/" rel="external">http://brændendekærlighed.com</a>'),
                     array('http://あーるいん.com',
                           '<a href="http://&#x3042;&#x30FC;&#x308B;&#x3044;&#x3093;.com/" rel="external">http://あーるいん.com</a>'),
                     array('http://예비교사.com',
                           '<a href="http://&#xC608;&#xBE44;&#xAD50;&#xC0AC;.com/" rel="external">http://예비교사.com</a>'),
                     array('http://example.com.',
                           '<a href="http://example.com/" rel="external">http://example.com</a>.'),
                     array('http://example.com?',
                           '<a href="http://example.com/" rel="external">http://example.com</a>?'),
                     array('http://example.com!',
                           '<a href="http://example.com/" rel="external">http://example.com</a>!'),
                     array('http://example.com,',
                           '<a href="http://example.com/" rel="external">http://example.com</a>,'),
                     array('http://example.com;',
                           '<a href="http://example.com/" rel="external">http://example.com</a>;'),
                     array('http://example.com:',
                           '<a href="http://example.com/" rel="external">http://example.com</a>:'),
                     array('\'http://example.com\'',
                           '\'<a href="http://example.com/" rel="external">http://example.com</a>\''),
                     array('"http://example.com"',
                           '&quot;<a href="http://example.com/" rel="external">http://example.com</a>&quot;'),
                     array('http://example.com',
                           '<a href="http://example.com/" rel="external">http://example.com</a>'),
                     array('(http://example.com)',
                           '(<a href="http://example.com/" rel="external">http://example.com</a>)'),
                     array('[http://example.com]',
                           '[<a href="http://example.com/" rel="external">http://example.com</a>]'),
                     array('<http://example.com>',
                           '&lt;<a href="http://example.com/" rel="external">http://example.com</a>&gt;'),
                     array('http://example.com/path/(foo)/bar',
                           '<a href="http://example.com/path/(foo)/bar" rel="external">http://example.com/path/(foo)/bar</a>'),
                     array('http://example.com/path/[foo]/bar',
                           '<a href="http://example.com/path/[foo]/bar" rel="external">http://example.com/path/[foo]/bar</a>'),
                     array('http://example.com/path/foo/(bar)',
                           '<a href="http://example.com/path/foo/(bar)" rel="external">http://example.com/path/foo/(bar)</a>'),
                     //Not a valid url - urls cannot contain unencoded square brackets
                     array('http://example.com/path/foo/[bar]',
                           '<a href="http://example.com/path/foo/[bar]" rel="external">http://example.com/path/foo/[bar]</a>'),
                     array('Hey, check out my cool site http://example.com okay?',
                           'Hey, check out my cool site <a href="http://example.com/" rel="external">http://example.com</a> okay?'),
                     array('What about parens (e.g. http://example.com/path/foo/(bar))?',
                           'What about parens (e.g. <a href="http://example.com/path/foo/(bar)" rel="external">http://example.com/path/foo/(bar)</a>)?'),
                     array('What about parens (e.g. http://example.com/path/foo/(bar)?',
                           'What about parens (e.g. <a href="http://example.com/path/foo/(bar)" rel="external">http://example.com/path/foo/(bar)</a>?'),
                     array('What about parens (e.g. http://example.com/path/foo/(bar).)?',
                           'What about parens (e.g. <a href="http://example.com/path/foo/(bar)" rel="external">http://example.com/path/foo/(bar)</a>.)?'),
                     //Not a valid url - urls cannot contain unencoded commas
                     array('What about parens (e.g. http://example.com/path/(foo,bar)?',
                           'What about parens (e.g. <a href="http://example.com/path/(foo,bar)" rel="external">http://example.com/path/(foo,bar)</a>?'),
                     array('Unbalanced too (e.g. http://example.com/path/((((foo)/bar)?',
                           'Unbalanced too (e.g. <a href="http://example.com/path/((((foo)/bar)" rel="external">http://example.com/path/((((foo)/bar)</a>?'),
                     array('Unbalanced too (e.g. http://example.com/path/(foo))))/bar)?',
                           'Unbalanced too (e.g. <a href="http://example.com/path/(foo))))/bar" rel="external">http://example.com/path/(foo))))/bar</a>)?'),
                     array('Unbalanced too (e.g. http://example.com/path/foo/((((bar)?',
                           'Unbalanced too (e.g. <a href="http://example.com/path/foo/((((bar)" rel="external">http://example.com/path/foo/((((bar)</a>?'),
                     array('Unbalanced too (e.g. http://example.com/path/foo/(bar))))?',
                           'Unbalanced too (e.g. <a href="http://example.com/path/foo/(bar)" rel="external">http://example.com/path/foo/(bar)</a>)))?'),
                     array('example.com',
                           '<a href="http://example.com/" rel="external">example.com</a>'),
                     array('example.org',
                           '<a href="http://example.org/" rel="external">example.org</a>'),
                     array('example.co.uk',
                           '<a href="http://example.co.uk/" rel="external">example.co.uk</a>'),
                     array('www.example.co.uk',
                           '<a href="http://www.example.co.uk/" rel="external">www.example.co.uk</a>'),
                     array('farm1.images.example.co.uk',
                           '<a href="http://farm1.images.example.co.uk/" rel="external">farm1.images.example.co.uk</a>'),
                     array('example.museum',
                           '<a href="http://example.museum/" rel="external">example.museum</a>'),
                     array('example.travel',
                           '<a href="http://example.travel/" rel="external">example.travel</a>'),
                     array('example.com.',
                           '<a href="http://example.com/" rel="external">example.com</a>.'),
                     array('example.com?',
                           '<a href="http://example.com/" rel="external">example.com</a>?'),
                     array('example.com!',
                           '<a href="http://example.com/" rel="external">example.com</a>!'),
                     array('example.com,',
                           '<a href="http://example.com/" rel="external">example.com</a>,'),
                     array('example.com;',
                           '<a href="http://example.com/" rel="external">example.com</a>;'),
                     array('example.com:',
                           '<a href="http://example.com/" rel="external">example.com</a>:'),
                     array('\'example.com\'',
                           '\'<a href="http://example.com/" rel="external">example.com</a>\''),
                     array('"example.com"',
                           '&quot;<a href="http://example.com/" rel="external">example.com</a>&quot;'),
                     array('example.com',
                           '<a href="http://example.com/" rel="external">example.com</a>'),
                     array('(example.com)',
                           '(<a href="http://example.com/" rel="external">example.com</a>)'),
                     array('[example.com]',
                           '[<a href="http://example.com/" rel="external">example.com</a>]'),
                     array('<example.com>',
                           '&lt;<a href="http://example.com/" rel="external">example.com</a>&gt;'),
                     array('Hey, check out my cool site example.com okay?',
                           'Hey, check out my cool site <a href="http://example.com/" rel="external">example.com</a> okay?'),
                     array('Hey, check out my cool site example.com.I made it.',
                           'Hey, check out my cool site <a href="http://example.com/" rel="external">example.com</a>.I made it.'),
                     array('Hey, check out my cool site example.com.Funny thing...',
                           'Hey, check out my cool site <a href="http://example.com/" rel="external">example.com</a>.Funny thing...'),
                     array('Hey, check out my cool site example.com.You will love it.',
                           'Hey, check out my cool site <a href="http://example.com/" rel="external">example.com</a>.You will love it.'),
                     array('What about parens (e.g. example.com/path/foo/(bar))?',
                           'What about parens (e.g. <a href="http://example.com/path/foo/(bar)" rel="external">example.com/path/foo/(bar)</a>)?'),
                     array('What about parens (e.g. example.com/path/foo/(bar)?',
                           'What about parens (e.g. <a href="http://example.com/path/foo/(bar)" rel="external">example.com/path/foo/(bar)</a>?'),
                     array('What about parens (e.g. example.com/path/foo/(bar).)?',
                           'What about parens (e.g. <a href="http://example.com/path/foo/(bar)" rel="external">example.com/path/foo/(bar)</a>.)?'),
                     array('What about parens (e.g. example.com/path/(foo,bar)?',
                           'What about parens (e.g. <a href="http://example.com/path/(foo,bar)" rel="external">example.com/path/(foo,bar)</a>?'),
                     array('file.ext',
                           'file.ext'),
                     array('file.html',
                           'file.html'),
                     array('file.php',
                           'file.php')
                     );
    }
}

