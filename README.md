# Api BookCamp

Api live
-----
Api disponible à cette adresse : https://bookcamp.keepvibz.ovh/api  
Documentation : https://documenter.getpostman.com/view/17771184/2s93sc3sDu

Prérequis
------------

* PHP 8.1.0 ou plus

Installation
------------

```cmd
git clone https://github.com/charlottesaidi/NFSBookCamp/api.git
cd bookcamp_api/
composer install
```

Base de données
------------

__Création de la base de donnée__
```bash
symfony console doctrine:database:create
```  
```bash
symfony console doctrine:migrations:migrate
```  
__Fixtures(fake datas)__
```bash
symfony console doctrine:fixtures:load
```  

Utilisation locale
-----

**1** Lancer le serveur symfony avec cette commande :

```cmd
symfony serve
```

**2** Requêter l'api à l'URL donnée (<http://localhost:8000> par défaut).

[1]: https://symfony.com/doc/current/best_practices.html
[2]: https://symfony.com/doc/current/setup.html#technical-requirements
[3]: https://symfony.com/download
[4]: https://symfony.com/book
