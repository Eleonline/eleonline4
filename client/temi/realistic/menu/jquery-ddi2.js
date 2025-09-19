$(function(){
$("#nav>li").hoverIntent(
    function(){$("ul",this).slideDown("slow");},
    function(){$("ul",this).css({left:"-1px"}).slideUp('fast')});
});



