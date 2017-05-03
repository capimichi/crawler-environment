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


}