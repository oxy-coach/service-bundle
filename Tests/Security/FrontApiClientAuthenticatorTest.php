<?php

namespace RetailCrm\ServiceBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator;
use RetailCrm\ServiceBundle\Tests\DataFixtures\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Class FrontApiClientAuthenticatorTest
 *
 * @package RetailCrm\ServiceBundle\Tests\Security
 */
class FrontApiClientAuthenticatorTest extends TestCase
{
    public function testAuthenticate(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);
        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->authenticate(new Request([], [FrontApiClientAuthenticator::AUTH_FIELD => '123']));

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
        $security = $this->createMock(Security::class);

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->start(new Request(), new AuthenticationException());

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertEquals(Response::HTTP_FORBIDDEN, $result->getStatusCode());
    }

    public function testSupportsFalse(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(new User());

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->supports(new Request());

        static::assertFalse($result);
    }

    public function testSupportsTrue(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);
        $result = $auth->supports(new Request([], [FrontApiClientAuthenticator::AUTH_FIELD => '123']));

        static::assertTrue($result);
    }

    public function testOnAuthenticationSuccess(): void
    {
        $errorResponseFactory = $this->createMock(ErrorJsonResponseFactory::class);
        $security = $this->createMock(Security::class);
        $request = $this->createMock(Request::class);
        $token = $this->createMock(TokenInterface::class);
        $auth = new FrontApiClientAuthenticator($errorResponseFactory, $security);

        $result = $auth->onAuthenticationSuccess($request, $token, 'key');

        static::assertNull($result);
    }
}
