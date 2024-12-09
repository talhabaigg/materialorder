# Filament - Material Order Application


## Features

- 🚀 Quick, clean application with Invoice style order page.
- 🧑‍💻 Support for FCM messages
- SPA for best user experience

## Getting Started

Start by creating the project using Composer and configuring the `.env` file:

```sh
composer create-project log1x/filament-starter:dev-main
cd filament-starter
```

After `.env` is configured, you can proceed to migrate & seed the database:

```sh
php artisan migrate:fresh --seed
```

Once the database is seeded, you can login at `/admin` using the default admin user:

```yaml
Username: admin
Password: admin
```

### Build Assets

The project assets are compiled using Vite. This can be done by installing the dependencies and running the build command with Yarn.

```sh
yarn install
yarn build
```

## Plugins Used

The following [Filament plugins](https://filamentphp.com/plugins) come fully implemented and configured out of the box:

| **Plugin**                                                          | **Description**                                    | **Author**                                      |
| :------------------------------------------------------------------ | :------------------------------------------------- | :---------------------------------------------- |
| [Curator](https://github.com/awcodes/filament-curator)              | A beautiful media library.                         | [awcodes](https://github.com/awcodes)           |
| [Gravatar](https://github.com/awcodes/filament-gravatar)            | Easy avatar integration powered by Gravatar.       | [awcodes](https://github.com/awcodes)           |
| [Exceptions](https://github.com/bezhansalleh/filament-exceptions)   | A simple but powerful Exception viewer.            | [bezhansalleh](https://github.com/bezhansalleh) |
| [Jobs Monitor](https://github.com/croustibat/filament-jobs-monitor) | Easily monitor background jobs and their progress. | [croustibat](https://github.com/croustibat)     |
| [Breezy](https://github.com/jeffgreco13/filament-breezy)            | Customizable user profile pages and 2FA support.   | [jeffgreco13](https://github.com/jeffgreco13)   |
| [Peek](https://github.com/pboivin/filament-peek)                    | Quick & efficient front-end previews of resources. | [pboivin](https://github.com/pboivin)           |
| [Logger](https://github.com/z3d0x/filament-logger)                  | Zero-config resource activity logging.             | [z3d0x](https://github.com/z3d0x)               |



## License

Filament Starter is provided under the [MIT License](LICENSE.md).
