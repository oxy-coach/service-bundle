<?php

namespace RetailCrm\ServiceBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Class CallbackClientAuthenticator
 *
 * @package RetailCrm\ServiceBundle\Security
 */
class CallbackClientAuthenticator extends AbstractClientAuthenticator
{
    /**
     * {@inheritdoc }
     */
    public function supports(Request $request): bool
    {
        return $request->request->has(static::AUTH_FIELD) || $request->query->has(static::AUTH_FIELD);
    }

    public function authenticate(Request $request): Passport
    {
        $clientId = $request->request->get(static::AUTH_FIELD) ?? $request->query->get(static::AUTH_FIELD);

        return new SelfValidatingPassport(
            new UserBadge((string) $clientId)
        );
    }
}
