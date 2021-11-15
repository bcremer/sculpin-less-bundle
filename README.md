# Sculpin Less Bundle

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

[Sculpin](http://sculpin.io) bundle that integrates the [wikimedia/less.php](https://github.com/wikimedia/less.php) LESS processor.

Each `*.less` file is parsed to CSS. 
If the parser generated any CSS output the file is renamed to `*.css` in place. 
If the parser did not generate any valid CSS output the `*.less` file will be ignored. 

## Future scope
This is a POC implementation.
In the future this may be extended to include advanced configurations to have a separate output directory for the generated `*.css` files as well as compression for example.
Eventually this may result in a CSS processor bundle that supports multiple processors like `SASS` as well.  

Please create an issue on GitHub if you have any ideas. All contributions are welcomed.


## Installation

* Add the following to your `sculpin.json` file:

```json
{
    "require": {
        "bcremer/sculpin-less-bundle": "~0.2"
    }
}
```

* Run `sculpin update`.
* Add the bundle to your kernel `app/SculpinKernel.php`:

```php
<?php

use Bcremer\Sculpin\Bundle\LessBundle\SculpinLessBundle;

class SculpinKernel extends \Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel
{
    protected function getAdditionalSculpinBundles()
    {
        return [
            SculpinLessBundle::class
        ];
    }
}
```

## Configuration

```yaml
# app/config/sculpin_kernel.yml
sculpin_less:
    extensions: ["less"]
    files: ["assets/css/basic.less"]
```

By default, the `extensions` whitelist is used.
If the `files` whitelist is set it takes precedence and all other LESS files are not converted.

To ignore non-matching LESS files the sculping `ignore` configuration can be used:

```yaml
# app/config/sculpin_kernel.yml
sculpin:
    ignore: ["assets/css/_imports/"]
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
