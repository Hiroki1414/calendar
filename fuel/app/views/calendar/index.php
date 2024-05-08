<!DOCTYPE html>
<html lang="ja" class="h-100">
<head>
    <?php echo Asset::css('bootstrap.min.css'); ?>
    <?php echo Asset::css('style.css'); ?>
    <?php
    $colorsConfig = include(APPPATH . 'config/calendar/config.php');
    $colors = $colorsConfig['colors'];
    ?>
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <title><?php echo $title; ?></title>
    <script src="https://knockoutjs.com/downloads/knockout-3.5.1.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body class="d-flex flex-column vh-100">

<div id="calendar" class="flex-grow-1"></div>

<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="eventTitle">Title</label>
            <input type="text" class="form-control" id="eventTitle" data-bind="value: eventTitle">
          </div>
          <div class="form-group">
            <label for="eventStart">Start</label>
            <input type="datetime-local" class="form-control" id="eventStart" data-bind="value: eventStart">
          </div>
          <div class="form-group">
            <label for="eventEnd">End</label>
            <input type="datetime-local" class="form-control" id="eventEnd" data-bind="value: eventEnd">
          </div>
          <div class="form-group">
            <label for="colorSelection">Color</label>
            <select class="form-control" id="colorSelection" data-bind="options: availableColors, optionsText: 'name', optionsValue: 'key', value: selectedColor"></select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bind="click: saveEvent">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var colorsArray = <?php echo json_encode($colors); ?>;
        var getColorCode = function(colorName) {
        return colorsArray[colorName] ? colorsArray[colorName].color : '#333333';
        };
        var calendar = new FullCalendar.Calendar(calendarEl, {
            customButtons: {
                myCustomButton: {
                    text: 'events',
                    click: function() {
                        location.href='list';
                    }
                }
            },
            headerToolbar: {
                left: 'prev,next today myCustomButton',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            datesSet: function(dateInfo) {
                // ヘッダーセルを赤色に設定
                if (dateInfo.view.type === 'timeGridWeek' || dateInfo.view.type === 'timeGridDay') {
                    var headers = document.querySelectorAll('.fc-col-header-cell');
                    if (headers.length > 0) {
                        headers[0].style.color = '#ff0000';  // 週ビューでの日曜日
                        if (dateInfo.view.type === 'timeGridDay' && new Date().getDay() === 0) { // 日ビューで今日が日曜日の場合
                            headers[0].style.color = '#ff0000';
                        }
                    }
                }
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '/rest/calendar/list', 
                    type: 'GET',
                    contentType:"application/json; charset=utf-8",
                    success: function(response) {
                        if(response.status === 'success') {
                            var events = response.data.map(function(event) {
                                return {
                                    title: event.title,
                                    start: event.start,
                                    end: event.end,
                                    backgroundColor: getColorCode(event.color),
                                    borderColor: getColorCode(event.color) 
                                };
                            });
                            successCallback(events);
                        } else {
                            failureCallback();
                            console.error('スケジュールの取得に失敗:', response.message);
                        }
                    },
                    error: function(xhr) {
                        failureCallback();
                        console.error('スケジュールの取得中にエラーが発生しました:', xhr.responseText);
                    }
                });
            },
            navLinks: true,
            businessHours: true,
            editable: true, // イベントを編集可能に設定
            selectable: true, // 日付の範囲選択を可能に設定
            dateClick: function(info) {
                viewModel.eventTitle(''); // タイトルをリセット
                viewModel.eventStart(info.dateStr + 'T00:00'); // 開始時間をリセット
                viewModel.eventEnd(info.dateStr + 'T00:00'); // 終了時間をリセット
                viewModel.selectedDate(info.dateStr); // ViewModelに日付を更新
                $('#eventModal').modal('show'); // モーダルを表示
            },
        });
        calendar.render();

        function AppViewModel() {
            this.eventTitle = ko.observable();
            this.eventStart = ko.observable();
            this.eventEnd = ko.observable();
            this.selectedColor = ko.observable(); 
            this.selectedDate = ko.observable();
            this.availableColors = ko.observableArray(Object.keys(colorsArray).map(function(key) {
                return { key: key, name: colorsArray[key].name };
            }));

            // カレンダーにイベントを追加
            this.saveEvent = function(){
                var eventData = {
                    title: this.eventTitle(),
                    start: this.eventStart(),
                    end: this.eventEnd(),
                    color: this.selectedColor()
                };
                console.log(eventData);

                $.ajax({
                    url:'/rest/calendar/add',
                    type:'POST',
                    dataType: 'json',
                    data: eventData,
                    success: function(response) {
                        alert(response.message);
                        console.log(eventData);
                        $('#eventModal').modal('hide');
                        calendar.addEvent({
                            title: eventData.title,
                            start: eventData.start,
                            end: eventData.end,
                            backgroundColor: getColorCode(eventData.color),
                            borderColor: getColorCode(eventData.color)
                        });
                    },
                    error: function(xhr, status, error) {
                        alert('エラーが発生しました: ' + xhr.responseText);
                    }
                }); 
            };
        };
        // ViewModelをインスタンス化し、Knockout.jsに適用
        var viewModel = new AppViewModel();
        ko.applyBindings(viewModel);
    });
</script>
<?php echo Asset::js('bootstrap.min.js'); ?>
</body>
</html>
