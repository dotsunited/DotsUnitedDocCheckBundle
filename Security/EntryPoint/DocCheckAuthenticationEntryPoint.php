<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\Security\EntryPoint;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * FormAuthenticationEntryPoint starts an authentication via a login form.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DocCheckAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $loginPath;
    private $useForward;
    private $httpKernel;

    /**
     * Constructor
     *
     * @param HttpKernelInterface $kernel
     * @param string  $loginPath  The path to the login form
     * @param Boolean $useForward Whether to forward or redirect to the login form
     */
    public function __construct(HttpKernelInterface $kernel, $loginPath, $useForward = false)
    {
        $this->httpKernel   = $kernel;
        $this->loginPath    = $loginPath;
        $this->useForward   = (Boolean) $useForward;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($this->useForward) {
            return $this->httpKernel->handle(Request::create($this->loginPath), HttpKernelInterface::SUB_REQUEST);
        }

        return new RedirectResponse(0 !== strpos($this->loginPath, 'http') ? $request->getUriForPath($this->loginPath) : $this->loginPath, 302);
    }
}
