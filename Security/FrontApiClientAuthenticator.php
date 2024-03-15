<?php

namespace RetailCrm\ServiceBundle\Security;

use RetailCrm\ServiceBundle\Models\Error;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Class FrontApiClientAuthenticator
 *
 * @package RetailCrm\ServiceBundle\Security
 */
class FrontApiClientAuthenticator extends AbstractClientAuthenticator
{
    private Security $security;

    public function __construct(ErrorJsonResponseFactory $errorResponseFactory, Security $security)
    {
        parent::__construct($errorResponseFactory);

        $this->security = $security;
    }

    /**
     * {@inheritdoc }
     */
    public function supports(Request $request): bool
    {
        if ($this->security->getUser()) {
            return false;
        }

        return $request->request->has(static::AUTH_FIELD);
    }

    public function authenticate(Request $request): Passport
    {
        $clientId = $request->request->get(static::AUTH_FIELD);

        return new SelfValidatingPassport(
            new UserBadge((string) $clientId),
            [new RememberMeBadge()]
        );
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $error = new Error();
        $error->message = 'Authentication required';

        return $this->errorResponseFactory->create($error, Response::HTTP_UNAUTHORIZED);
    }
}
