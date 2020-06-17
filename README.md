# Mezzio/Prismic Skeleton

![PHPUnit Test Suite](https://github.com/netglue/primo-skeleton/workflows/PHPUnit%20Test%20Suite/badge.svg)

You can see this app running in its default state _(Including a whole load of documentation)_ at _TBC_

This skeleton is opinionated in order to supply quite a bit of functionality out of the box without too much complexity.

Importantly, it's the product of the [Mezzio Skeleton](https://github.com/mezzio/mezzio-skeleton), and using it is contingent on understanding [Prismic](https://prismic.io) - a headless CMS service and some familiarity with the following dependencies will be very useful:

* [netglue/prismic-client](https://github.com/netglue/prismic-client)
* [netglue/primo](https://github.com/netglue/primo)

<table>
    <tr>
        <th align="right">Templating</th>
        <td>laminas-view</td>
    </tr>
    <tr>
        <th align="right">DI&nbsp;Container</th>
        <td>laminas-servicemanager</td>
    </tr>
    <tr>
        <th align="right">Routing</th>
        <td>fastroute</td>
    </tr>
    <tr>
        <th align="right">PSR Event Dispatcher</th>
        <td>phly/event-dispatcher</td>
    </tr>
    <tr>
        <th align="right">PSR Cache</th>
        <td>laminas-cache</td>
    </tr>
    <tr>
        <th align="right">PSR Logger</th>
        <td>monolog</td>
    </tr>
    <tr>
        <th align="right">PSR HTTP Client</th>
        <td>httplug</td>
    </tr>
</table>

## Rudimentary Frontend Asset Compilation

After running `npm install`, if `./node_modules/.bin` is on your `$PATH`, you'll be able to issue `npm-watch`, otherwise, put it on your `$PATH` or type the full path to npm-watch.

If you look at `package.json`, and the resources in `./frontend` you'll see that scss, js and prismic document type definitions are compiled by npm scripts.

Styles are intentionally empty and intended to be a starting point for a completely new website. Similarly JS is just an iif triggered on load.

Prismic document types are compiled with [netglue/prismic-cli](https://github.com/netglue/prismic-cli) via node and there's a `page` type setup that can be used as starting point to build your own types. Take a look in `./config/autoload/prismic-cli.global.php` for relevant configuration of the tool.


### To disable development mode

```bash
$ composer development-disable
```

### Development mode status

```bash
$ composer development-status
```

## Configuration caching

By default, the skeleton will create a configuration cache in
`data/config-cache.php`. When in development mode, the configuration cache is
disabled, and switching in and out of development mode will remove the
configuration cache.

You may need to clear the configuration cache in production when deploying if
you deploy to the same directory. You may do so using the following:

```bash
$ composer clear-config-cache
```

You may also change the location of the configuration cache itself by editing
the `config/config.php` file and changing the `config_cache_path` entry of the
local `$cacheConfig` variable.
