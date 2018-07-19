// jQuery(document).ready(function () {

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
	            
	        if (month==11)  {year++; month=0;   
	        }else 
	        if (month==0)   {year--; month=11;   }
	            
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
	
// });
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiIiwic291cmNlcyI6WyJjYWxlbmRhci5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvLyBqUXVlcnkoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcclxuXHJcblx0Ly8g0JrQsNC70LXQvdC00LDRgNGMXHJcblxyXG5cdCAgICB2YXIgYXJyX2FjdGlvbj17MTpcItGB0L7QsdGL0YLQuNC1IDFcIiwyOlwi0YHQvtCx0YvRgtC40LUgMlwiLDExOlwi0YHQvtCx0YvRgtC40LUgMTFcIiwxMjpcItGB0L7QsdGL0YLQuNC1IDEyXCIsMTU6XCLRgdC+0LHRi9GC0LjQtSAxNVwiLDIyOlwi0YHQvtCx0YvRgtC40LUgMjJcIn07XHJcblxyXG5cdCAgICBmdW5jdGlvbiBlKHRkMSl7XHJcblx0ICAgICAgICBcclxuXHQgICAgICAgIHZhciBpZF9tZXM9ZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJpZF9tZXNcIik7XHJcblx0ICAgICAgICAgICAgaWYoaWRfbWVzIT11bmRlZmluZWQpIGlkX21lcy5yZW1vdmUoKTtcclxuXHQgICAgICAgICAgICAgICAgaWYodGQxLmlkPT0nJylcclxuXHQgICAgICAgICAgICAgICAgICAgIHJldHVybjtcclxuXHQgICAgICAgIHZhciBtPWFycl9hY3Rpb25bdGQxLmlkXTsgICAgXHJcblx0ICAgICAgICB2YXIgdGIgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcInRhYmxlX2NhbGVuZGFyXCIpO1xyXG5cdCAgICAgICAgdmFyIGk9dGQxLnBhcmVudE5vZGUucm93SW5kZXg7XHJcblx0ICAgICAgICB2YXIgdHIgPSB0Yi5pbnNlcnRSb3coaSsxKTtcclxuXHQgICAgICAgIHZhciB0ZCA9IHRyLmluc2VydENlbGwoMCk7XHJcblx0ICAgICAgXHJcblx0ICAgICAgICB2YXIgbmV3VGV4dCA9IGRvY3VtZW50LmNyZWF0ZVRleHROb2RlKG0pO1xyXG5cdCAgICAgICAgdGQuYXBwZW5kQ2hpbGQobmV3VGV4dCk7XHJcblx0ICAgICAgICB0ZC5zZXRBdHRyaWJ1dGUoXCJjb2xzcGFuXCIsIDcpOyAgXHJcblx0ICAgICAgICB0ci5zZXRBdHRyaWJ1dGUoXCJpZFwiLCBcImlkX21lc1wiKTtcclxuXHQgICAgfVxyXG5cdCAgICBcclxuXHQgICAgZnVuY3Rpb24gY3JlYXRlQ2FsZW5kYXIoIHllYXIsIG1vbnRoKSB7XHJcblx0ICAgICAgICB2YXIgY2wxPScgY2xhc3M9XCJkdF8xXCInOyAgLy8gY9GC0LjQu9C4INC00LvRjyDRj9GH0LXQtdC6INGC0LDQsdC70LjRhtGLXHJcblx0ICAgICAgICB2YXIgY2wyPScgY2xhc3M9XCJkdF8yXCInOyAvLyBj0YLQuNC70Lgg0LTQu9GPINGP0YfQtdC10Log0YLQsNCx0LvQuNGG0Ysg0YEg0YHQvtCx0YvRgtC40Y/QvNC4XHJcblx0ICAgICAgICB2YXIgY2wzPScgY2xhc3M9XCJkdF8zXCInOyAgLy8g0L3QtdCw0LrRgtC40LLQvdGL0LUg0LTQvdC4INC00YDRg9Cz0L7Qs9C+INC80LXRgdGP0YbQsFxyXG5cdCAgICAgICAgdmFyIGFycl9tb250aD1bXCLQr9C90LLQsNGA0YxcIixcItCk0LXQstGA0LDQu9GMXCIsXCLQnNCw0YDRglwiLFwi0JDQv9GA0LXQu9GMXCIsXCLQnNCw0LlcIixcItCY0Y7QvdGMXCIsXCLQmNGO0LvRjFwiLFwi0JDQstCz0YPRgdGCXCIsXCLQodC10L3RgtGP0LHRgNGMXCIsXCLQntC60YLRj9Cx0YDRjFwiLFwi0J3QvtGP0LHRgNGMXCIsXCLQlNC10LrQsNCx0YDRjFwiXTtcclxuXHJcblx0ICAgICAgICB2YXIgZWxlbSA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwiY2FsXCIpO1xyXG5cdCAgICAgICAgdmFyIG1vbiA9IG1vbnRoIC0gMTsgLy8g0LzQtdGB0Y/RhtGLINCyIEpTINC40LTRg9GCINC+0YIgMCDQtNC+IDExLCDQsCDQvdC1INC+0YIgMSDQtNC+IDEyXHJcblx0ICAgICAgICB2YXIgZCA9IG5ldyBEYXRlKHllYXIsIG1vbik7XHJcblx0ICAgICAgICB2YXIgdDE9JyA8YSBvbmNsaWNrPVwiY3JlYXRlQ2FsZW5kYXIoICcreWVhcisnLCcrKG1vbnRoLTEpKycgKVwiPiZsdDs8L2E+ICc7XHJcblx0ICAgICAgICB2YXIgdDI9JyA8YSBvbmNsaWNrPVwiY3JlYXRlQ2FsZW5kYXIoICcreWVhcisnLCcrKG1vbnRoKzEpKycgKVwiPj48L2E+ICc7XHJcblx0ICAgICAgICBpZiAobW9udGgrMT09MTMpIHQyPScgPGEgb25jbGljaz1cImNyZWF0ZUNhbGVuZGFyKCAnKyh5ZWFyKzEpKycsMSApXCI+PjwvYT4gJztcclxuXHQgICAgICAgIGlmIChtb250aC0xPT0wKXQxPScgPGEgb25jbGljaz1cImNyZWF0ZUNhbGVuZGFyKCAnKyh5ZWFyLTEpKycsMTIgKVwiPiZsdDs8L2E+ICc7XHJcblxyXG5cdCAgICAgICAgdmFyIHRhYmxlID0gJzx0YWJsZSAgaWQ9XCJ0YWJsZV9jYWxlbmRhclwiPjx0cj48dGQgY2xhc3M9XCJjaGFuZ2VfbW91bnRoXCIgY29sc3Bhbj1cIjdcIj4gJyt0MSsnPHNwYW4+JyArYXJyX21vbnRoW21vbnRoLTFdICsgeWVhciArICc8L3NwYW4+Jyt0MisnPC90ZD48L3RyPjx0ciBjbGFzcz1cImNhbGVuZGFyX2RheXNcIj48dGg+0L/QvTwvdGg+PHRoPtCy0YI8L3RoPjx0aD7RgdGAPC90aD48dGg+0YfRgjwvdGg+PHRoPtC/0YI8L3RoPjx0aD7RgdCxPC90aD48dGg+0LLRgTwvdGg+PC90cj48dHI+JztcclxuXHJcblx0ICAgICAgICAvLyDQt9Cw0L/QvtC70L3QuNGC0Ywg0L/QtdGA0LLRi9C5INGA0Y/QtCDQvtGCINC/0L7QvdC10LTQtdC70YzQvdC40LrQsFxyXG5cdCAgICAgICAgLy8g0Lgg0LTQviDQtNC90Y8sINGBINC60L7RgtC+0YDQvtCz0L4g0L3QsNGH0LjQvdCw0LXRgtGB0Y8g0LzQtdGB0Y/RhlxyXG5cdCAgICAgICAgLy8gKiAqICogfCAxICAyICAzICA0XHJcblx0ICAgICAgICB2YXIgZDIgPSBnZXRMYXN0RGF5T2ZNb250aCh5ZWFyLCBtb24tMSk7XHJcblxyXG5cdCAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBnZXREYXkoZCk7IGkrKykge1xyXG5cdCAgICAgICAgICAgIHZhciB3PWQyLWdldERheShkKStpKzE7XHJcblx0ICAgICAgICAgICAgdGFibGUgKz0gJzx0ZCAnK2NsMysnPicrdysnPC90ZD4nO1xyXG5cdCAgICAgICAgfVxyXG5cclxuXHQgICAgICAgIC8vINGP0YfQtdC50LrQuCDQutCw0LvQtdC90LTQsNGA0Y8g0YEg0LTQsNGC0LDQvNC4XHJcblx0ICAgICAgICB3aGlsZSAoZC5nZXRNb250aCgpID09IG1vbikge1xyXG5cdCAgICAgICAgICAgIHZhciBjbD1jbDE7XHJcblx0ICAgICAgICAgICAgdmFyIGlkPScnO1xyXG5cdCAgICAgICAgICAgIHZhciBrZXk9ZC5nZXREYXRlKCk7XHJcblx0ICAgICAgICAgICAgaWYoICBrZXkgaW4gYXJyX2FjdGlvbil7XHJcblx0ICAgICAgICAgICAgICAgICBjbD1jbDI7IFxyXG5cdCAgICAgICAgICAgICAgICAgaWQ9JyBpZD1cIicrZC5nZXREYXRlKCkrJ1wiJztcclxuXHQgICAgICAgICAgICB9XHJcblx0ICAgICAgICAgICAgXHJcblx0ICAgICAgICAgICAgdGFibGUgKz0gJzx0ZCAnK2NsK2lkKyc+JyAra2V5ICsgJzwvdGQ+JztcclxuXHJcblx0ICAgICAgICAgICAgaWYgKGdldERheShkKSAlIDcgPT0gNikgeyAvLyDQstGBLCDQv9C+0YHQu9C10LTQvdC40Lkg0LTQtdC90YwgLSDQv9C10YDQtdCy0L7QtCDRgdGC0YDQvtC60LhcclxuXHQgICAgICAgICAgICAgIHRhYmxlICs9ICc8L3RyPjx0cj4nO1xyXG5cdCAgICAgICAgICAgIH1cclxuXHJcblx0ICAgICAgICAgICAgZC5zZXREYXRlKGQuZ2V0RGF0ZSgpICsgMSk7ICBcclxuXHQgICAgICAgIH1cclxuXHJcblx0ICAgICAgICAvLyDQtNC+0LHQuNGC0Ywg0YLQsNCx0LvQuNGG0YMg0L/Rg9GB0YLRi9C80Lgg0Y/Rh9C10LnQutCw0LzQuCwg0LXRgdC70Lgg0L3Rg9C20L3QvlxyXG5cdCAgICAgICAgaWYgKGdldERheShkKSAhPSAwKSB7XHJcblx0ICAgICAgICAgICAgdmFyIHc9MTtcclxuXHQgICAgICAgICAgICBmb3IgKHZhciBpID0gZ2V0RGF5KGQpOyBpIDwgNzsgaSsrKSB7XHJcblx0ICAgICAgICAgICAgICB0YWJsZSArPSAnPHRkICcrY2wzKyc+Jyt3Kyc8L3RkPic7XHJcblx0ICAgICAgICAgICAgICB3Kys7XHJcblx0ICAgICAgICAgICAgfVxyXG5cdCAgICAgICAgfVxyXG5cclxuXHQgICAgICAgIC8vINC30LDQutGA0YvRgtGMINGC0LDQsdC70LjRhtGDXHJcblx0ICAgICAgICB0YWJsZSArPSAnPC90cj48L3RhYmxlPic7XHJcblxyXG5cdCAgICAgICAgLy8g0YLQvtC70YzQutC+INC+0LTQvdC+INC/0YDQuNGB0LLQsNC40LLQsNC90LjQtSBpbm5lckhUTUxcclxuXHQgICAgICAgIGVsZW0uaW5uZXJIVE1MID0gdGFibGU7XHJcblx0ICAgICAgICAkKCd0YWJsZSB0ZCcpLm1vdXNlb3ZlcihmdW5jdGlvbigpe1xyXG5cdCAgICAgICAgICAgIGUodGhpcyk7XHJcblx0ICAgICAgICAgICAgfSk7XHJcblx0ICAgICAgICB9XHJcblx0ICAgICAgICBcclxuXHQgICAgICAgIGZ1bmN0aW9uIGdldExhc3REYXlPZk1vbnRoKHllYXIsIG1vbnRoKSB7XHJcblx0ICAgICAgICAgICAgXHJcblx0ICAgICAgICBpZiAobW9udGg9PTExKSAge3llYXIrKzsgbW9udGg9MDsgICBcclxuXHQgICAgICAgIH1lbHNlIFxyXG5cdCAgICAgICAgaWYgKG1vbnRoPT0wKSAgIHt5ZWFyLS07IG1vbnRoPTExOyAgIH1cclxuXHQgICAgICAgICAgICBcclxuXHQgICAgICAgIHZhciBkYXRlID0gbmV3IERhdGUoeWVhciwgbW9udGggKyAxLCAwKTtcclxuXHQgICAgICAgICAgICByZXR1cm4gZGF0ZS5nZXREYXRlKCk7XHJcblx0ICAgICAgICB9XHJcblxyXG5cdCAgICAgICAgZnVuY3Rpb24gZ2V0RGF5KGRhdGUpIHsgLy8g0L/QvtC70YPRh9C40YLRjCDQvdC+0LzQtdGAINC00L3RjyDQvdC10LTQtdC70LgsINC+0YIgMCjQv9C9KSDQtNC+IDYo0LLRgSlcclxuXHQgICAgICAgICAgICB2YXIgZGF5ID0gZGF0ZS5nZXREYXkoKTtcclxuXHQgICAgICAgICAgICBpZiAoZGF5ID09IDApIGRheSA9IDc7XHJcblx0ICAgICAgICAgICAgcmV0dXJuIGRheSAtIDE7XHJcblx0ICAgICAgICB9XHJcblxyXG5cdCAgICAvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQgICAgY3JlYXRlQ2FsZW5kYXIoIDIwMTIsIDkpO1xyXG5cdFxyXG4vLyB9KTsiXSwiZmlsZSI6ImNhbGVuZGFyLmpzIn0=
