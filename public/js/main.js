$( document ).ready(function() {
    
    
    $(".navLink").click(function(){
        
        console.log("clic");
        
        if($("nav").hasClass("open")){
            $("nav").removeClass("open");
        }else{
            $("nav").addClass("open");
        }
        
        return false;
        
    });
    
    
});