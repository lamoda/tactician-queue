<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Middleware;

use Lamoda\QueueBundle\Factory\PublisherFactory;
use League\Tactician\Middleware;

final class QueuePublishMiddleware implements Middleware
{
    /** @var PublisherFactory */
    private $publisherFactory;

    public function __construct(PublisherFactory $publisherFactory)
    {
        $this->publisherFactory = $publisherFactory;
    }

    public function execute($command, callable $next)
    {
        $result = $next($command);

        $this->publisherFactory->releaseAll();

        return $result;
    }
}
