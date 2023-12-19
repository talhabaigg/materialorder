# Filament Starter

This is my preferred defaults to quickly bootstrap a project utilizing the TALL stack with Filament for the backend.

## Requirements

Make sure all dependencies have been installed before moving on:

- [PHP](https://secure.php.net/manual/en/install.php) >= 8.1
- [Composer](https://getcomposer.org/download/)
- [Node.js](http://nodejs.org/) >= 18
- [Yarn](https://yarnpkg.com/en/docs/install)

## Getting Started

Start by cloning the Filament Starter repository and initializing the `.env` file:

```sh
$ git clone --depth=1 git@github.com:log1x/filament-starter.git
$ cd filament-starter
$ cp .env.example .env
$ composer install
$ php artisan key:generate
```

### Build Assets

The project assets are compiled using Vite. This can be done by installing the dependencies and running the build command with Yarn.

```sh
$ yarn install
$ yarn build
```

## Bug Reports

If you discover a bug in Filament Starter, please [open an issue](https://github.com/log1x/filament-starter/issues).

## Contributing

Contributing whether it be through PRs, reporting an issue, or suggesting an idea is encouraged and appreciated.

## License

This project and it's codebase is proprietary and not available for public use.