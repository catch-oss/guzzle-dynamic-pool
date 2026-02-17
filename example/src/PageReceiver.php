<?php

namespace AlexS\GuzzleDynamicPool\Example;

use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;

class PageReceiver
{
    private ?TransferStats $stats = null;
    private ResponseInterface $response;
    private string $html;
    private readonly int $currentLevel;

    public function __construct(int $currentLevel)
    {
        $this->currentLevel = $currentLevel;
    }

    public function onStats(): \Closure
    {
        return function (TransferStats $stats): void {
            $this->stats = $stats;
        };
    }

    public function __invoke(ResponseInterface $response): self
    {
        $this->response = $response;

        $this->html = $this->response->getBody()->getContents();

        try {
            $this->response->getBody()->rewind();
        } catch (\RuntimeException $exception) {
            // Rewind is not allowed, skipping
        }

        return $this;
    }

    public function generatePage(): Page
    {
        return new Page($this->currentLevel, $this->response, $this->html, $this->stats);
    }
}
