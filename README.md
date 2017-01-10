<h1 align="center">
    <img src="https://s-media-cache-ak0.pinimg.com/564x/eb/99/06/eb990621cef085814404e5e6964b95b7.jpg" width="230px" alt="Logo" />
    <img src="https://cloud.githubusercontent.com/assets/7669734/21817952/fcb209d8-d78b-11e6-8b84-06d076592be5.png" alt="Laravel Package Manager" />
</h1>

<p align="center">
<a href="https://travis-ci.org/Qafeen/Manager"><img src="https://travis-ci.org/Qafeen/Manager.svg?branch=master" /></a> 
<a href="https://packagist.org/packages/qafeen/manager"><img src="https://poser.pugx.org/qafeen/manager/v/stable" /></a> <a href="https://codeclimate.com/github/Qafeen/Manager"><img src="https://codeclimate.com/github/Qafeen/Manager/badges/gpa.svg" /></a> 
<a href="https://packagist.org/packages/qafeen/manager"><img src="https://poser.pugx.org/qafeen/manager/v/unstable" /></a> <a href="https://packagist.org/packages/qafeen/manager"><img src="https://poser.pugx.org/qafeen/manager/license" /></a> 
</p>


## Manager aims to automate package search and install functionality.
1. Register Service provider and facade.
2. Search relevant package.
3. Run migration.
4. Publish Resource and vue files (comming soon).
5. Run necessary command specific to a package (coming soon).
6. Uninstall package (comming soon).

## Installation:
Get manager package.
```bash
  composer require qafeen/manager
```

Register service provider. Possibilities are this will be your last time to do it manually.
```php
  Qafeen\Manager\ManagerServiceProvider::class,
```

## We are done!

Now let's install package by using our newly added manager. To search and add a package you need to run:
```bash
php artisan add passport
```

Manager will look for the package and give you the results:

![Manager search result if package not found by the given name](https://cloud.githubusercontent.com/assets/7669734/21749504/a17d7970-d5c5-11e6-9104-6edb414d0502.png)

Once you selected a package then composer will take care to download it and Manager will find service providers and facades to register. Also manager will look for migration files to run.

![Service providers and facades registration](https://cloud.githubusercontent.com/assets/7669734/21742305/de3ffcac-d511-11e6-96d9-4a9281cd736e.png)

If you are very specific to a package and know what you want to download then you can do it directly:
```bash
php artisan add zizaco/entrust:5.2.x-dev
```

## Notes: 
1. In the upcoming development, the package will also find files which need to be published to resource or assets directory.
2. Custom commands which need to be run after migration which will be handled by `manager.yml` file in root directory of the downloaded package.
3. Manager store your service providers and facades in different file `config/manager.php` and will take care to load it.
4. Stay tune...


<a name="Contribution"></a>
## Contribution
Just do it. You are welcome :)


<a name="Credits"></a>
## Credits

| Contributors           | Twitter   | Ask for Help | Contact / Hire  | Site            |
|------------------------|---------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------|-----------------|-----------------|
| [Mohammed Mudasir](https://github.com/Modelizer) (Creator) | @[md_mudasir](https://twitter.com/md_mudasir) | [![Get help on Codementor](https://cdn.codementor.io/badges/get_help_github.svg)](https://www.codementor.io/modelizer) | hello@mudasir.me | [http://mudasir.me](http://mudasir.me/) |

