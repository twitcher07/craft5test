<?php
namespace modules;

use Craft;
use craft\helpers\App;
use craft\helpers\UrlHelper;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use yii\caching\ChainedDependency;
use yii\caching\FileDependency;
use yii\caching\TagDependency;

class Helpers
{
    // Constants
    // =========================================================================

    const CACHE_KEY = 'vite';
    const CACHE_TAG = 'vite';

    const DEVMODE_CACHE_DURATION = 1;
    
    public static function fetch(string $pathOrUrl, callable $callback = null, string $cacheKeySuffix = '')
    {
        $pathOrUrl = (string)Craft::parseEnv($pathOrUrl);
        // Create the dependency tags
        $dependency = new TagDependency([
            'tags' => [
                self::CACHE_TAG . $cacheKeySuffix,
                self::CACHE_TAG . $cacheKeySuffix . $pathOrUrl,
            ],
        ]);
        // If this is a file path such as for the `manifest.json`, add a FileDependency so it's cache bust if the file changes
        if (!UrlHelper::isAbsoluteUrl($pathOrUrl)) {
            $dependency = new ChainedDependency([
                'dependencies' => [
                    new FileDependency([
                        'fileName' => $pathOrUrl
                    ]),
                    $dependency
                ]
            ]);
        }
        // Set the cache duration based on devMode
        $cacheDuration = Craft::$app->getConfig()->getGeneral()->devMode
            ? self::DEVMODE_CACHE_DURATION
            : null;
        // Get the result from the cache, or parse the file
        $cache = Craft::$app->getCache();
        return $cache->getOrSet(
            self::CACHE_KEY . $cacheKeySuffix . $pathOrUrl,
            function () use ($pathOrUrl, $callback) {
                $contents = null;
                if (UrlHelper::isAbsoluteUrl($pathOrUrl)) {
                    $response = self::fetchResponse($pathOrUrl);
                    if ($response && $response->getStatusCode() === 200) {
                        $contents = $response->getBody()->getContents();
                    }
                } else {
                    $contents = @file_get_contents($pathOrUrl);
                }
                if ($contents && $callback) {
                    $contents = $callback($contents);
                }

                return $contents;
            },
            $cacheDuration,
            $dependency
        );
    }

    /**
     * Return a Guzzle ResponseInterface for the passed in $url
     *
     * @param string $url
     * @return ResponseInterface|null
     */
    public static function fetchResponse(string $url, array $guzzleOptions = []): ?ResponseInterface
    {
        $response = null;
        $clientOptions = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::CONNECT_TIMEOUT => 3,
            RequestOptions::VERIFY => false,
            RequestOptions::TIMEOUT => 5,
        ];
        $clientOptions = array_merge($clientOptions, $guzzleOptions);
        $client = new Client($clientOptions);
        $auth = null;

        if (App::env('HTTP_AUTHENTICATION_USER') && App::env('HTTP_AUTHENTICATION_PASSWORD')) {
            $auth = [(string)App::env('HTTP_AUTHENTICATION_USER'), (string)App::env('HTTP_AUTHENTICATION_PASSWORD')];
        }
        
        try {
            $response = $client->request('GET', $url, [
                RequestOptions::HEADERS => [
                    'Accept' => '*/*',
                ],
                RequestOptions::AUTH => $auth
            ]);
        } catch (Throwable $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }

        return $response;
    }
}