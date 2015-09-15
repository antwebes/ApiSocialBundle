![Boilerplate](http://www.chateagratis.net/images/pachatea.png)

[![Build Status](https://travis-ci.org/backbone-boilerplate/backbone-boilerplate.png?branch=master)](https://travis-ci.org/backbone-boilerplate/backbone-boilerplate) [![Coverage Status](https://coveralls.io/repos/backbone-boilerplate/backbone-boilerplate/badge.png)](https://coveralls.io/r/backbone-boilerplate/backbone-boilerplate) [![Dependency Status](https://gemnasium.com/backbone-boilerplate/backbone-boilerplate.png)](https://gemnasium.com/backbone-boilerplate/backbone-boilerplate)

Backbone Social Dinamic
====================

## Documentation ##


- [Doc index about bundle: ](docs/index.md)

###Backbone Froms

Usamos la librería backbone forms.

Debemos tener en cuenta que en la entidad profile. El api devuelve los datos con underscore( _ ), y por el contrario los require con camelCase.

Para solucionar esto de forma rápida en la entidad profile.js, dentro del método sync se ha substituido las keys, recogemos con _, pero lo cambiamos a camelCase para que el api lo actualice correctamente.

@TODO
Revisar esto para ponerlo de forma correcta.

``` bash
c["sexualOrientation"] = c["sexual_orientation"];
delete c["sexual_orientation"];
c["youWant"] = c["you_want"];
delete c["you_want"];
```

## Commands ##
compress files with require.js

>r.js -o tools/build.js


It will create a dist file in www-public/main.js witch is the file to include to use the message app.
  
  
This command compress all files, and its dependences.

Upload files of www-built to s3:
>grunt s3

## License ##
Copyright © 2014 Antweb
Licensed under the MIT license.
