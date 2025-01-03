# New generation of administration

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jaydeepukani/craftable-pro.svg?style=flat-square)](https://packagist.org/packages/jaydeepukani/craftable-pro)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/jaydeepukani/craftable-pro/run-tests?label=tests)](https://github.com/jaydeepukani/craftable-pro/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/jaydeepukani/craftable-pro/Check%20&%20fix%20styling?label=code%20style)](https://github.com/jaydeepukani/craftable-pro/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jaydeepukani/craftable-pro.svg?style=flat-square)](https://packagist.org/packages/jaydeepukani/craftable-pro)

## Installation

You can install the package via composer:

```bash
composer require jaydeepukani/craftable-pro
```

Then you need to install the package (it will publish resources, migrations, configs, edit some configuration, ...) with:

```bash
php artisan craftable-pro:install
```

and finally you run:

```bash
npm install
npm run craftable-pro:dev
```

## Development

To develop this package, we recommend you to start on a fresh Laravel instance using Sail:

```bash
curl -s "https://laravel.build/craftable-pro-dev" | bash

cd craftable-pro-dev

./vendor/bin/sail up -d
```

and then run these commands:

```bash
composer require jaydeepukani/craftable-pro
./vendor/bin/sail artisan craftable-pro:install
./vendor/bin/sail artisan vendor:publish --tag=craftable-pro-seeders
./vendor/bin/sail artisan db:seed --class=DummyDataSeeder
./vendor/bin/sail npm install
./vendor/bin/sail npm run craftable-pro:dev
```

## Testing

```bash
composer test
```

