# apprenticeship-report

A tool for generating my apprenticeship report.

## Installation

Run composer to install the php (backend) dependencies:

```bash
composer install
```

Run npm to install the node (gulp and frontend) dependencies:

```bash
npm install
```

To view the available gulp tasks, run the following command:

```bash
gulp --tasks
```

The gulpfile allows an `--env` flag to change the build behaviour (minify).

Create a database and import the creation script from `./asset/data/schema/entry.sql`.

Add the `CHROME_HOME` environment variable as part of your `PATH`. If there is no `CHROME_HOME` on your system, create
it and point it to the installation directory of your local chrome installation.

Make sure you place the logo of the company at `./asset/image/company.png`.

Adapt the configuration inside `./app/configuration.php` to your needs.

Also create the file `./app/print_service_extra_data.php` with the following content:

```php
<?php

return [
    // key => value
];

```

The key value pairs will be pushed into the print template (`./view/print/index.html.twig`) as `extra_data`.

For convenience add a new hosts entry pointing `apprenticeship-report.local` to `127.0.0.1`.

You can run the application on your internal PHP web server using the following command:

```bash
php -S apprenticeship-report.local:80 -t ./public ./public/index.php
```

## Usage

> **Print:**
> ```bash
> chrome --headless --disable-gpu --print-to-pdf=<PROJECT_DIRECTORY>/print-test.pdf http://apprenticeship-report.local/print
> ```
> The above command will generate a pdf file at `<PROJECT_DIRECTORY>/print-test.pdf`.

TODO

## License

It's MIT
