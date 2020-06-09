# Mezzio/Prismic Skeleton

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
