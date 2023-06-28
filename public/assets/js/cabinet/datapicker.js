$(document).ready(function () {
    let newDate = 0
    $('.dataPicker').click(function (e) { 
        e.preventDefault();
        datapicker(newDate);
        $('.dataPopup').fadeIn();
    });
    $('.dataBg').click(function (e) { 
        e.preventDefault();
        $('.dataPopup').fadeOut();
    });
    $('.pickerComplete').click(function (e) { 
        e.preventDefault();
        
        let day = $('input[name=day]').val().padStart(2, '0');
        let month = $('input[name=month]').val().padStart(2, '0');
        let year = $('input[name=year]').val();
        let hour = $('input[name=hour]').val().padStart(2, '0');
        let minute = $('input[name=minute]').val().padStart(2, '0');
        
        let message = `${day}.${month}.${year} в ${hour}:${minute}`;
        let date = `${year}-${month}-${day} ${hour}:${minute}`;
        
        $('.dataPickerText').html(message);
        $('input[name="date"]').val(date);
        $('.dataPopup').fadeOut();
    });
    const datapicker = (newDate) =>{
        const monthNames = ["Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря"];
        let date = new Date();
        let dateArray, timeArray, monthName, year, nextYear;
        if (newDate == 0) {
            let currentDate = new Date().toLocaleDateString("ru-ru", {timeZone: "Europe/Moscow"});
            let currentTime = new Date().toLocaleTimeString("ru-ru", {timeZone: "Europe/Moscow"});
            dateArray = currentDate.split('.');
            timeArray = currentTime.split(':');
            monthName = monthNames[date.getMonth()];
            year = dateArray[2];
            nextYear = parseInt(year) + 1;
            $('input[name=day]').prop('min', 1);
            $('input[name=month]').prop('min', 1);
            $('input[name=month]').prop('max', 12);
            $('input[name=hour]').prop('min', 0);
            $('input[name=hour]').prop('max', 23);
            $('input[name=minute]').prop('min', 0);
            $('input[name=minute]').prop('max', 59);
            $('input[name=year]').prop('min', year);
            $('input[name=year]').prop('max', nextYear);
        }else{
            let dd = newDate.getDate();
            let mm = newDate.getMonth() + 1;
            let yy = newDate.getFullYear();
            dateArray = [dd, mm, yy];
            let hh = newDate.getHours();
            let ii = newDate.getMinutes();
            timeArray = [hh, ii];
            monthName = monthNames[newDate.getMonth()];
        }
        let days = dateArray[0];
        let month = dateArray[1];
        year = dateArray[2];
        nextYear = parseInt(year) + 1;
        let hour = timeArray[0];
        let minute = timeArray[1];
        minute = (minute < 10) ? 0 + minute : minute
        var daysPerMonth = new Date(year, month, 0).getDate();
        let message = `${days} ${monthName} ${year} в ${hour}:${minute} (МСК)`;
        $('.currentDate').html(message);
        $('input[name=day]').val(days);
        $('input[name=day]').prop('max', daysPerMonth);
        $('input[name=month]').val(month);
        $('input[name=year]').val(year);
        $('input[name=hour]').val(hour);
        $('input[name=minute]').val(minute);
    }
    $('.pickerBtn').click(function (e) { 
        e.preventDefault();
        let operation = $(this).text();
        let name = $(this).attr('data-name');
        let max = $(this).parent().children('input').attr('max');
        let min = $(this).parent().children('input').attr('min');
        let value = $(this).parent().children('input').val();
        if (operation == "+") {
            let newValue = parseInt(value) + 1;
            if (newValue <= max) {
                $(this).parent().children('input').val(newValue);
            }else{
                $(this).parent().children('input').val(min);
            }
        }else if (operation == "-") {
            let newValue = parseInt(value) - 1;
            if (newValue >= min) {
                $(this).parent().children('input').val(newValue);
            }else{
                $(this).parent().children('input').val(max);
            }
        }
        let day = $('input[name=day]').val().padStart(2, '0');
        let month = $('input[name=month]').val().padStart(2, '0');
        let year = $('input[name=year]').val();
        let hour = $('input[name=hour]').val().padStart(2, '0');
        let minute = $('input[name=minute]').val().padStart(2, '0');
        newDate = new Date(year, month-1, day, hour, minute);
        datapicker(newDate);
    });
    $('.pickerText').keyup(function (e) {
        e.preventDefault();
        let max = $(this).attr('max');
        let min = $(this).attr('min');
        let value = $(this).val();
        if (value > max) {
            $(this).val(max);
        }else if(value < min){
            $(this).val(min);
        }else{
            let day = $('input[name=day]').val().padStart(2, '0');
            let month = $('input[name=month]').val().padStart(2, '0');
            let year = $('input[name=year]').val();
            let hour = $('input[name=hour]').val().padStart(2, '0');
            let minute = $('input[name=minute]').val().padStart(2, '0');
            newDate = new Date(year, month-1, day, hour, minute);
            datapicker(newDate);
        }
    });
});