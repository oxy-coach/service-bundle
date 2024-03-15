<?php

namespace RetailCrm\ServiceBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\CallbackClientAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Class CallbackClientAuthenticatorTest
 *
 * @package RetailCrm\ServiceBundle\Tests\Security
 */
class CallbackClientAuthenticatorTest extends TestCase
{
    public function testAuthenticate(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $auth = new CallbackClientAuthenticator($errorResponseFactory);
        $result = $auth->authenticate(new Request([], [CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertInstanceOf(SelfValidatingPassport::class, $result);
    }

    public function testOnAuthenticationFailure(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $errorResponseFactory
            ->expects(static::once())
            ->method('create')
            ->willReturn(
                new JsonResponse(
                    ['message' => 'An authentication exception occurred.'],
                    Response::HTTP_FORBIDDEN
                )
            );

        $auth = new CallbackClientAuthenticator($errorResponseFactory);
        $result = $auth->onAuthenticationFailure(new Request(), new AuthenticationException());

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertEquals(Response::HTTP_FORBIDDEN, $result->getStatusCode());
    }

    public function testSupports(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);

        $auth = new CallbackClientAuthenticator($errorResponseFactory);
        $result = $auth->supports(new Request([], [CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertTrue($result);

        $result = $auth->supports(new Request([CallbackClientAuthenticator::AUTH_FIELD => '123']));

        static::assertTrue($result);

        $result = $auth->supports(new Request());

        static::assertFalse($result);
    }

    public function testOnAuthenticationSuccess(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $request = $this->createMock(Request::class);
        $token = $this->createMock(TokenInterface::class);
        $auth = new CallbackClientAuthenticator($errorResponseFactory);

        $result = $auth->onAuthenticationSuccess($request, $token, 'key');

        static::assertNull($result);
    }
}
