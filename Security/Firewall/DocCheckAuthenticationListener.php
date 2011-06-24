<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\Security\Firewall;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Form\CsrfProvider\CsrfProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use DotsUnited\DocCheckBundle\Security\Authentication\Token\DocCheckToken;

/**
 * DocCheck anonymous authentication listener.
 */
class DocCheckAuthenticationListener extends AbstractAuthenticationListener
{
    private $csrfProvider;

    /**
     * {@inheritdoc}
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, array $options = array(), AuthenticationSuccessHandlerInterface $successHandler = null, AuthenticationFailureHandlerInterface $failureHandler = null, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, CsrfProviderInterface $csrfProvider = null)
    {
        $options = array_merge(array(
            'key'            => uniqid(),
            'csrf_parameter' => '_csrf_token',
            'csrf_page_id'   => 'dotsunited_doccheck_anonymous_login',
        ), $options);

        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $options, $successHandler, $failureHandler, $logger, $dispatcher);

        $this->csrfProvider = $csrfProvider;
    }

    /**
     * {@inheritDoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        if (null !== $this->csrfProvider) {
            $csrfToken = $request->get($this->options['csrf_parameter']);

            if (false === $this->csrfProvider->isCsrfTokenValid($this->options['csrf_page_id'], $csrfToken)) {
                throw new InvalidCsrfTokenException('Invalid CSRF token.');
            }
        }

        $token = new DocCheckToken($this->options['key'], 'DOCCHECK_ANONYMOUS');
        $token->setAttributes($request->query->all());

        return $this->authenticationManager->authenticate($token);
    }
}

