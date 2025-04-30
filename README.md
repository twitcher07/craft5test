# Welcome to the Craft 5 with Vite.

# Table of Contents
---
1. [Requirements](#installation-requirements)
2. [Build Tools & Assets](#build-tools-&-assets)
3. [Getting Started](#getting-started)
4. [Build Commands](#build-commands)


# Installation requirements
---
* Node.js
* NPM
* Composer
* Vite


# Tools & Frontend Assets
---
## What's included:
* Craft CMS v5 - Craft is a flexible, user-friendly CMS for creating custom digital experiences on the web and beyond. [Documentation](https://craftcms.com/docs/5.x/)
* Vite - [Documentation](https://vitejs.dev/)

## Frontend assets:
* Alpine.js v3 - [Documentation](https://alpinejs.dev)
* Tailwind CSS v3 - [Documentation](https://tailwindcss.com/docs)
* Vanilla Lazyload - [Documentation](https://github.com/verlok/vanilla-lazyload/tree/17.1.3)
* GSAP - [Documentation](https://gsap.com/docs/v3/)
* fitvids - [Documentation](https://github.com/rosszurowski/fitvids#readme)

# Getting Started
---
## Configuring enviroment
Make sure .env variables are updated with the appropriate values. They are used for all frontend build commands. Below are the values used by the frontend build commands.

### `ENVIRONMENT`
Is used to set whether development or production version of files are generated. Expected values are `development`, `dev`, `staging`, or `production`.

#### Important note about sites behind HTTP authentication:
> Because this site uses the vite plugin, if your site is behind HTTP authentication - you will have to update the `HTTP_AUTHENTICATION_USER` and `HTTP_AUTHENTICATION_PASSWORD` in the .env file to make sure pulling in vite build files is working properly.

### `PRIMARY_SITE_URL`
Must be set for Vite plugin and Craft to work properly.

## Configuring paths
### WARNING: Whatever is inside the folder `./web/dist` will be deleted whenever you run `npm run build`. This is to prevent caching of old build files. Be careful not to put anything else inside this folder.


## Setting up purging of TailwindCSS 
Make sure in your `package.json` that `"purgeFiles": []` contains an array to any files that contain tailwindcss classes. To learn more go to tailwindcss documentation about (optimizing for production)[https://tailwindcss.com/docs/optimizing-for-production].
 
## Customizing Webpack
Set any custom configuration (e.g. new entry points or other settings) in `vite.config.js`.

## Favicons
Favicon generation uses Real Favicon Generator. Edit `./faviconConfig.json` to be valid Real [Favicon Generator configuration](https://realfavicongenerator.net/api/non_interactive_api). 


# Build Commands
---
## `npm run build`
Builds production frontend files using vite.

## `npm run dev`
Builds development frontend files and watches for changes. In order for this to work properly, make sure the craft environment is set to `dev` or `development`.

## `npm run favicon`
Generates favicon information using real favicon generator.

## `npm run favicon-update`
Checks real favicon generator for any updates.
