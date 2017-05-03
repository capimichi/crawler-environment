<?php
use Crawler\Downloader\CacheDownloadManager;
use Crawler\Downloader\SimpleDownloadManager;

require_once "vendor/autoload.php";
require_once "src/autoload.php";

$products = [];
$productUrls = [];
$scrapedPageUrls = [];
$scrapedProductUrls = [];
$start = "https://www.idosrl.net/shop";
scrapePage($start);
$productUrls = array_unique($productUrls);
foreach ($productUrls as $productUrl) {
    scrapeProduct($productUrl);
}
echo json_encode($products);

function scrapePage($url)
{
    global $productUrls, $scrapedPageUrls;
    if (!in_array($url, $scrapedPageUrls)) {
        array_push($scrapedPageUrls, $url);
        $cdm = new CacheDownloadManager(new SimpleDownloadManager($url), "var/cache/");
        $xpath = $cdm->getXpath();

        // Scan pages urls
        $domPageUrls = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' page-numbers ')]//a/@href");
        foreach ($domPageUrls as $domPageUrl) {
            scrapePage($domPageUrl->nodeValue);
        }

        // Fetch products urls
        $domProductUrls = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' product-grid-item ')]//h3//a/@href");
        foreach ($domProductUrls as $domProductUrl) {
            array_push($productUrls, $domProductUrl->nodeValue);
        }
    }
}

function scrapeProduct($url)
{
    global $scrapedProductUrls, $products;
    if (!in_array($url, $scrapedProductUrls)) {
        $cdm = new CacheDownloadManager(new SimpleDownloadManager($url), "var/cache/");
        $dom = $cdm->getDom();
        $xpath = $cdm->getXpath();

        // Title
        $domTitle = $dom->getElementsByTagName("h1");
        $domTitle = $domTitle[0];
        $title = $domTitle->nodeValue;

        // Price
        $domPrice = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' price ')]");
        $domPrice = $domPrice->item(0);
        $price = $domPrice->nodeValue;
        $price = trim($price);
        if (preg_match("/(.*?)-/is", $price, $lowPrice)) {
            $price = $lowPrice[1];
        }
        $price = preg_replace("/[^0-9,.]/", "", $price);

        // Description
        $domDescription = $dom->getElementById("tab-description");
        $description = $domDescription->nodeValue;
        $description = trim($description);

        // Tags
        $tags = [];
        $descriptionForTags = $description;
        $descriptionForTags = str_replace(".", ". ", $descriptionForTags);
        $descriptionForTags = str_replace("  ", " ", $descriptionForTags);
        foreach (explode(" ", $descriptionForTags) as $tag) {
            $tag = trim($tag);
            $tag = rtrim($tag, ".,')(");
            $tag = ltrim($tag, ".,')(");
            $tag = preg_replace('/[^A-Za-z0-9]/', '', $tag);
            $tag = strtolower($tag);
            if (strlen($tag) > 3) {
                array_push($tags, $tag);
            }
        }

        // Categories
        $domCategories = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' posted_in ')]//a");
        $categories = [];
        foreach ($domCategories as $domCategory) {
            array_push($categories, $domCategory->nodeValue);
        }

        // Image
        $domImage = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' product-images ')]//figure//img/@src");
        $image = $domImage->item(0)->nodeValue;

        $product = array(
            "slug"        => md5($url),
            "title"       => $title,
            "price"       => $price,
            "description" => $description,
            "tags"        => $tags,
            "categories"  => $categories,
            "image"       => $image,
        );
        array_push($products, $product);
    }
}