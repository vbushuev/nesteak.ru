jQuery(document).ready(function () {

	// Смена активной кнопки - страницы. Левое меню
	$('.p-menu').click(function(){
		$('.p-menu').removeClass('active');
		$(this).addClass('active');
	});

	// Показываем \ прячем id юзера
	$('.header .user_info p.flex').click(function(){
		$(this).toggleClass('rotate');
		$('.user_info p.id').toggleClass('hidden');
	});

});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiIiwic291cmNlcyI6WyJsZWZ0LXRvcC5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJqUXVlcnkoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcclxuXHJcblx0Ly8g0KHQvNC10L3QsCDQsNC60YLQuNCy0L3QvtC5INC60L3QvtC/0LrQuCAtINGB0YLRgNCw0L3QuNGG0YsuINCb0LXQstC+0LUg0LzQtdC90Y5cclxuXHQkKCcucC1tZW51JykuY2xpY2soZnVuY3Rpb24oKXtcclxuXHRcdCQoJy5wLW1lbnUnKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcblx0XHQkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuXHR9KTtcclxuXHJcblx0Ly8g0J/QvtC60LDQt9GL0LLQsNC10LwgXFwg0L/RgNGP0YfQtdC8IGlkINGO0LfQtdGA0LBcclxuXHQkKCcuaGVhZGVyIC51c2VyX2luZm8gcC5mbGV4JykuY2xpY2soZnVuY3Rpb24oKXtcclxuXHRcdCQodGhpcykudG9nZ2xlQ2xhc3MoJ3JvdGF0ZScpO1xyXG5cdFx0JCgnLnVzZXJfaW5mbyBwLmlkJykudG9nZ2xlQ2xhc3MoJ2hpZGRlbicpO1xyXG5cdH0pO1xyXG5cclxufSk7Il0sImZpbGUiOiJsZWZ0LXRvcC5qcyJ9
