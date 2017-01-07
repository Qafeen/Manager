<p align="center">
  <img src="https://s-media-cache-ak0.pinimg.com/564x/eb/99/06/eb990621cef085814404e5e6964b95b7.jpg" width="230px" />
</p>

<h1 align="center">Laravel Package Manager</h1>

## Manager aims to automate package search and install functionality.
1. Register Service provider and facade.
2. Search relevant package.
3. Run migration (coming soon).
4. Run necessary command specific to a package (coming soon).
5. Uninstall package (comming soon).

## Installation:
Get manager package.
```bash
  composer require qafeen/manager:dev-master
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

![Manager search result if package not found by the given name](https://cloud.githubusercontent.com/assets/7669734/21744445/e128995e-d53b-11e6-9131-49da0ea65fd7.png)

Once you selected a package then composer will take care to download it and Manager will find service providers and facades to register.

![Service providers and facades registration](https://cloud.githubusercontent.com/assets/7669734/21742305/de3ffcac-d511-11e6-96d9-4a9281cd736e.png)

If you are very specific to a package and know what you want to download then you can do it directly:
```bash
php artisan add zizaco/entrust:5.2.x-dev
```

## Notes: 
1. In the upcoming development, the package will also find migration files, files which need to be published to resource or assets directory.
2. Custom commands which need to be run after migration which will be handled by `manager.yml` file in root directory of the downloaded package.
3. Manager will store your service providers and facades in deferent file `config/manager.php` and will take care to load it.
4. Stay tune...


<a name="Contribution"></a>
## Contribution
Just do it. You are welcome :)


<a name="Credits"></a>
## Credits

| Contributors           | Twitter   | Ask for Help | Contact / Hire  | Site            |
|------------------------|---------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------|-----------------|-----------------|
| [Mohammed Mudasir](https://github.com/Modelizer) (Creator) | @[md_mudasir](https://twitter.com/md_mudasir) | [![Get help on Codementor](https://cdn.codementor.io/badges/get_help_github.svg)](https://www.codementor.io/modelizer) | hello@mudasir.me | [http://mudasir.me](http://mudasir.me/) |

