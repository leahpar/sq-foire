$( document ).ready(function() {
    
    
    $(".navLink").click(function(){
        
        if($("nav").hasClass("open")){
            $("nav").removeClass("open");
        }else{
            $("nav").addClass("open");
        }
        
        return false;
        
    });

    function GetURLParameter(sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam) {
                return sParameterName[1];
            }
        }
    }
    reqTime = Math.floor(new Date()/1000);
    //console.log(GetURLParameter('action'), GetURLParameter('entity'));
    if ( GetURLParameter('action') === 'list'
      && GetURLParameter('entity') === 'Player') {
        setInterval(function() {
            // Si pas de notif de tirage en cours
            if ($('div.alert').length === 0) {
                $.ajax({
                    url: "/admin/reload?t="+reqTime,
                    statusCode: {
                        200: function() {
                            //console.log(200);
                                window.location.reload();
                            }
                        },
                        // Pas de modification
                        304: function() {
                            //console.log(304);
                        },
                        500: function() {
                            //console.log(500);
                        },
                });
            }
        }, 10000);
    }
    
});