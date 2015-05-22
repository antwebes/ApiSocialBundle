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