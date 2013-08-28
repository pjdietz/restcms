# RestCms

## Installation

### Clone

Begin by cloning RestCms to you server.

```bash
git clone https://github.com/pjdietz/restcms.git
```

Create a virtual host with the document root pointing to the `htdocs` directory inside the repository.

### Composer

RestCms uses [composer](http://getcomposer.org) to manage dependencies and autoloading. To download Composer:

```bash
curl -sS https://getcomposer.org/installer | php
```

Run composer:

```bash
php composer install
```

### Update Configuration Files

The `RestCmsConfig` namespace contains classes that you must create specific for your installation. You can find the files for this namespace in the `config/RestCmsConfig` directory.

Template versions of these files are provided in the repository. You will need to copy each of these, remove the `.template` from the end of the filename, and modify to suite your needs. Any `.php` files in this directory will be ignored by Git, so later pulls will not overwrite your configuration.
