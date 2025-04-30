<?php
/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see \craft\config\GeneralConfig
 */

use craft\config\GeneralConfig;
use craft\helpers\App;

$isDev = strpos(strtolower(App::env('CRAFT_ENVIRONMENT')), 'dev') !== false;
$isProduction = strpos(strtolower(App::env('CRAFT_ENVIRONMENT')), 'prod') !== false || empty(App::env('CRAFT_ENVIRONMENT'));

return GeneralConfig::create()
    // Set the @webroot alias so the clear-caches command knows where to find CP resources
    ->aliases([
        '@web' => App::env('PRIMARY_SITE_URL')
    ])
    // Allow administrative changes
    ->allowAdminChanges(App::env('ALLOW_ADMIN_CHANGES') ?? $isDev ?? false)
    // Set the default week start day for date pickers (0 = Sunday, 1 = Monday, etc.)
    ->defaultWeekStartDay(0)
    // Enable Dev Mode (see https://craftcms.com/guides/what-dev-mode-does)
    ->devMode(App::env('DEV_MODE') ?? $isDev ?? false)
    // Disallow robots
    ->disallowRobots(App::env('DISALLOW_ROBOTS') ?? !$isProduction)
    // Path to error templates
    ->errorTemplatePrefix('_errors/_')
    // Prevent generated URLs from including "index.php"
    ->omitScriptNameInUrls()
    // Prevent user enumeration attacks
    ->preventUserEnumeration()
    // Set whether to send X-Powered-By headers
    ->sendPoweredByHeader(false)
;
