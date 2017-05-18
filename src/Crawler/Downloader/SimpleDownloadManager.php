<?php
namespace Crawler\Downloader;

class SimpleDownloadManager extends DownloadManager
{
    /**
     * @return string|null
     */
    public function getContent()
    {
        $content = file_get_contents($this->url);
        $this->setHeaders($this->generatelHeaders($http_response_header));
        $jsonHeaders = json_encode($this->getHeaders());
        $content = "[downloadManager]{$jsonHeaders}[/downloadManager]" . $content;
        return $content;
    }

}