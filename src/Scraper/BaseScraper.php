<?php

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseScraper
{
    /**
     * Search the merchant file in GAA
     *
     */
    abstract public function scrape();

    /**
     * Login in to GAA
     *
     * @param $login_url
     * @param $credential
     * @return mixed $crawler
     */
    abstract protected function login($login_url,$credential);

    /**
     * @return mixed
     */
    abstract public function save();

    protected function cleanHtml($html)
    {
        $htmlPurifier = new HTMLPurifier();
        $htmlPurifier->purify($html);
        return $html;
    }
}