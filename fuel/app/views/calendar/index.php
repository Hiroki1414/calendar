<!DOCTYPE html>
<html lang="ja" class="h-100">
<head>
    <?php echo Asset::css('bootstrap.min.css'); ?>
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <title><?php echo $title; ?></title>
    <script src="https://knockoutjs.com/downloads/knockout-3.5.1.js"></script>
</head>
<body class="d-flex flex-column h-100">

<div id='calendar'></div>

<select data-bind="options: availableColors, optionsText: 'name', value: selectedColor, optionsCaption: '色を選択...'"></select>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function AppViewModel() {
            this.selectedDate = ko.observable();
            this.availableColors = ko.observableArray([
                { name: '赤', class: 'bg-danger' },
                { name: 'オレンジ', class: 'bg-warning' },
                { name: '青', class: 'bg-primary' },
                { name: '水色', class: 'bg-info' },
                { name: '緑', class: 'bg-success' },
                { name: '黒', class: 'bg-dark' },
                { name: 'グレー', class: 'bg-secondary' }
            ]);
            this.selectedColor = ko.observable(); // 選択された色
        }
    
        // ViewModelをインスタンス化し、Knockout.jsに適用
        var viewModel = new AppViewModel();
        ko.applyBindings(viewModel);
        
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: <?php echo json_encode($schedules); ?>,
            editable: true, // イベントを編集可能に設定
            selectable: true, // 日付の範囲選択を可能に設定

            dateClick: function(info) {
                // イベントタイトルの入力
                var title = prompt('イベントタイトルを入力してください:');
                if (!title) {
                    return;
                }

                // 持続時間（日数）の入力
                var days = prompt('イベントの持続日数を入力してください (1で終日):', '1');
                if (!days || isNaN(days)) {
                    return;
                }

                // 終了日時の計算
                var endDate = new Date(info.dateStr);
                endDate.setDate(endDate.getDate() + parseInt(days));

                // 色の指定
                var colorClass = viewModel.selectedColor() ? viewModel.selectedColor().class : '';
                if (!colorClass) {
                    alert('色を選択してください。');
                    return;
                }

                // カレンダーにイベントを追加
                let eventData = {
                    title: title,
                    start: info.dateStr,
                    end: endDate.toISOString().split('T')[0], // ISO形式の日付のみを使用
                    colorName: colorClass,
                };
                console.log(JSON.stringify(eventData));

                // const events = [
                //     {
                //         id: 
                //         start: 
                //         end: 
                //         title: 
                //         Color: 
                //         title:
                //     }
                // ];

                calendar.addEvent(eventData);

                $.ajax({
                    url:'/rest/calendar/add',
                    type:'POST',
                    contentType:"application/json",
                    data:JSON.stringify(eventData),
                    // dataType:'json',
                }).done(function(data) {
                    console.log(data)    
                        alert("ok");
                }).fail(function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(JSON.stringify(eventData));
                    console.log(textStatus);
                    console.log(errorThrown);      
                        alert("error");
                })
            } 
        });
        calendar.render();
    });
</script>

</body>
</html>
