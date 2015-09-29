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
- In config file put:

```
api_social:
    visits_limit: 3 # default value is 3
    parameters_service: [parameters_in_yml|ant_web_site_parameters] # default value is parameters_in_yml
    parameters_service_yml:
        file_dir: <bundle_root_dir>/Resources/config # the directory with parameters file
        file_name: parameters.yml # the parameters file
```
    
Services:
---------

 This bundle define the service 'ant_parameters_service', this use in layout with format web_param.<parameter_name>.
 
 The service 'ant_parameters_service' load configuration from Resources/config/parameters.yml.
  
 If the bundle "website-parameters" is installed, and api_social_bundle has configurated the 
 parameter "parameters_service" as ant_web_site_parameters, this use the service 
 "ant_web_site_parameters.services.web_sites_parameters_service" of  "website-parameters"   
 
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
Other parameters:
    realtime_endpoint: http://127.0.0.1:8000