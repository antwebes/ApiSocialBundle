###Reuse


###Errors
We have a view to show error page 404.

The name module is : "Commons.Errors.TemplateNotFound"
Is in the folder: apps/commons/errors

How can I use it ?

```
requirejs(["apps/commons/errors/404"],
    function(ViewNotFound){
		App.body_container_main_Region.show(new ViewNotFound.NotFound());
});
```

###Commons list

We have a module called: Commons.List to generate commons list.
Is in the folder: apps/commons/list/listView

The CompositeView must to receive one template, but it can also get other parameters.




```
Module.Layout.region.show(new CommonsList.CompositeView({
    collection: your_collection,
    templateItemView: UserItemTemplate,
    tagName: 'ul class="list-unstyled"',
    //el margin y el border es para sobreescribir el estilo del ace-thumbnails
    tagNameItemView: 'li class="col-xs-6 col-lg-3" style="margin:5px 0px;border:0"',
    sizeImage: {width: '100px', height: '100px'},
    lazy: true
}));
```

* collection: Is a backbone.collection
* templateItemView: the template to each item of the collection
* tagName: You can customize the tagName, value by default is: ul class="row list-unstyled"
* tagNameItemView: You can customize the tagName to each ItemView. By default: li class="col-xs-3"
* sizeImage: you can send '50px' so the width and the height will be same value, or send {width: '100px', height: '150px'}
* lazy: if true lazy load works, and use the specific template


###Modal

In apps/commons/dialog/dialog_view
You can use the view to show a window modal to confirm a action.


Example of use:

```
require(["apps/commons/dialog/dialog_view"],function(DialogView){
    DialogView
        .confirm("Are you sure you don't wanna be more fan of this channel?", "Stop being fan")
        .then(function(confirmed){
            if(confirmed){
                //your code after the user confirm
            }
        });
});
```

###Template Loading

Sometimes it is useful to show the user a page load.

We have a template of loading.

Is in folder: apps/commons/list/listView.js
It view can get the parameters:
>title: now is obsolete, this param doesnt show in template
message: The message that user see in the template

Example of use:

```
define([
    'apps/commons/list/listView',
    ],
function(CommonsViews) {
	//create templating de loading
	var loadingView = new CommonsViews.Loading({
	    message: "Reading Channels."
	});
	layout_I_want.on("show", function(){
		layout_I_want.Region_I_want.show(loadingView);
	});
})
```
