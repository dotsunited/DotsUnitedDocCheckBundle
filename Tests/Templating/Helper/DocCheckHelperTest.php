<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\Tests\Templating\Helper;

use DotsUnited\DocCheckBundle\Templating\Helper\DocCheckHelper;

class DocCheckHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testLoginForm()
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $session
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $session
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('abcdefg'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects($this->once())
            ->method('hasSession')
            ->will($this->returnValue(true));

        $request
            ->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($session));

        $expected = new \stdClass;

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $templating
            ->expects($this->once())
            ->method('render')
            ->with('DotsUnitedDocCheckBundle::loginForm.html.php', array(
                'loginUrl' => 'http://example.com/456/de/xl_red/' . session_name() . '=abcdefg/special1=foo',
                'width'    => 467,
                'height'   => 231,
                'class'    => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_xl_red'
            ))
            ->will($this->returnValue($expected));

        $helper = new DocCheckHelper($templating, $request, 123);

        $options = array(
            'template'           => 'xl_red',
            'login_id'           => 456,
            'base_url'           => 'http://example.com',
            'append_session_id'  => true,
            'locale'             => 'de',
            'special_parameters' => array(
                'special1' => 'foo'
            )
        );

        $this->assertSame($expected, $helper->loginForm($options));
    }

    public function testLoginFormSpecialParametersAsStringGetsParsed()
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $session
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($session));

        $expected = new \stdClass;

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $templating
            ->expects($this->once())
            ->method('render')
            ->with('DotsUnitedDocCheckBundle::loginForm.html.php', array(
                'loginUrl' => 'http://login.doccheck.com/code/123/com/s_red/special1=foo/special2=bar',
                'width'    => 156,
                'height'   => 203,
                'class'    => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_s_red'
            ))
            ->will($this->returnValue($expected));

        $helper = new DocCheckHelper($templating, $request, 123);

        $options = array(
            'special_parameters' => 'special1=foo&special2=bar'
        );

        $this->assertSame($expected, $helper->loginForm($options));
    }

    public function testLoginFormWithCsrfProvider()
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $session
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($session));

        $expected = new \stdClass;

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $templating
            ->expects($this->once())
            ->method('render')
            ->with('DotsUnitedDocCheckBundle::loginForm.html.php', array(
                'loginUrl' => 'http://login.doccheck.com/code/123/com/s_red/_token=1234567890',
                'width'    => 156,
                'height'   => 203,
                'class'    => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_s_red'
            ))
            ->will($this->returnValue($expected));

        $helper = new DocCheckHelper($templating, $request, 123);

        $csrfProvider = $this->getMockBuilder('Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $csrfProvider
            ->expects($this->once())
            ->method('generateCsrfToken')
            ->with('dotsunited_doccheck_loginform')
            ->will($this->returnValue('1234567890'));

        $options = array(
            'csrf_provider' => $csrfProvider
        );

        $this->assertSame($expected, $helper->loginForm($options));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLoginFormWithCsrfProviderThrowsExceptionOnInvalidProvider()
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $session
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($session));

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $templating
            ->expects($this->never())
            ->method('render');

        $helper = new DocCheckHelper($templating, $request, 123);

        $options = array(
            'csrf_provider' => new \stdClass
        );

        $helper->loginForm($options);
    }

    public function testLoginFormWithCustomTemplate()
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $session
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($session));

        $expected = new \stdClass;

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $templating
            ->expects($this->once())
            ->method('render')
            ->with('DotsUnitedDocCheckBundle::loginForm.html.php', array(
                'loginUrl' => 'http://login.doccheck.com/code/123/com/custom',
                'width'    => 123,
                'height'   => 456,
                'class'    => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_custom'
            ))
            ->will($this->returnValue($expected));

        $helper = new DocCheckHelper($templating, $request, 123);

        $helper->addTemplate('custom', array(
            'width'  => 123,
            'height' => 456,
            'class'  => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_custom'
        ));

        $options = array(
            'template' => 'custom'
        );

        $this->assertSame($expected, $helper->loginForm($options));
    }

    public function testLoginFormWithCustomLocale()
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $session
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('fo_BA'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($session));

        $expected = new \stdClass;

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $templating
            ->expects($this->once())
            ->method('render')
            ->with('DotsUnitedDocCheckBundle::loginForm.html.php', array(
                'loginUrl' => 'http://login.doccheck.com/code/123/ba/s_red',
                'width'    => 156,
                'height'   => 203,
                'class'    => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_s_red'
            ))
            ->will($this->returnValue($expected));

        $helper = new DocCheckHelper($templating, $request, 123);

        $helper->addLocale('fo', 'ba');

        $this->assertSame($expected, $helper->loginForm());
    }

    public function testLoginFormWithUnknownLocaleUsesCom()
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $session
            ->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('foo'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($session));

        $expected = new \stdClass;

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $templating
            ->expects($this->once())
            ->method('render')
            ->with('DotsUnitedDocCheckBundle::loginForm.html.php', array(
                'loginUrl' => 'http://login.doccheck.com/code/123/com/s_red',
                'width'    => 156,
                'height'   => 203,
                'class'    => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_s_red'
            ))
            ->will($this->returnValue($expected));

        $helper = new DocCheckHelper($templating, $request, 123);

        $this->assertSame($expected, $helper->loginForm());
    }

    public function testGetName()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $helper = new DocCheckHelper($templating, $request, 123);

        $this->assertEquals('doccheck', $helper->getName());
    }
}
