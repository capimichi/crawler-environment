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
     * CacheDownloadManager constructor.
     * @param DownloadManager $downloadManager
     * @param string $cacheDirectory
     */
    public function __construct(DownloadManager $downloadManager, $cacheDirectory)
    {
        $this->downloadManager = $downloadManager;
        $this->cacheDirectory = $cacheDirectory;
        if(!file_exists($cacheDirectory)){
            $this->createDirectory($cacheDirectory);
        }
    }

    /**
     * @return null|string
     */
    public function getContent(){
        $cachePath = $this->getCacheDir() . $this->getCacheName();
        if(file_exists($cachePath)){
            $content = file_get_contents($cachePath);
        } else {
            $content = $this->downloadManager->getContent();
            file_put_contents($cachePath, $content);
        }
        return $content;
    }

    /**
     * @return string
     */
    protected function getCacheName(){
        return md5($this->downloadManager->getUrl());
    }

    /**
     * @return string
     */
    protected function getCacheDir(){
        $subdir = trim($this->cacheDirectory, DIRECTORY_SEPARATOR);
        $subdir .= DIRECTORY_SEPARATOR . substr($this->getCacheName(), 0, 2);
        $subdir .= DIRECTORY_SEPARATOR . substr($this->getCacheName(), 2, 2);
        $subdir .= DIRECTORY_SEPARATOR . substr($this->getCacheName(), 4, 2);
        $subdir .= DIRECTORY_SEPARATOR;
        if(!file_exists($subdir)){
            $this->createDirectory($subdir);
        }
        return $subdir;
    }

    /**
     * @param $directory
     */
    protected function createDirectory($directory){
        mkdir($directory, 0777, true);
    }



}