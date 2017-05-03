<?php
namespace Crawler\Downloader;

abstract class DownloadManager
{
    /**
     * @var string
     */
    protected $url;

    /**
     * DownloadManager constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    abstract public function getContent();

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return \DOMDocument
     */
    public function getDom(){
        $dom = new \DOMDocument();
        @$dom->loadHTML($this->getContent());
        return $dom;
    }

    /**
     * @return \DOMXPath
     */
    public function getXpath(){
        $xpath = new \DOMXPath($this->getDom());
        return $xpath;
    }
}