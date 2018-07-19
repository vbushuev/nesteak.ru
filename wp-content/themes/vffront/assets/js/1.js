jQuery(document).ready(function () {

	// L1
	
		// Переключение табов
			$(function() {

				$('.tabs_calendar ul.tab_title').on('click', 'li:not(.active)', function() {
					$(this)
					  .addClass('active').siblings().removeClass('active')
					  .closest('div.tabs_calendar').find('div.tab_content').removeClass('active').eq($(this).index()).addClass('active');
				});

			});

		// Календарь

			var arr_action={1:"событие 1",2:"событие 2",11:"событие 11",12:"событие 12",15:"событие 15",22:"событие 22"};

			function e(td1){
				
				var id_mes=document.getElementById("id_mes");
					if(id_mes!=undefined) id_mes.remove();
						if(td1.id=='')
				    		return;
				var m=arr_action[td1.id];    
				var tb = document.getElementById("table_calendar");
				var i=td1.parentNode.rowIndex;
				var tr = tb.insertRow(i+1);
				var td = tr.insertCell(0);
			  
				var newText = document.createTextNode(m);
				td.appendChild(newText);
				td.setAttribute("colspan", 7); 	
				tr.setAttribute("id", "id_mes");
			}
			
			function createCalendar( year, month) {
				var cl1=' class="dt_1"';  // cтили для ячеек таблицы
				var cl2=' class="dt_2"'; // cтили для ячеек таблицы с событиями
				var cl3=' class="dt_3"';  // неактивные дни другого месяца
				var arr_month=["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"];

				var elem = document.getElementById("cal");
				var mon = month - 1; // месяцы в JS идут от 0 до 11, а не от 1 до 12
				var d = new Date(year, mon);
				var t1=' <a onclick="createCalendar( '+year+','+(month-1)+' )">&lt;</a> ';
				var t2=' <a onclick="createCalendar( '+year+','+(month+1)+' )">></a> ';
				if (month+1==13) t2=' <a onclick="createCalendar( '+(year+1)+',1 )">></a> ';
				if (month-1==0)t1=' <a onclick="createCalendar( '+(year-1)+',12 )">&lt;</a> ';

				var table = '<table  id="table_calendar"><tr><td class="change_mounth" colspan="7"> '+t1+'<span>' +arr_month[month-1] + year + '</span>'+t2+'</td></tr><tr class="calendar_days"><th>пн</th><th>вт</th><th>ср</th><th>чт</th><th>пт</th><th>сб</th><th>вс</th></tr><tr>';

				// заполнить первый ряд от понедельника
				// и до дня, с которого начинается месяц
				// * * * | 1  2  3  4
				var d2 = getLastDayOfMonth(year, mon-1);

				for (var i = 0; i < getDay(d); i++) {
					var w=d2-getDay(d)+i+1;
					table += '<td '+cl3+'>'+w+'</td>';
				}

				// ячейки календаря с датами
				while (d.getMonth() == mon) {
				  	var cl=cl1;
				  	var id='';
					var key=d.getDate();
				  	if(  key in arr_action){
				  		 cl=cl2; 
				  		 id=' id="'+d.getDate()+'"';
				  	}
				  	
				    table += '<td '+cl+id+'>' +key + '</td>';

				    if (getDay(d) % 7 == 6) { // вс, последний день - перевод строки
				      table += '</tr><tr>';
				    }

				    d.setDate(d.getDate() + 1);  
				}

				// добить таблицу пустыми ячейками, если нужно
				if (getDay(d) != 0) {
					var w=1;
					for (var i = getDay(d); i < 7; i++) {
					  table += '<td '+cl3+'>'+w+'</td>';
					  w++;
					}
				}

				// закрыть таблицу
				table += '</tr></table>';

				// только одно присваивание innerHTML
				elem.innerHTML = table;
				$('table td').mouseover(function(){
					e(this);
					});
				}
			    
				function getLastDayOfMonth(year, month) {
					
				if (month==11)	{year++; month=0;   
				}else 
				if (month==0)	{year--; month=11;   }
					
				var date = new Date(year, month + 1, 0);
					return date.getDate();
				}

			    function getDay(date) { // получить номер дня недели, от 0(пн) до 6(вс)
					var day = date.getDay();
					if (day == 0) day = 7;
					return day - 1;
			    }

			//----------------------------------------------------------------------------
	    	createCalendar( 2012, 9);

	// L2
	// L3
	
	// R1
	// R2
	// R3
	
	// C1
	
	$('dialog.express .list_express li div a').click(function(){
		$(this).parent().parent().detach();
	});

	$('.table_footer a.order_deal').click(function(){
		$('dialog.express').fadeIn();
		return false;
	});

	$('.minus').click(function () {
		var $input = $(this).parent().find('input');
		var count = parseInt($input.val()) - 1;
		count = count < 1 ? 1 : count;
		$input.val(count);
		$input.change();
		return false;
	});
	
	$('.plus').click(function () {
		var $input = $(this).parent().find('input');
		$input.val(parseInt($input.val()) + 1);
		$input.change();
		return false;
	});

	$('.express .change_Payment_to a').click(function(){
		$('.express .change_Payment input').val($(this).find('span').html());
		return false;
	});

	$('.filter_game .closer a').click(function(){
		$(this).parent().toggleClass('active');
		if ( $('.filter_game_list > div').is(':visible') ) {
			$('.filter_game_list > div').slideUp();	
		} else {
			$('.filter_game_list > div').slideDown();
		}
		return false;
	});

	$('.table .table_column.sub_menu a:not(.toggleClick)').click(function(){
		$(this).toggleClass('changeCon').parent().parent().parent().find('.sub_table_item').toggleClass('hide').parent().toggleClass('active');
	});

	$('.table_inner').on('click','a.toggleClick',function(){

		var kof = parseFloat($(this).html()),
			totalKof = parseFloat($('.total_kof').html()),
			
			player = $(this).parent().prev().html(),
			date = $(this).parent().parent().find('.table_column').eq(1).html();

		if ( $(this).hasClass('active') ) {
			$(this).removeClass('active');
			totalKof /= kof;
			$('.list_table_item li:last').detach();
		} else {
			$(this).addClass('active');
			totalKof *= kof;
			
			if ( $('.list_table_item li').length == 0 ) {
				$('.list_table_item').append('<li>' + player + '</li>');
				$('.list_express').append('<li><div><p>' + date + '</p><p>' + player + '</p></div><div>' + kof + '<a><i class="ic ic_del"></i></a></div></li>');
			} else {
				$('.list_table_item').append('<li>,' + player + '</li>');
				$('.list_express').append('<li><div><p>' + date + '</p><p>' + player + '</p></div><div>' + kof + '<a><i class="ic ic_del"></i></a></div></li>');
			}
		}

		$('.total_kof').html( totalKof.toFixed(3) );
		$('.total_koff span').html( totalKof.toFixed(3) );

		return false;
	});

	$('.line_turn_off a').click(function(){
		$(this).parent().slideUp();

		return false;
	});
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiIiwic291cmNlcyI6WyIxLmpzIl0sInNvdXJjZXNDb250ZW50IjpbImpRdWVyeShkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xyXG5cclxuXHQvLyBMMVxyXG5cdFxyXG5cdFx0Ly8g0J/QtdGA0LXQutC70Y7Rh9C10L3QuNC1INGC0LDQsdC+0LJcclxuXHRcdFx0JChmdW5jdGlvbigpIHtcclxuXHJcblx0XHRcdFx0JCgnLnRhYnNfY2FsZW5kYXIgdWwudGFiX3RpdGxlJykub24oJ2NsaWNrJywgJ2xpOm5vdCguYWN0aXZlKScsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdFx0JCh0aGlzKVxyXG5cdFx0XHRcdFx0ICAuYWRkQ2xhc3MoJ2FjdGl2ZScpLnNpYmxpbmdzKCkucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpXHJcblx0XHRcdFx0XHQgIC5jbG9zZXN0KCdkaXYudGFic19jYWxlbmRhcicpLmZpbmQoJ2Rpdi50YWJfY29udGVudCcpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKS5lcSgkKHRoaXMpLmluZGV4KCkpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuXHRcdFx0XHR9KTtcclxuXHJcblx0XHRcdH0pO1xyXG5cclxuXHRcdC8vINCa0LDQu9C10L3QtNCw0YDRjFxyXG5cclxuXHRcdFx0dmFyIGFycl9hY3Rpb249ezE6XCLRgdC+0LHRi9GC0LjQtSAxXCIsMjpcItGB0L7QsdGL0YLQuNC1IDJcIiwxMTpcItGB0L7QsdGL0YLQuNC1IDExXCIsMTI6XCLRgdC+0LHRi9GC0LjQtSAxMlwiLDE1Olwi0YHQvtCx0YvRgtC40LUgMTVcIiwyMjpcItGB0L7QsdGL0YLQuNC1IDIyXCJ9O1xyXG5cclxuXHRcdFx0ZnVuY3Rpb24gZSh0ZDEpe1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdHZhciBpZF9tZXM9ZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJpZF9tZXNcIik7XHJcblx0XHRcdFx0XHRpZihpZF9tZXMhPXVuZGVmaW5lZCkgaWRfbWVzLnJlbW92ZSgpO1xyXG5cdFx0XHRcdFx0XHRpZih0ZDEuaWQ9PScnKVxyXG5cdFx0XHRcdCAgICBcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdHZhciBtPWFycl9hY3Rpb25bdGQxLmlkXTsgICAgXHJcblx0XHRcdFx0dmFyIHRiID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJ0YWJsZV9jYWxlbmRhclwiKTtcclxuXHRcdFx0XHR2YXIgaT10ZDEucGFyZW50Tm9kZS5yb3dJbmRleDtcclxuXHRcdFx0XHR2YXIgdHIgPSB0Yi5pbnNlcnRSb3coaSsxKTtcclxuXHRcdFx0XHR2YXIgdGQgPSB0ci5pbnNlcnRDZWxsKDApO1xyXG5cdFx0XHQgIFxyXG5cdFx0XHRcdHZhciBuZXdUZXh0ID0gZG9jdW1lbnQuY3JlYXRlVGV4dE5vZGUobSk7XHJcblx0XHRcdFx0dGQuYXBwZW5kQ2hpbGQobmV3VGV4dCk7XHJcblx0XHRcdFx0dGQuc2V0QXR0cmlidXRlKFwiY29sc3BhblwiLCA3KTsgXHRcclxuXHRcdFx0XHR0ci5zZXRBdHRyaWJ1dGUoXCJpZFwiLCBcImlkX21lc1wiKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRcclxuXHRcdFx0ZnVuY3Rpb24gY3JlYXRlQ2FsZW5kYXIoIHllYXIsIG1vbnRoKSB7XHJcblx0XHRcdFx0dmFyIGNsMT0nIGNsYXNzPVwiZHRfMVwiJzsgIC8vIGPRgtC40LvQuCDQtNC70Y8g0Y/Rh9C10LXQuiDRgtCw0LHQu9C40YbRi1xyXG5cdFx0XHRcdHZhciBjbDI9JyBjbGFzcz1cImR0XzJcIic7IC8vIGPRgtC40LvQuCDQtNC70Y8g0Y/Rh9C10LXQuiDRgtCw0LHQu9C40YbRiyDRgSDRgdC+0LHRi9GC0LjRj9C80LhcclxuXHRcdFx0XHR2YXIgY2wzPScgY2xhc3M9XCJkdF8zXCInOyAgLy8g0L3QtdCw0LrRgtC40LLQvdGL0LUg0LTQvdC4INC00YDRg9Cz0L7Qs9C+INC80LXRgdGP0YbQsFxyXG5cdFx0XHRcdHZhciBhcnJfbW9udGg9W1wi0K/QvdCy0LDRgNGMXCIsXCLQpNC10LLRgNCw0LvRjFwiLFwi0JzQsNGA0YJcIixcItCQ0L/RgNC10LvRjFwiLFwi0JzQsNC5XCIsXCLQmNGO0L3RjFwiLFwi0JjRjtC70YxcIixcItCQ0LLQs9GD0YHRglwiLFwi0KHQtdC90YLRj9Cx0YDRjFwiLFwi0J7QutGC0Y/QsdGA0YxcIixcItCd0L7Rj9Cx0YDRjFwiLFwi0JTQtdC60LDQsdGA0YxcIl07XHJcblxyXG5cdFx0XHRcdHZhciBlbGVtID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJjYWxcIik7XHJcblx0XHRcdFx0dmFyIG1vbiA9IG1vbnRoIC0gMTsgLy8g0LzQtdGB0Y/RhtGLINCyIEpTINC40LTRg9GCINC+0YIgMCDQtNC+IDExLCDQsCDQvdC1INC+0YIgMSDQtNC+IDEyXHJcblx0XHRcdFx0dmFyIGQgPSBuZXcgRGF0ZSh5ZWFyLCBtb24pO1xyXG5cdFx0XHRcdHZhciB0MT0nIDxhIG9uY2xpY2s9XCJjcmVhdGVDYWxlbmRhciggJyt5ZWFyKycsJysobW9udGgtMSkrJyApXCI+Jmx0OzwvYT4gJztcclxuXHRcdFx0XHR2YXIgdDI9JyA8YSBvbmNsaWNrPVwiY3JlYXRlQ2FsZW5kYXIoICcreWVhcisnLCcrKG1vbnRoKzEpKycgKVwiPj48L2E+ICc7XHJcblx0XHRcdFx0aWYgKG1vbnRoKzE9PTEzKSB0Mj0nIDxhIG9uY2xpY2s9XCJjcmVhdGVDYWxlbmRhciggJysoeWVhcisxKSsnLDEgKVwiPj48L2E+ICc7XHJcblx0XHRcdFx0aWYgKG1vbnRoLTE9PTApdDE9JyA8YSBvbmNsaWNrPVwiY3JlYXRlQ2FsZW5kYXIoICcrKHllYXItMSkrJywxMiApXCI+Jmx0OzwvYT4gJztcclxuXHJcblx0XHRcdFx0dmFyIHRhYmxlID0gJzx0YWJsZSAgaWQ9XCJ0YWJsZV9jYWxlbmRhclwiPjx0cj48dGQgY2xhc3M9XCJjaGFuZ2VfbW91bnRoXCIgY29sc3Bhbj1cIjdcIj4gJyt0MSsnPHNwYW4+JyArYXJyX21vbnRoW21vbnRoLTFdICsgeWVhciArICc8L3NwYW4+Jyt0MisnPC90ZD48L3RyPjx0ciBjbGFzcz1cImNhbGVuZGFyX2RheXNcIj48dGg+0L/QvTwvdGg+PHRoPtCy0YI8L3RoPjx0aD7RgdGAPC90aD48dGg+0YfRgjwvdGg+PHRoPtC/0YI8L3RoPjx0aD7RgdCxPC90aD48dGg+0LLRgTwvdGg+PC90cj48dHI+JztcclxuXHJcblx0XHRcdFx0Ly8g0LfQsNC/0L7Qu9C90LjRgtGMINC/0LXRgNCy0YvQuSDRgNGP0LQg0L7RgiDQv9C+0L3QtdC00LXQu9GM0L3QuNC60LBcclxuXHRcdFx0XHQvLyDQuCDQtNC+INC00L3Rjywg0YEg0LrQvtGC0L7RgNC+0LPQviDQvdCw0YfQuNC90LDQtdGC0YHRjyDQvNC10YHRj9GGXHJcblx0XHRcdFx0Ly8gKiAqICogfCAxICAyICAzICA0XHJcblx0XHRcdFx0dmFyIGQyID0gZ2V0TGFzdERheU9mTW9udGgoeWVhciwgbW9uLTEpO1xyXG5cclxuXHRcdFx0XHRmb3IgKHZhciBpID0gMDsgaSA8IGdldERheShkKTsgaSsrKSB7XHJcblx0XHRcdFx0XHR2YXIgdz1kMi1nZXREYXkoZCkraSsxO1xyXG5cdFx0XHRcdFx0dGFibGUgKz0gJzx0ZCAnK2NsMysnPicrdysnPC90ZD4nO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0Ly8g0Y/Rh9C10LnQutC4INC60LDQu9C10L3QtNCw0YDRjyDRgSDQtNCw0YLQsNC80LhcclxuXHRcdFx0XHR3aGlsZSAoZC5nZXRNb250aCgpID09IG1vbikge1xyXG5cdFx0XHRcdCAgXHR2YXIgY2w9Y2wxO1xyXG5cdFx0XHRcdCAgXHR2YXIgaWQ9Jyc7XHJcblx0XHRcdFx0XHR2YXIga2V5PWQuZ2V0RGF0ZSgpO1xyXG5cdFx0XHRcdCAgXHRpZiggIGtleSBpbiBhcnJfYWN0aW9uKXtcclxuXHRcdFx0XHQgIFx0XHQgY2w9Y2wyOyBcclxuXHRcdFx0XHQgIFx0XHQgaWQ9JyBpZD1cIicrZC5nZXREYXRlKCkrJ1wiJztcclxuXHRcdFx0XHQgIFx0fVxyXG5cdFx0XHRcdCAgXHRcclxuXHRcdFx0XHQgICAgdGFibGUgKz0gJzx0ZCAnK2NsK2lkKyc+JyAra2V5ICsgJzwvdGQ+JztcclxuXHJcblx0XHRcdFx0ICAgIGlmIChnZXREYXkoZCkgJSA3ID09IDYpIHsgLy8g0LLRgSwg0L/QvtGB0LvQtdC00L3QuNC5INC00LXQvdGMIC0g0L/QtdGA0LXQstC+0LQg0YHRgtGA0L7QutC4XHJcblx0XHRcdFx0ICAgICAgdGFibGUgKz0gJzwvdHI+PHRyPic7XHJcblx0XHRcdFx0ICAgIH1cclxuXHJcblx0XHRcdFx0ICAgIGQuc2V0RGF0ZShkLmdldERhdGUoKSArIDEpOyAgXHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyDQtNC+0LHQuNGC0Ywg0YLQsNCx0LvQuNGG0YMg0L/Rg9GB0YLRi9C80Lgg0Y/Rh9C10LnQutCw0LzQuCwg0LXRgdC70Lgg0L3Rg9C20L3QvlxyXG5cdFx0XHRcdGlmIChnZXREYXkoZCkgIT0gMCkge1xyXG5cdFx0XHRcdFx0dmFyIHc9MTtcclxuXHRcdFx0XHRcdGZvciAodmFyIGkgPSBnZXREYXkoZCk7IGkgPCA3OyBpKyspIHtcclxuXHRcdFx0XHRcdCAgdGFibGUgKz0gJzx0ZCAnK2NsMysnPicrdysnPC90ZD4nO1xyXG5cdFx0XHRcdFx0ICB3Kys7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cclxuXHRcdFx0XHQvLyDQt9Cw0LrRgNGL0YLRjCDRgtCw0LHQu9C40YbRg1xyXG5cdFx0XHRcdHRhYmxlICs9ICc8L3RyPjwvdGFibGU+JztcclxuXHJcblx0XHRcdFx0Ly8g0YLQvtC70YzQutC+INC+0LTQvdC+INC/0YDQuNGB0LLQsNC40LLQsNC90LjQtSBpbm5lckhUTUxcclxuXHRcdFx0XHRlbGVtLmlubmVySFRNTCA9IHRhYmxlO1xyXG5cdFx0XHRcdCQoJ3RhYmxlIHRkJykubW91c2VvdmVyKGZ1bmN0aW9uKCl7XHJcblx0XHRcdFx0XHRlKHRoaXMpO1xyXG5cdFx0XHRcdFx0fSk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHQgICAgXHJcblx0XHRcdFx0ZnVuY3Rpb24gZ2V0TGFzdERheU9mTW9udGgoeWVhciwgbW9udGgpIHtcclxuXHRcdFx0XHRcdFxyXG5cdFx0XHRcdGlmIChtb250aD09MTEpXHR7eWVhcisrOyBtb250aD0wOyAgIFxyXG5cdFx0XHRcdH1lbHNlIFxyXG5cdFx0XHRcdGlmIChtb250aD09MClcdHt5ZWFyLS07IG1vbnRoPTExOyAgIH1cclxuXHRcdFx0XHRcdFxyXG5cdFx0XHRcdHZhciBkYXRlID0gbmV3IERhdGUoeWVhciwgbW9udGggKyAxLCAwKTtcclxuXHRcdFx0XHRcdHJldHVybiBkYXRlLmdldERhdGUoKTtcclxuXHRcdFx0XHR9XHJcblxyXG5cdFx0XHQgICAgZnVuY3Rpb24gZ2V0RGF5KGRhdGUpIHsgLy8g0L/QvtC70YPRh9C40YLRjCDQvdC+0LzQtdGAINC00L3RjyDQvdC10LTQtdC70LgsINC+0YIgMCjQv9C9KSDQtNC+IDYo0LLRgSlcclxuXHRcdFx0XHRcdHZhciBkYXkgPSBkYXRlLmdldERheSgpO1xyXG5cdFx0XHRcdFx0aWYgKGRheSA9PSAwKSBkYXkgPSA3O1xyXG5cdFx0XHRcdFx0cmV0dXJuIGRheSAtIDE7XHJcblx0XHRcdCAgICB9XHJcblxyXG5cdFx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQgICAgXHRjcmVhdGVDYWxlbmRhciggMjAxMiwgOSk7XHJcblxyXG5cdC8vIEwyXHJcblx0Ly8gTDNcclxuXHRcclxuXHQvLyBSMVxyXG5cdC8vIFIyXHJcblx0Ly8gUjNcclxuXHRcclxuXHQvLyBDMVxyXG5cdFxyXG5cdCQoJ2RpYWxvZy5leHByZXNzIC5saXN0X2V4cHJlc3MgbGkgZGl2IGEnKS5jbGljayhmdW5jdGlvbigpe1xyXG5cdFx0JCh0aGlzKS5wYXJlbnQoKS5wYXJlbnQoKS5kZXRhY2goKTtcclxuXHR9KTtcclxuXHJcblx0JCgnLnRhYmxlX2Zvb3RlciBhLm9yZGVyX2RlYWwnKS5jbGljayhmdW5jdGlvbigpe1xyXG5cdFx0JCgnZGlhbG9nLmV4cHJlc3MnKS5mYWRlSW4oKTtcclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9KTtcclxuXHJcblx0JCgnLm1pbnVzJykuY2xpY2soZnVuY3Rpb24gKCkge1xyXG5cdFx0dmFyICRpbnB1dCA9ICQodGhpcykucGFyZW50KCkuZmluZCgnaW5wdXQnKTtcclxuXHRcdHZhciBjb3VudCA9IHBhcnNlSW50KCRpbnB1dC52YWwoKSkgLSAxO1xyXG5cdFx0Y291bnQgPSBjb3VudCA8IDEgPyAxIDogY291bnQ7XHJcblx0XHQkaW5wdXQudmFsKGNvdW50KTtcclxuXHRcdCRpbnB1dC5jaGFuZ2UoKTtcclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9KTtcclxuXHRcclxuXHQkKCcucGx1cycpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuXHRcdHZhciAkaW5wdXQgPSAkKHRoaXMpLnBhcmVudCgpLmZpbmQoJ2lucHV0Jyk7XHJcblx0XHQkaW5wdXQudmFsKHBhcnNlSW50KCRpbnB1dC52YWwoKSkgKyAxKTtcclxuXHRcdCRpbnB1dC5jaGFuZ2UoKTtcclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9KTtcclxuXHJcblx0JCgnLmV4cHJlc3MgLmNoYW5nZV9QYXltZW50X3RvIGEnKS5jbGljayhmdW5jdGlvbigpe1xyXG5cdFx0JCgnLmV4cHJlc3MgLmNoYW5nZV9QYXltZW50IGlucHV0JykudmFsKCQodGhpcykuZmluZCgnc3BhbicpLmh0bWwoKSk7XHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fSk7XHJcblxyXG5cdCQoJy5maWx0ZXJfZ2FtZSAuY2xvc2VyIGEnKS5jbGljayhmdW5jdGlvbigpe1xyXG5cdFx0JCh0aGlzKS5wYXJlbnQoKS50b2dnbGVDbGFzcygnYWN0aXZlJyk7XHJcblx0XHRpZiAoICQoJy5maWx0ZXJfZ2FtZV9saXN0ID4gZGl2JykuaXMoJzp2aXNpYmxlJykgKSB7XHJcblx0XHRcdCQoJy5maWx0ZXJfZ2FtZV9saXN0ID4gZGl2Jykuc2xpZGVVcCgpO1x0XHJcblx0XHR9IGVsc2Uge1xyXG5cdFx0XHQkKCcuZmlsdGVyX2dhbWVfbGlzdCA+IGRpdicpLnNsaWRlRG93bigpO1xyXG5cdFx0fVxyXG5cdFx0cmV0dXJuIGZhbHNlO1xyXG5cdH0pO1xyXG5cclxuXHQkKCcudGFibGUgLnRhYmxlX2NvbHVtbi5zdWJfbWVudSBhOm5vdCgudG9nZ2xlQ2xpY2spJykuY2xpY2soZnVuY3Rpb24oKXtcclxuXHRcdCQodGhpcykudG9nZ2xlQ2xhc3MoJ2NoYW5nZUNvbicpLnBhcmVudCgpLnBhcmVudCgpLnBhcmVudCgpLmZpbmQoJy5zdWJfdGFibGVfaXRlbScpLnRvZ2dsZUNsYXNzKCdoaWRlJykucGFyZW50KCkudG9nZ2xlQ2xhc3MoJ2FjdGl2ZScpO1xyXG5cdH0pO1xyXG5cclxuXHQkKCcudGFibGVfaW5uZXInKS5vbignY2xpY2snLCdhLnRvZ2dsZUNsaWNrJyxmdW5jdGlvbigpe1xyXG5cclxuXHRcdHZhciBrb2YgPSBwYXJzZUZsb2F0KCQodGhpcykuaHRtbCgpKSxcclxuXHRcdFx0dG90YWxLb2YgPSBwYXJzZUZsb2F0KCQoJy50b3RhbF9rb2YnKS5odG1sKCkpLFxyXG5cdFx0XHRcclxuXHRcdFx0cGxheWVyID0gJCh0aGlzKS5wYXJlbnQoKS5wcmV2KCkuaHRtbCgpLFxyXG5cdFx0XHRkYXRlID0gJCh0aGlzKS5wYXJlbnQoKS5wYXJlbnQoKS5maW5kKCcudGFibGVfY29sdW1uJykuZXEoMSkuaHRtbCgpO1xyXG5cclxuXHRcdGlmICggJCh0aGlzKS5oYXNDbGFzcygnYWN0aXZlJykgKSB7XHJcblx0XHRcdCQodGhpcykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG5cdFx0XHR0b3RhbEtvZiAvPSBrb2Y7XHJcblx0XHRcdCQoJy5saXN0X3RhYmxlX2l0ZW0gbGk6bGFzdCcpLmRldGFjaCgpO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0JCh0aGlzKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcblx0XHRcdHRvdGFsS29mICo9IGtvZjtcclxuXHRcdFx0XHJcblx0XHRcdGlmICggJCgnLmxpc3RfdGFibGVfaXRlbSBsaScpLmxlbmd0aCA9PSAwICkge1xyXG5cdFx0XHRcdCQoJy5saXN0X3RhYmxlX2l0ZW0nKS5hcHBlbmQoJzxsaT4nICsgcGxheWVyICsgJzwvbGk+Jyk7XHJcblx0XHRcdFx0JCgnLmxpc3RfZXhwcmVzcycpLmFwcGVuZCgnPGxpPjxkaXY+PHA+JyArIGRhdGUgKyAnPC9wPjxwPicgKyBwbGF5ZXIgKyAnPC9wPjwvZGl2PjxkaXY+JyArIGtvZiArICc8YT48aSBjbGFzcz1cImljIGljX2RlbFwiPjwvaT48L2E+PC9kaXY+PC9saT4nKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHQkKCcubGlzdF90YWJsZV9pdGVtJykuYXBwZW5kKCc8bGk+LCcgKyBwbGF5ZXIgKyAnPC9saT4nKTtcclxuXHRcdFx0XHQkKCcubGlzdF9leHByZXNzJykuYXBwZW5kKCc8bGk+PGRpdj48cD4nICsgZGF0ZSArICc8L3A+PHA+JyArIHBsYXllciArICc8L3A+PC9kaXY+PGRpdj4nICsga29mICsgJzxhPjxpIGNsYXNzPVwiaWMgaWNfZGVsXCI+PC9pPjwvYT48L2Rpdj48L2xpPicpO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblxyXG5cdFx0JCgnLnRvdGFsX2tvZicpLmh0bWwoIHRvdGFsS29mLnRvRml4ZWQoMykgKTtcclxuXHRcdCQoJy50b3RhbF9rb2ZmIHNwYW4nKS5odG1sKCB0b3RhbEtvZi50b0ZpeGVkKDMpICk7XHJcblxyXG5cdFx0cmV0dXJuIGZhbHNlO1xyXG5cdH0pO1xyXG5cclxuXHQkKCcubGluZV90dXJuX29mZiBhJykuY2xpY2soZnVuY3Rpb24oKXtcclxuXHRcdCQodGhpcykucGFyZW50KCkuc2xpZGVVcCgpO1xyXG5cclxuXHRcdHJldHVybiBmYWxzZTtcclxuXHR9KTtcclxufSk7Il0sImZpbGUiOiIxLmpzIn0=
