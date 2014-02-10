/* Main Javascript File 
 * author http://google.com/+BillRocha 
 * date:  2014/02/08
*/

var view = 'ajax';
var runing = false;

/* Jquery */
$(document).ready(function() {


    $("#viewiframe").click(function(){
        showView('iframe');
        return false;});
    $("#viewajax").click(function(){
        showView('ajax');
        return false;
    });

    function showView(what){
        //format
        if(!what){ what = view;}
        view = what;
        
        $("#play").animate({top: '10px', opacity: 1}, 10, function(){
            $("#playcontent").html('loading...');
            
            if(what == 'iframe'){
                $("#play h3").html('This content is loaded with iFrame');
                $("#playcontent").html('<iframe src="http://loren.tk/p/4/1"></iframe>');
            }
            if(what == 'ajax'){
                $("#play h3").html('Content loaded with Ajax');
                $("#playcontent").load("http://loren.tk/p/2/1"); 
            }
        });
        
    }
    

    //BUTTONS
    $("#reloadView").click(function(){
        if(runing == false){
            runing = true;
            $("#playcontent").html('loading...');        
            $("#play").animate({opacity: 0}, 100).animate({opacity: 1}, 1500, function(){showView(); runing = false;});
        }        
    });
    
    $("#closeView").click(function(){        
        $("#play").animate({top: '-400px', opacity:0}, 10, function(){
            $("#play h3").html('Loading ....');
            $("#playcontent").html('. . .')})})
})