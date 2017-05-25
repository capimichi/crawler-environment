<?php

namespace Crawler\Downloader;

class SimpleDownloadManager extends DownloadManager
{
    /**
     * @param bool $raw If true, it return content without downloadManager headers
     *
     * @return string|null
     */
    public function getContent($raw = false)
    {
        $content = file_get_contents($this->url);
        $this->setHeaders($this->generatelHeaders($http_response_header));
        $jsonHeaders = json_encode($this->getHeaders());
        if (!$raw) {
            $content = "[downloadManager]{$jsonHeaders}[/downloadManager]" . $content;
        }
        return $content;
    }

}