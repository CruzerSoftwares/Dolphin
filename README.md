# Dolphin ORM & Query Builder

[![Donate](https://img.shields.io/badge/donate-patreon-yellow.svg)](https://www.patreon.com/join/CruzerSoftwares)
[![GitHub issues](https://img.shields.io/github/issues/CruzerSoftwares/Dolphin.svg)](https://github.com/CruzerSoftwares/Dolphin/issues)
![PHP from Packagist](https://img.shields.io/packagist/php-v/cruzer/dolphin.svg)
![Packagist Version](https://img.shields.io/packagist/v/cruzer/dolphin.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/CruzerSoftwares/Dolphin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/CruzerSoftwares/Dolphin/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/CruzerSoftwares/Dolphin/badges/build.png?b=master)](https://scrutinizer-ci.com/g/CruzerSoftwares/Dolphin/build-status/master)
![Packagist](https://img.shields.io/packagist/dt/cruzer/dolphin.svg)
[![Code Coverage](https://scrutinizer-ci.com/g/CruzerSoftwares/Dolphin/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/CruzerSoftwares/Dolphin/?branch=master)
[![GitHub license](https://img.shields.io/github/license/CruzerSoftwares/Dolphin.svg)](https://github.com/CruzerSoftwares/Dolphin/blob/master/LICENSE)

A lightweight Database Query builder and ORM for PHP 7 without any external dependency(only PDO is required).
API looks quite same as of Laravel's Eloquent but without the complexity.

## Features
- Easy to use and rememberable syntax
- Uses prepared statements
- Method chaining
- Support multiple database driver through PDO
- Support join(), leftJoin(), rightJoin(), crossJoin()
- Supprts where(), whereIn(), whereNotIn(), whereNull(), whereNotNull(), whereRaw()
- Support groupBy(), having(), orderBy()
- Supports offset() and limit()
- Supports the shortcuts for retreiving data such as
  first(), last(), min(), max(), avg()
- Supports count()
- insert()
- update()
- delete()
- truncate()

## Not yet Supported
- union()
- exists()
- orWhere()
- events support
- transactions support

## Tests
Tests are still pending.

## Inspirations
Initialy, I created it for the CruzerMini to interact with the database because I was looking for a good library like Laravel's Eloquent but with small footprint. I tried severals but found nothing that has the nice syntax as Eloquent. Thats why it inspired me to create it. 


## Contributing
Please see [CONTRIBUTING](https://github.com/CruzerSoftwares/Dolphin/blob/master/CONTRIBUTING.md) for details.


## Security
If you discover any security related issues, please email [support@cruzersoftwares.com](mailto:support@cruzersoftwares.com) instead of using the issue tracker.


## Credits
- [RN Kushwaha](https://github.com/RNKushwaha022)


## License
The MIT License (MIT). Please see [License File](https://github.com/CruzerSoftwares/Dolphin/blob/master/LICENSE) for more information.


## Buy me a coffee
<a href='https://www.patreon.com/join/CruzerSoftwares'><img alt='Become a Patron' src='https://s3.amazonaws.com/patreon_public_assets/toolbox/patreon.png' border='0' width='200px' ></a>

