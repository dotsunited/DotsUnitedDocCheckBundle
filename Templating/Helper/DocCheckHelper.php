<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormContextInterface;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;

class DocCheckHelper extends Helper
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $loginId;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $templates = array(
        's_red' => array(
            'width'  => 156,
            'height' => 203,
            'class'  => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_s_red'
        ),
        'm_red' => array(
            'width'  => 311,
            'height' => 188,
            'class'  => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_m_red'
        ),
        'l_red' => array(
            'width'  => 424,
            'height' => 215,
            'class'  => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_l_red'
        ),
        'xl_red' => array(
            'width'  => 467,
            'height' => 231,
            'class'  => 'dotsunited_doccheck_loginform dotsunited_doccheck_loginform_xl_red'
        )
    );

    /**
     * @var arary
     */
    private $locales = array(
        'de'    => 'de',
        'en'    => 'com',
        'fr'    => 'fr',
        'nl'    => 'nl',
        'be'    => 'befr',
        'be_FR' => 'befr',
        'be_NL' => 'benl',
        'ch'    => 'chde',
        'ch_DE' => 'chde',
        'ch_FR' => 'chfr',
        'it'    => 'it',
        'es'    => 'es',
    );

    /**
     * Constructor.
     *
     * @param EngineInterface $templating
     * @param Request $request
     * @param integer $loginId
     * @param FormContextInterface $context
     */
    public function __construct(EngineInterface $templating, Request $request, $loginId, array $options = array())
    {
        $this->templating = $templating;
        $this->request    = $request;
        $this->loginId    = $loginId;
        $this->options    = $options;
    }

    /**
     * Add a template.
     *
     * @param string $name The name of the template
     * @param array $attributes The attributes of the template
     */
    public function addTemplate($name, array $attributes)
    {
        $this->templates[$name] = $attributes;
        return $this;
    }

    /**
     * Add a locale.
     *
     * @param string $locale The original locale
     * @param string $fix The locale understandable by DocCheck
     */
    public function addLocale($locale, $fix)
    {
        $this->locales[$locale] = $fix;
        return $this;
    }

    /**
     * Returns the HTML for the DocCheck login iframe.
     *
     * The default template includes the following parameters:
     *
     * * loginUrl
     * * width
     * * height
     * * class
     *
     * @param array $options An array of options
     * @param string $name A template name
     *
     * @return string An HTML string
     */
    public function loginForm($options = array(), $name = null)
    {
        $name = $name ?: 'DotsUnitedDocCheckBundle::loginForm.html.php';

        $session = $this->request->getSession();

        $options += $this->options;

        $options += array(
            'template'           => 's_red',
            'login_id'           => (string) $this->loginId,
            'base_url'           => 'http://login.doccheck.com/code',
            'append_session_id'  => false,
            'locale'             => $session->getLocale(),
            'special_parameters' => array(),
            'csrf_provider'      => null,
            'csrf_field_name'    => '_token',
            'csrf_page_id'       => 'dotsunited_doccheck_loginform'
        );

        $loginUrl = sprintf(
            '%s/%s/%s/%s',
            trim($options['base_url'], '/'),
            urlencode($options['login_id']),
            urlencode($this->fixLocale($options['locale'])),
            urlencode($options['template'])
        );

        if ($options['append_session_id'] && $this->request->hasSession()) {
            $loginUrl .= '/' . urlencode(session_name()) . '=' . urlencode($session->getId());
        }

        $params = array();

        if (!empty($options['special_parameters'])) {
            if (is_string($options['special_parameters'])) {
                parse_str($options['special_parameters'], $params);
            } else {
                $params = (array) $options['special_parameters'];
            }
        }

        if ($options['csrf_provider']) {
            if (!$options['csrf_provider'] instanceof CsrfProviderInterface) {
                throw new \RuntimeException('The object passed to the "csrf_provider" option must implement CsrfProviderInterface');
            }

            $params[$options['csrf_field_name']] = $options['csrf_provider']->generateCsrfToken($options['csrf_page_id']);
        }

        foreach ($params as $key => $val) {
            $loginUrl .= '/' . urlencode($key) . '=' . urlencode($val);
        }
 
        $parameters = array('loginUrl' => $loginUrl) + $this->templates[$options['template']];

        return $this->templating->render($name, $parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'doccheck';
    }

    /**
     * Transform a given locale to one understandable by DocCheck.
     * 
     * @param string $locale
     * @return string
     */
    private function fixLocale($locale)
    {
        if (isset($this->locales[$locale])) {
            return $this->locales[$locale];
        }

        $locale = substr($locale, 0, 2);

        if (isset($this->locales[$locale])) {
            return $this->locales[$locale];
        }

        return 'com';
    }
}
