$(function(){

	$('.main_menu_mobile_obj').on('click', function(){
		$('.main_menu_mobile_sub').toggleClass('ds_none');
		$(this).toggleClass('main_menu_mobile_obj_active');
	});
});
