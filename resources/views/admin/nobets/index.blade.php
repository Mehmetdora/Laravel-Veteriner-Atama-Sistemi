@extends('admin.layouts.app')
@section('admin.customCSS')
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/fullcalendar/main.css">
@endsection

@section('admin.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Nöbet Listesi</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#"><button onclick="exportCalendarToPDF()">Takvimi PDF
                                        Olarak Kaydet</button></a>
                            </li>


                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="sticky-top mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Sürükle Bırak</h4>
                                </div>
                                <div class="card-body">
                                    <!-- the events -->
                                    <div id="external-events">
                                        @if (isset($vets))
                                            @foreach ($vets as $vet)
                                                <div class="external-event bg-warning" id="{{ $vet->id }}">
                                                    {{ $vet->name }}</div>
                                            @endforeach
                                        @endif
                                        <div class="checkbox" style="display:none">
                                            <label for="drop-remove">
                                                <input type="checkbox" id="drop-remove">
                                                remove after drop
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="card">
                                <a class="btn btn-primary card-title" onclick="getModifiedWeeks()">
                                    Kaydet
                                </a>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="card card-primary">
                            <div class="card-body p-0">
                                <!-- THE CALENDAR -->
                                <div id="calendar"></div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('admin.customJS')
    <script src="{{ asset('admin_Lte/') }}/plugins/fullcalendar/main.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/moment/moment.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin_Lte/') }}/plugins/jquery-ui/jquery-ui.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"
        integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous">
    </script>

    <script>
        var calendar;

        $(function() {



            /* initialize the external events
             -----------------------------------------------------------------*/
            function ini_events(ele) {
                ele.each(function() {

                    // create an Event Object (https://fullcalendar.io/docs/event-object)
                    // it doesn't need to have a start or end
                    var eventObject = {
                        title: $.trim($(this).text()) // use the element's text as the event title
                    }

                    // store the Event Object in the DOM element so we can get to it later
                    $(this).data('eventObject', eventObject)

                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex: 1070,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0 //  original position after the drag
                    })

                })
            }

            ini_events($('#external-events div.external-event'))

            /* initialize the calendar
             -----------------------------------------------------------------*/
            //Date for the calendar events (dummy data)
            var date = new Date()
            var d = date.getDate(),
                m = date.getMonth(),
                y = date.getFullYear()

            var Calendar = FullCalendar.Calendar;
            var Draggable = FullCalendar.Draggable;

            var containerEl = document.getElementById('external-events');
            var checkbox = document.getElementById('drop-remove');
            var calendarEl = document.getElementById('calendar');

            // initialize the external events
            // -----------------------------------------------------------------

            new Draggable(containerEl, {
                itemSelector: '.external-event',
                eventData: function(eventEl) {
                    return {
                        title: eventEl.innerText,
                        backgroundColor: window.getComputedStyle(eventEl, null).getPropertyValue(
                            'background-color'),
                        borderColor: window.getComputedStyle(eventEl, null).getPropertyValue(
                            'background-color'),
                        textColor: window.getComputedStyle(eventEl, null).getPropertyValue('color'),
                    };
                }
            });

            calendar = new Calendar(calendarEl, {
                locale: "tr",
                buttonText: {
                    today: "Bugün",
                    month: "Aylık",
                    week: "Hafta",
                    day: "Gün",
                    list: "Liste",
                },

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth', //timeGridWeek
                },
                themeSystem: 'bootstrap',
                //Random default events
                events: [

                    @foreach ($nobetci_haftalari as $week)
                        @php
                            $days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                            $colors = ['#215EAA', '#2478C6', '#2A92E4', '#3FA3F7', '#5CAAFD', '#72B5FF','#1E3A8A']; // Günlere özel renkler
                        @endphp

                        @foreach ($days as $index => $day)
                            @foreach ($week->$day as $event)
                                {
                                    title: "{{ $event['vet_name'] }}",
                                    start: new Date("{{ $event['date'] }}"),
                                    backgroundColor: "{{ $colors[$index] }}",
                                    borderColor: "{{ $colors[$index] }}",
                                    allDay: true,
                                },
                            @endforeach
                        @endforeach
                    @endforeach
                ],
                editable: true,
                droppable: true, // this allows things to be dropped onto the calendar !!!
                drop: function(info) {
                    // is the "remove after drop" checkbox checked?
                    if (checkbox.checked) {
                        // if so, remove the element from the "Draggable Events" list
                        info.draggedEl.parentNode.removeChild(info.draggedEl);
                    }
                },

                eventReceive: function(info) {
                    var newEvent = info.event;

                    // Eğer event zaten bir id'ye sahipse, o zaman tekrar kontrolü yapma
                    if (newEvent.id) return;

                    // Yeni bir event ID oluştur,her öğenin farklı olması için
                    newEvent.setProp('id', 'event-' + Date.now());

                    var existingEvents = calendar.getEvents();
                    var isDuplicate = existingEvents.some(event =>
                        event.id !== newEvent.id &&
                        event.start.getTime() === newEvent.start.getTime() &&
                        event.title === newEvent.title
                    );


                    if (isDuplicate) {
                        alert("Bu bölmeye zaten aynı etkinlik eklenmiş!");
                        info.event.remove(); // Aynı olanı kaldır
                    }
                },

                initialView: 'dayGridMonth', // Başlangıçta haftalık görünüm olsun
                slotMinTime: "16:00:00", // En erken gösterilecek saat
                slotMaxTime: "23:00:00", // En geç gösterilecek saat
                eventDidMount: function(info) {

                    //info.el.innerHTML = ""; // Önceki içeriği temizle

                    // Ana div oluştur (Etkinliği 2 parçaya bölecek)
                    var wrapper = document.createElement("div");
                    wrapper.style.display = "flex";
                    wrapper.style.alignItems = "center";
                    wrapper.style.width = "100%";

                    // Etkinlik adını içeren div
                    var titleDiv = document.createElement("div");
                    titleDiv.style.width = "75%"; // Sol kısım (3/4)
                    titleDiv.style.overflow = "hidden";
                    titleDiv.style.textOverflow = "ellipsis";
                    titleDiv.style.whiteSpace = "nowrap";
                    //titleDiv.innerText = info.event.title;

                    // Silme butonunu içeren div
                    var deleteDiv = document.createElement("div");
                    deleteDiv.style.width = "25%"; // Sağ kısım (1/4)
                    deleteDiv.style.display = "flex";
                    deleteDiv.style.justifyContent = "center";
                    deleteDiv.style.alignItems = "center";

                    // Silme butonu oluştur
                    var deleteBtn = document.createElement("span");
                    deleteBtn.innerHTML = "❌"; // Buton simgesi
                    deleteBtn.style.cursor = "pointer";
                    deleteBtn.style.color = "red";

                    // Silme butonuna tıklanınca işlemi gerçekleştir
                    deleteBtn.addEventListener("click", function(event) {
                        event.stopPropagation(); // Takvimdeki diğer işlemleri engelle
                        if (confirm("Bu etkinliği silmek istiyor musunuz?")) {
                            info.event.remove();
                        }
                    });

                    // Elemanları birleştir
                    deleteDiv.appendChild(deleteBtn);
                    wrapper.appendChild(titleDiv);
                    wrapper.appendChild(deleteDiv);

                    // Ana elemana ekle
                    info.el.appendChild(wrapper);
                }
            });

            calendar.render();
            // $('#calendar').fullCalendar()

            /* ADDING EVENTS */
            var currColor = '#3c8dbc' //Red by default
            // Color chooser button
            $('#color-chooser > li > a').click(function(e) {
                e.preventDefault()
                // Save color
                currColor = $(this).css('color')
                // Add color effect to button
                $('#add-new-event').css({
                    'background-color': currColor,
                    'border-color': currColor
                })
            })
            $('#add-new-event').click(function(e) {
                e.preventDefault()
                // Get value and make sure it is not null
                var val = $('#new-event').val()
                if (val.length == 0) {
                    return
                }

                // Create events
                var event = $('<div />')
                event.css({
                    'background-color': currColor,
                    'border-color': currColor,
                    'color': '#fff'
                }).addClass('external-event')
                event.text(val)
                $('#external-events').prepend(event)

                // Add draggable funtionality
                ini_events(event)

                // Remove event from text input
                $('#new-event').val('')
            })



        })

        function getModifiedWeeks() {
            var existingEvents = calendar.getEvents();
            var today = new Date();
            var currentWeekStart = new Date(today.setDate(today.getDate() - today
                .getDay())); // Haftanın başlangıcı (Pazartesi)

            // 2 hafta öncesi ve 2 hafta sonrası dahil tüm haftaları belirle
            var startDate = new Date(currentWeekStart);
            startDate.setDate(startDate.getDate() - 14); // 2 hafta önce başla

            var endDate = new Date(currentWeekStart);
            endDate.setDate(endDate.getDate() + 21); // 2 hafta sonrası

            let weekGroups = {}; // Haftaları objeye ayıracağız

            existingEvents.forEach(event => {
                let eventStart = new Date(event.start);

                // Etkinlik belirtilen 5 hafta içinde mi?
                if (eventStart >= startDate && eventStart <= endDate) {
                    let weekName = getWeekNumber(eventStart); // Haftayı belirle

                    if (!weekGroups[weekName]) {
                        weekGroups[weekName] = {
                            weekName: weekName,
                            startOfWeek: getWeekStart(eventStart), // Haftanın başlangıç tarihi
                            endOfWeek: getWeekEnd(eventStart), // Haftanın bitiş tarihi
                            events: []
                        };
                    }

                    weekGroups[weekName].events.push({
                        vet_name: event.title,
                        date: eventStart.toISOString(),
                    });
                }
            });

            // Sadece değişiklik yapılmış (etkinlik eklenmiş) haftaları al
            save_users(Object.values(weekGroups).filter(week => week.events.length > 0));
        }


        function save_users(modifiedWeeks) {
            $.ajax({
                url: "{{ route('admin.nobet.edited') }}", // Laravel rotası
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    _token: "{{ csrf_token() }}",
                    modifiedWeeks: modifiedWeeks
                }),
                success: function(response) {
                    if (response.success) {
                        alert("Nöbetçi listesi başarıyla kaydedildi!");
                        window.location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    console.error("Hata:", xhr.responseText);
                }
            });
        }


        // 📌 Yardımcı Fonksiyonlar

        // Tarihin hangi hafta numarasına ait olduğunu bul
        function getWeekNumber(date) {
            let d = new Date(date);
            d.setHours(0, 0, 0, 0);
            d.setDate(d.getDate() - d.getDay() + 1); // Haftanın başlangıcını bul
            let startYear = new Date(d.getFullYear(), 0, 1);
            let weekNumber = Math.ceil((((d - startYear) / 86400000) + startYear.getDay() + 1) / 7);
            return `${d.getFullYear()}-W${weekNumber}`;
        }

        // Haftanın başlangıcını bul (Pazartesi)
        function getWeekStart(date) {
            let d = new Date(date);
            d.setDate(d.getDate() - d.getDay() + 1); // Haftanın Pazartesi'sini bul
            return d.toISOString().split('T')[0];
        }

        // Haftanın bitişini bul (Pazar)
        function getWeekEnd(date) {
            let d = new Date(date);
            d.setDate(d.getDate() - d.getDay() + 7); // Haftanın Pazar'ını bul
            return d.toISOString().split('T')[0];
        }
    </script>

    <script>
        function exportCalendarToPDF() {
            html2canvas(document.getElementById('calendar')).then(canvas => {
                var imgData = canvas.toDataURL('image/png');
                var pdf = new jsPDF('landscape'); // Yatay modda PDF oluştur
                pdf.addImage(imgData, 'PNG', 10, 10, 280, 150);
                pdf.save("Takvim.pdf");
            });
        }
    </script>
@endsection
