<?php

namespace AlexS\GuzzleDynamicPool\Example;

use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class Page
{
    private int $level;
    private TransferStats $stats;
    private Crawler $domCrawler;
    private string $html;
    private ResponseInterface $response;

    public function __construct(int $level, ResponseInterface $response, string $html, TransferStats $stats)
    {
        $this->level = $level;
        $this->stats = $stats;
        $this->response = $response;
        $this->html = $html;

        $this->domCrawler = new Crawler($html, $this->getEffectiveUrl());
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getStats(): TransferStats
    {
        return $this->stats;
    }

    /**
     * @return \Generator<string>
     */
    public function getLinks(): \Generator
    {
        $links = $this->domCrawler->filterXPath('//a')->links();
        foreach ($links as $link) {
            // TODO Filter # links
            yield $link->getUri();
        }
    }

    public function getEffectiveUrl(): string
    {
        return (string) $this->stats->getEffectiveUri();
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getContent(): string
    {
        return $this->html;
    }
}
