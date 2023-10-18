<?php

namespace RetailCrm\ServiceBundle\Messenger\Middleware;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Throwable;

/**
 * Class LockableMessageMiddleware
 *
 * @package RetailCrm\ServiceBundle\Messenger\Middleware
 */
class LockableMessageMiddleware implements MiddlewareInterface
{
    /**
     * @var LockFactory
     */
    private $lockFactory;

    /**
     * @var int|null
     */
    private $ttl;

    public function __construct(LockFactory $lockFactory, int $ttl = null)
    {
        $this->lockFactory = $lockFactory;
        $this->ttl = $ttl;
    }

    /**
     * @param Envelope $envelope
     * @param StackInterface $stack
     *
     * @return Envelope
     *
     * @throws Throwable
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($envelope->all(ReceivedStamp::class) && $message instanceof LockableMessage) {
            $lock = $this->lockFactory->createLock($this->objectHash($message), $this->ttl);
            if (!$lock->acquire()) {
                return $envelope;
            }

            try {
                return $stack->next()->handle($envelope, $stack);
            } catch (Throwable $exception) {
                throw $exception;
            } finally {
                $lock->release();
            }
        }

        return $stack->next()->handle($envelope, $stack);
    }

    /**
     * @param LockableMessage $message
     *
     * @return string
     */
    private function objectHash(LockableMessage $message): string
    {
        return hash('crc32', serialize($message));
    }
}
