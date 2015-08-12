define(["apps/messages/messages_app"], function(MessagesApp){
    describe("MessagesApp.Router routes", function(){
        beforeEach(function(){
            this.routeIndexSpy = sinon.spy();
            this.routeSentSpy = sinon.spy();
            this.routeShowThreadSpy = sinon.spy();
            this.routeComposeSpy = sinon.spy();

            var API = {
                index: this.routeIndexSpy,
                sent: this.routeSentSpy,
                showThread: this.routeShowThreadSpy,
                compose: this.routeComposeSpy
            };
            
            this.router = new MessagesApp.app.MessagesApp.Router({ controller: API });
            
            try {
              Backbone.history.start({silent:true, pushState:true});
            } catch(e) {}
            this.router.navigate('social-dinamic/app/SpecRunner.html');
        });

        afterEach(function(){
            this.router.navigate('social-dinamic/app/SpecRunner.html');
        });

        it("fires the index action with the /messsage/ route", function(){
            this.router.navigate("/me/messages/", true);
            expect(this.routeIndexSpy).toHaveBeenCalled();
        });

        it("fires the sent action with the /messsage/sent route", function(){
            this.router.navigate("/me/messages/sent", true);
            expect(this.routeSentSpy).toHaveBeenCalled();
        });

        it("fires the compose action with the /messsage/compose route", function(){
            this.router.navigate("/me/messages/compose", true);
            expect(this.routeComposeSpy).toHaveBeenCalled();
        });

        it("fires the showThread action with the /messsage/ID_OF_THREAD route", function(){
            this.router.navigate("/me/messages/1", true);
            expect(this.routeShowThreadSpy).toHaveBeenCalled();
            expect(this.routeShowThreadSpy).toHaveBeenCalledWith("1");
        });
    });

    return {
        name: "messages_router_spec"
    };
});