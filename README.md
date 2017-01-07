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

Now let's install package by using our newly added manager. To search and add a package in your project then you need to run:
```bash
php artisan add passport
```

Manager will look for the package and give you the result:

![Manager search result if package not found by the given name](https://cloud.githubusercontent.com/assets/7669734/21742279/50ee9516-d511-11e6-8444-c938c0951769.png)

Once you selected your package like I selected `laravel/passport` then it will download it for you and will find service providers and facades to register.

![Service providers and facades registration](https://cloud.githubusercontent.com/assets/7669734/21742305/de3ffcac-d511-11e6-96d9-4a9281cd736e.png)

If you are very specific to a package and know what you want to download then you can do it directly:
```bash
php artisan add zizaco/entrust:5.2.x-dev
```

## Notes: 
In up coming development package will also find migration files, files which need to be publish to resource or assets directory. Also custom commands which need to be run after migration which will be handled by `manager.yml` file in root directory of downloaded package. Stay tune...


<a name="Contribution"></a>
## Contribution
Just do it. You are welcome :)


<a name="Credits"></a>
## Credits

| Contributors           | Twitter   | Ask for Help | Contact / Hire  | Site            |
|------------------------|---------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------|-----------------|-----------------|
| [Mohammed Mudasir](https://github.com/Modelizer) (Creator) | @[md_mudasir](https://twitter.com/md_mudasir) | [![Get help on Codementor](https://cdn.codementor.io/badges/get_help_github.svg)](https://www.codementor.io/modelizer) | hello@mudasir.me | [http://mudasir.me](http://mudasir.me/) |

