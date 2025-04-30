<?php
namespace modules;

use modules\Helpers;

use Craft;
use craft\helpers\Json;
use craft\helpers\App;
use craft\services\Plugins;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use nystudio107\vite\Vite;
use craft\helpers\Template;
use nystudio107\pluginvite\helpers\ManifestHelper;
use nystudio107\pluginvite\helpers\FileHelper as ViteFileHelper;


class TwigExtensions extends AbstractExtension
{

    public static $craftVite;

    function __construct()
    {

        if (Craft::$app->plugins->getPlugin('vite') && Craft::$app->plugins->getPlugin('vite')->isInstalled) {
            self::$craftVite = Craft::$app->plugins->getPlugin('vite')->getVite();
        }

    }

    public function getFunctions(): array
    {
        
        return [
            // First argument is the function name; second is a callable:
            new TwigFunction('inline', [ new class(self::$craftVite) {

                function __construct($vite) {
                    $this->craftVite = $vite;
                    
                }

                function init($filename) {

                    if (!$this->craftVite) {
                        return Template::raw('');
                    }

                    if ($this->craftVite->devServerRunning()) {
                        Craft::setAlias('@inlineAssetPath', '@webroot/dist/inline-assets');

                        $inlineFile = Helpers::fetch('@webroot/dist/inline-assets/' . $filename);

                        return Template::raw($inlineFile);
                    }
                    
                    $inlineFileManifestPath = $this->craftVite->manifestAsset('src/inline-assets/' . $filename);

                    $inlineFile = Helpers::fetch('@webroot/' . $inlineFileManifestPath);

                    return Template::raw($inlineFile);
                }

            }, 'init']),
            new TwigFunction('svgSprite', [ 
                new class(self::$craftVite) 
                {

                    function __construct($vite) 
                    {
                        $this->craftVite = $vite;

                        if ($this->craftVite) {
                            // Grab the manifest
                            $this->manifestPath = (string)App::parseEnv($this->craftVite->manifestPath);
                            $this->manifest = (array)Helpers::fetch($this->manifestPath, [Json::class, 'decodeIfJson']);
                            // If no manifest file is found, log it
                            if ($this->manifest === null) {
                                Craft::error('Manifest not found at ' . $this->manifestPath, __METHOD__);
                            }
                        }
                    }

                    function init($spriteFilename) 
                    {
                        $svgSprite = '';

                        if (empty($this->manifest) || empty($spriteFilename)) {
                            return Template::raw($svgSprite);
                        }

                        foreach ($this->manifest as $manifestKey => $entry) {

                            if (preg_match('/^' . $spriteFilename . '.svg$/', $manifestKey)) {

                                if (isset($entry['file'])) {
                                    $pathToSvg = ViteFileHelper::createUrl($this->craftVite->serverPublic, $entry['file']);
                                    $svgContent = Helpers::fetch('@webroot' . $pathToSvg);
                                    $svgSprite .= $svgContent;
                                }
                            }

                        }

                        return Template::raw($svgSprite);
                    }

                }, 
                'init'
            ]),
            new TwigFunction('log', [Craft::class, 'info']),
        ];
    }

}