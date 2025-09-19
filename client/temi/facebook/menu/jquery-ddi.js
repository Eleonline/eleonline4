$(function(){
$("#nav>li").hoverIntent(
    function(){$("ul",this).fadeIn("slow");},
    function(){$("ul",this).css({left:"-1px"}).fadeOut("fast");});
});