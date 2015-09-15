### Use the app

To use the app you need to user the www-built/main.js file and then with require.js require the App and start it with the
options. Example:

```
<script>
    window.user_id = 'userid';
    window.token = "access-token";
    window.api_endpoint = "url_of_api_endpoint";
    window.default_user_image_icon = "URL_OF_IMAGE_FOR_USERS_THAT_HAVE_NOT_IMAGES"

    var mail_app_options = {
        lang: "EN", the language of the user
        routes: {
            user_profile: 'route_to_user_profile' //for example /user/{username}-{id} The {VAR} are replaced by the VAR content of the object 
        }
    };
    
    require(["app"],function(App){
        App.vent.on('socialapp:loaded', function (){
            App.trigger('messages:index');
        });
        App.start(mail_app_options);
    });
</script>
```

There is also a way to starte the app to write a message to someone;

```
<script>
    require(["app"],function(App){
        App.vent.on('socialapp:loaded', function (){
            App.trigger('messages:compose', "USERNAME__TO_SEND_MAIL", true);
        });
        App.start(mail_app_options);
    });
</script>
```