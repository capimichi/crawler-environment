<?php

namespace Crawler\Downloader;


class CacheDownloadManager
{
    /**
     * @var DownloadManager
     */
    protected $downloadManager;

    /**
     * @var string
     */
    protected $cacheDirectory;

    /**
     * @var array
     */
    protected $headers;


    /**
     * CacheDownloadManager constructor.
     * @param DownloadManager $downloadManager
     * @param string $cacheDirectory
     */
    public function __construct(DownloadManager $downloadManager, $cacheDirectory)
    {
        $this->downloadManager = $downloadManager;
        $this->cacheDirectory = $cacheDirectory;
        if (!file_exists($cacheDirectory)) {
            $this->createDirectory($cacheDirectory);
        }
    }

    /**
     * @param bool $raw If true remove headers
     *
     * @return null|string
     */
    public function getContent($raw = false)
    {
        $cachePath = $this->getCacheFile();
        if (file_exists($cachePath)) {
            $content = file_get_contents($cachePath);
        } else {
            $content = $this->downloadManager->getContent();
            file_put_contents($cachePath, $content);
        }
        $this->setHeaders($this->generateHeaders($content));
        if ($raw) {
            $content = preg_replace("\[downloadManager\].*?\[\/downloadManager\]", "", $content);
        }
        return $content;
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
     * @return string
     */
    public function getCacheFile()
    {
        return $this->getCacheDir() . $this->getCacheName();
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    protected function getCacheName()
    {
        return md5($this->downloadManager->getUrl());
    }

    /**
     * @return string
     */
    protected function getCacheDir()
    {
        $subdir = rtrim($this->cacheDirectory, DIRECTORY_SEPARATOR);
        $subdir .= DIRECTORY_SEPARATOR . substr($this->getCacheName(), 0, 2);
        $subdir .= DIRECTORY_SEPARATOR . substr($this->getCacheName(), 2, 2);
        $subdir .= DIRECTORY_SEPARATOR . substr($this->getCacheName(), 4, 2);
        $subdir .= DIRECTORY_SEPARATOR;
        if (!file_exists($subdir)) {
            $this->createDirectory($subdir);
        }
        return $subdir;
    }

    /**
     * @param $directory
     */
    protected function createDirectory($directory)
    {
        mkdir($directory, 0777, true);
    }

    /**
     * @param array $headers
     */
    protected function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param $content
     *
     * @return array|null
     */
    protected function generateHeaders($content)
    {
        if (preg_match("/\[downloadManager\](.*?)\[\/downloadManager\]/is", $content, $headers)) {
            $headers = $headers[1];
            $headers = json_decode($headers);
            return $headers;
        }
        return null;
    }

}