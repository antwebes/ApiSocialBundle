var refresh = 5;
var refresh_time = parseInt(localStorage.getItem("connection_refresh")) | 0;
var debug_chat_bride = true;

if (window.addEventListener) {
    // Normal browsers
    window.addEventListener("storage", handlerChatNotify, false);
} else {
    // for IE (why make your life more difficult)
    window.attachEvent("onstorage", handlerChatNotify);
};

function handlerChatNotify(e) {
    if(debug_chat_bride) console.log('Received data: ' + localStorage.getItem('data'));

    var ev = JSON.parse(localStorage.getItem('data'));
    switch (ev.event) {
        case "connected":
            refresh_time = parseInt(ev.arguments);
            localStorage.setItem("connection_refresh", refresh_time);
            break;
        case "newprivate":
            $.notify({
                message: ev.arguments + " te ha enviado un mensaje privado en la página de chat.",
                enter: 'animated zoomInDown',
                exit: 'animated zoomOutUp'
            });
            break;
    }
}

function isConnected() {
    var current = Math.floor(Date.now() / 1000);
    var time = parseInt(refresh_time);

    if(debug_chat_bride) console.log(time + refresh +">" + current );

    if(time +refresh > current ) {
        return true;
    } else  return false;
}

function disableBtn() {
    $("#chatBtnConnect").attr('disabled','disabled');
    $("#chatBtnConnect").addClass("btn-success");
}

function newPrivateButton(target, url, url_message) {
    var connected = isConnected();
    $("#chatBtnConnect").text('Chatear con ' +target );
    setTimeout(function() {
        $("#chatBtnConnect").removeAttr('disabled');
        $("#chatBtnConnect").addClass("btn-highlight");

    }, 2000);


    $("#chatBtnConnect").click(function() {
        if( $("#chatBtnConnect").data("offline") === true) {
            window.location.href = url_message;
            return;
        }

        if(isConnected()) {

            localStorage.setItem("data", '{"event": "openPrivate", "arguments": "'+target+'"}');

            $.notify({
                message: " Abriendo privado con " +target + " dirígite a la pestaña de chat.",
                enter: 'animated zoomInDown',
                exit: 'animated zoomOutUp'
            });
            disableBtn();
        } else {
            var checkConnection = setInterval(function () {
                if(isConnected()) {
                    setTimeout(function () {
                        localStorage.setItem("data", '{"event": "openPrivate", "arguments": "'+target+'"}');
                        clearInterval(checkConnection);
                    }, 5000);
                }
            }, 5000);

            if(url == undefined) {
                window.open(window.chat_url+ "?target=" + target);
            } else {
                window.open(url);
            }

            disableBtn();
        }
    });


}

function newChatButton(target, url) {
    var connected = isConnected();
    if(target == undefined) {   //index
        if(connected){
            $("#chatBtnConnect").text('Online en el chat.');
            disableBtn();
        } else {
            $("#chatBtnConnect").removeAttr('disabled');
            $("#chatBtnConnect").text('Comenzar a chatear');
            $("#chatBtnConnect").addClass("btn-highlight");

        }
    } else {
        $("#chatBtnConnect").removeAttr('disabled');
        $("#chatBtnConnect").addClass("btn-highlight");
        $("#chatBtnConnect").text('Chatear en ' +target );


        $("#chatBtnConnect").click(function() {
            if(isConnected()) {

                localStorage.setItem("data", '{"event": "openChannel", "arguments": "'+target+'"}');

                $.notify({
                    message: " Abriendo chat " +target + " dirígite a la pestaña de chat.",
                    enter: 'animated zoomInDown',
                    exit: 'animated zoomOutUp'
                });
                disableBtn();
            } else {
                var checkConnection = setInterval(function () {
                    if(isConnected()) {
                        setTimeout(function () {
                            localStorage.setItem("data", '{"event": "openChannel", "arguments": "'+target+'"}');
                            clearInterval(checkConnection);
                        }, 5000);
                    }
                }, 5000);

                if(url == undefined) {
                    window.open(window.chat_url+ "?target=" + target);
                } else {
                    window.open(url);
                }

                disableBtn();
            }
        });
    }
}