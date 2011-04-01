<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class DocCheckToken extends AbstractToken
{
    private $key;

    /**
     * Constructor.
     *
     * @param string $key   The key shared with the authentication provider
     * @param string $user  The user
     * @param Role[] $roles An array of roles
     */
    public function __construct($key, $user, array $roles = array())
    {
        parent::__construct($roles);

        $this->key = $key;
        $this->setUser($user);
        $this->setAuthenticated(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * Returns the key.
     *
     * @return string The Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array($this->key, parent::serialize()));
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($str)
    {
        list($this->key, $parentStr) = unserialize($str);
        parent::unserialize($parentStr);
    }
}
