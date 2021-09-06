"use strict"; 
/*****Ready function start*****/
$(document).ready(function(){
	griffin();
	emailApp();
	chatApp();
	calendarApp();
	fmApp();
	/*Disabled*/
	$(document).on("click", "a.disabled,a:disabled",function(e) {
		 return false;
	});
});

/*Variables*/
var height,width,
$wrapper = $(".hk-wrapper"),
$nav = $(".hk-nav"),
$vertnaltNav = $(".hk-wrapper.hk-vertical-nav,.hk-wrapper.hk-alt-nav"),
$horizontalNav = $(".hk-wrapper.hk-horizontal-nav"),
$navbar= $(".hk-navbar");

/***** griffin function start *****/
var griffin = function(){
	
	/*Feather Icon*/
	var featherIcon = $('.feather-icon');
	if( featherIcon.length > 0 ){
		feather.replace();
	}
	
	
	/*Counter Animation*/
	var counterAnim = $('.counter-anim');
	if( counterAnim.length > 0 ){
		counterAnim.counterUp({ delay: 10,
        time: 1000});
	}
	
	/*Tooltip*/
	if( $('[data-toggle="tooltip"]').length > 0 )
		$('[data-toggle="tooltip"]').tooltip();
	
	/*Popover*/
	if( $('[data-toggle="popover"]').length > 0 )
		$('[data-toggle="popover"]').popover()
	
	/*Navbar Collapse Animation*/
	var navbarNavCollapse = $('.hk-nav .navbar-nav  li');
	var navbarNavAnchor = '.hk-nav .navbar-nav  li a';
	$(document).on("click",navbarNavAnchor,function (e) {
		if ($(this).attr('aria-expanded') === "false")
				$(this).blur();
			$(this).parent().siblings().find('.collapse').collapse('hide');
			$(this).parent().find('.collapse').collapse('hide');
	});