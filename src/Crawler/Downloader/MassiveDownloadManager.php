<?php

namespace Crawler\Downloader;

class MassiveDownloadManager extends DownloadManager
{
    /**
     * @var array
     */
    protected $curlOptions;

    /**
     * DownloadManager constructor.
     * @param string $url
     * @param array $curlOptions
     */
    public function __construct($url, $curlOptions = array())
    {
        $defaultCurlOptions = array(
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER         => 1,
        );
        $options = array_merge($defaultCurlOptions, $curlOptions);
        $this->curlOptions = $options;
        parent::__construct($url);
    }

    /**
     * @return string|null
     * @throws \Exception
     */
    public function getContent()
    {
        $ch = curl_init($this->url);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        if ($error) {
            throw new \Exception("CURL ERROR: {$error}");
        }
        curl_close($ch);
        return $data;
    }

    /**
     * @param $ch
     * @return mixed
     */
    protected function buildCurl($ch)
    {
        foreach ($this->curlOptions as $key => $value) {
            curl_setopt($ch, $key, $value);
        }
        return $ch;
    }


}