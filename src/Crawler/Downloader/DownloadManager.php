<?php
namespace Crawler\Downloader;

abstract class DownloadManager
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $headers;

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
    public function getDom()
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($this->getContent());
        return $dom;
    }

    /**
     * @return \DOMXPath
     */
    public function getXpath()
    {
        $xpath = new \DOMXPath($this->getDom());
        return $xpath;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    protected function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    protected function generatelHeaders($httpHeaders)
    {
        $additionalHeaders = array(
            "httpHeaders" => $httpHeaders,
            "created"     => date("d-m-Y H:i:s"),
        );
        return $additionalHeaders;
    }
}