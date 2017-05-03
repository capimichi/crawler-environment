<?php
namespace Crawler\Downloader;

class SimpleDownloadManager extends DownloadManager
{
    /**
     * @return string|null
     */
    public function getContent()
    {
        return file_get_contents($this->url);
    }

}