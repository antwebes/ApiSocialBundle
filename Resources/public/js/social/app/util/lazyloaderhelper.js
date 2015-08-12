define([
    "jquery-lazyload"
    ],
    function(){
        var lazyloadHelper = function(view){
            var viewShowen = false;

            view.on('show', function(){
                view.$el.find('img.lazy').lazyload({"effect": "fadeIn"});
                view.on("after:item:added", function(subView){
                    subView.$el.find('img.lazy').lazyload({"effect": "fadeIn"});

                    subView.on('show', function(){
                        subView.$el.find('img.lazy').lazyload({"effect": "fadeIn"});
                        viewShowen = true;
                    });
                });
            });

        };

        return lazyloadHelper;
    });