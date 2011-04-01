<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class DocCheckExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'doccheck_login_form' => new \Twig_Function_Method($this, 'renderLoginForm', array('is_safe' => array('html')))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'doccheck';
    }

    /**
     * Render the login form.
     *
     * @param array $options An array of options
     * @param string $name A template name
     * @return string An HTML string
     */
    public function renderLoginForm($options = array(), $name = null)
    {
        return $this->container->get('dotsunited_doccheck.templating.helper')->loginForm($options, $name ?: 'DotsUnitedDocCheck::loginForm.html.twig');
    }
}
