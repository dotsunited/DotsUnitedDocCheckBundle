<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use DotsUnited\DocCheckBundle\Security\Authentication\Token\DocCheckToken;

class DocCheckAuthenticationProvider implements AuthenticationProviderInterface
{
    private $key;
    private $roles;
    private $userProvider;
    private $userChecker;

    /**
     * Constructor.
     *
     * @param string $key The key shared with the authentication token
     */
    public function __construct($key, $roles = array(), UserProviderInterface $userProvider = null, UserCheckerInterface $userChecker = null)
    {
        if (null !== $userProvider && null === $userChecker) {
            throw new \InvalidArgumentException('$userChecker cannot be null, if $userProvider is not null.');
        }

        $this->key          = $key;
        $this->roles        = $roles;
        $this->userProvider = $userProvider;
        $this->userChecker  = $userChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        if ($this->key != $token->getKey()) {
            throw new BadCredentialsException('The Token does not contain the expected key.');
        }

        try {
            if (null === $this->userProvider) {
                $authenticatedToken = new DocCheckToken($token->getKey(), $token->getUser(), $this->roles);
                $authenticatedToken->setAttributes($token->getAttributes());

                return $authenticatedToken;
            }

            $user = $this->userProvider->loadUserByUsername($token->getUser());

            if (!$user instanceof UserInterface) {
                throw new \RuntimeException('User provider did not return an implementation of user interface.');
            }

            $this->userChecker->checkPreAuth($user);
            $this->userChecker->checkPostAuth($user);

            $authenticatedToken = new DocCheckToken($token->getKey(), $user, array_merge($user->getRoles(), $this->roles));
            $authenticatedToken->setAttributes($token->getAttributes());

            return $authenticatedToken;

        } catch (AuthenticationException $failed) {
            throw $failed;
        } catch (\Exception $failed) {
            throw new AuthenticationException('Unknown error', $failed->getMessage(), $failed->getCode(), $failed);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof DocCheckToken;
    }
}
