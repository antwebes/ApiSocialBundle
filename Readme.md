ApiSocialBundle
==================

Symfony2 bundle for ChatBoilerplate project, It makes it easy to use API of api.chatea.net , through ChateaClientBundle and can list users and channels.

Install
-------

1) Include Bundle to AppKernel.php ( ApiSocialBundle )

    new Ant\Bundle\ApiSocialBundle\ApiSocialBundle(),
    
2) Include routing.yml

```
api_social:
    resource: "@ApiSocialBundle/Resources/config/routing.yml"
    prefix:   /
``` 
    
3) Ready to use

Configuration
-------------

You can define some filters to get users. Now you can set language, to get users only of a language.

Define in your parameters.yml the following parameter to modify language by default ( es ), example:

```
parameters:
    users.language: 'en'

```

You also can establish the limit of the last visits or voyeur to show in the app confing (```app/config/config.yml```) under the api_social. If you don't configure it the default value is 3.

```
api_social;
    visits_limit: 5
    voyeur_limit: 5
```

Also, you can specify the columns to order the user list with the users_orders option (by default no order is specified). Form example:

```
api_social:
    users_orders:
        lastLogin: desc
        hasProfilePhoto: desc
```

The order of the channels list can also be configured under the channels_orders option being the default order by fan desc. To specify for example to order by name asc you should put the following in the config:

```
api_social:
    channels_orders:
        lastLogin: asc
```

The minimum number of votes that a photo must have to appear in the top photos list can be configured under the ```minimum_votes_for_popular_photos``` option. Example:

```
api_social;
    minimum_votes_for_popular_photos: 5
```

Other parameters:
    realtime_endpoint: http://127.0.0.1:8000
