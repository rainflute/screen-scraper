<?php

/**
 * Project:     Merchantaccess
 * Team:        Rebel Alliance <rebel.alliance@nabancard.com>
 *
 * @author      YUXIAO TAN <ytan@nabancard.com>
 * @copyright   1992-2016 North American Bancard
 */
class ScreenScraper extends BaseScraper
{
    protected $username;
    protected $password;
    protected $url;
    protected $content;
    /**
     * @var Output
     */
    protected $output;

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param mixed $output
     * @return ScreenScraper
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    public function __construct($loginUrl,$username,$password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->url = $loginUrl;
    }

    /**
     * Start scraping
     */
    public function scrape()
    {

    }

    /**
     * Login in to GAA
     *
     * @param $login_url
     * @param $credential
     * @return mixed $crawler
     */
    protected function login($login_url,$credential)
    {

    }

    /**
     * @return mixed
     */
    public function save()
    {
        $this->output->save();
    }
}