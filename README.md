# Sculpin Less Bundle

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

[Sculpin](http://sculpin.io) bundle that integrates the [oyejorge/less.php](https://github.com/oyejorge/less.php) LESS processor.

Each `*.less` file is parsed to CSS. 
If the parser generated any CSS output the file is renamend to `*.css` in place. 
If the parser did no generate any valid CSS output the `*.less` file will be ignored. 

## Future scope
This is a POC implementation. In the future this may be extended to include advaced configurations to have a separate output directory for the generated `*.css` files as well as compression for example.
Eventually this may result in a CSS processor bundle that supports multiple processors like `SASS` as well.  

Please create a issue on Github if you have any ideas. All contributions are welcomed.


## Installation

* Add the following to your `sculpin.json` file:

```json
{
    "require": {
        "bcremer/sculpin-less-bundle": "~0.1"
    }
}
```

* Run `sculpin update`.
* Add the bundle to your kernel `app/SculpinKernel.php`:

```php
<?php

class SculpinKernel extends \Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel
{
    protected function getAdditionalSculpinBundles()
    {
        return array(
            'Bcremer\Sculpin\Bundle\LessBundle\SculpinLessBundle'
        );
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

By default the `extensions` whitelist is used. If the `files` whitelist is set it takes precedence and all other LESS files are not converted.

To ignore non matching LESS files the sculping `ignore` configuration can be used:

```yaml
# app/config/sculpin_kernel.yml
sculpin:
    ignore: ["assets/css/_imports/"]
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
