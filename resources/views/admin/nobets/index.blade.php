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
                        <h1>Nöbet Listesi Takvimi</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#"><button class="btn btn-primary"
                                        onclick="exportCalendarToPDF()">Takvimi PDF
                                        Olarak İndir</button></a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <hr class="mt3-">
                <div class="row mb-3">
                    <h5 class="ml-1"><b>Uyarı:</b> Takvim üzerinde sadece boyalı günler için ekleme-düzenleme yapılabilir.
                    </h5>
                    <h5 class="ml-1"><b>Dikkat:</b> Takvim üzerinde değişiklikler yapıldıktan sonra kaydet butonu ile
                        kaydedilmelidir, aksi takdirde değişiklikler kayıt edilmez!
                    </h5>
                </div>

                <div class="row">
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

                    <div class="col-md-3">
                        <div class="sticky-top mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Veterinerler(Sürükle Bırak)</h4>
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
                                <a class="btn btn-primary card-title" onclick="saveWeeks()">
                                    Kaydet
                                </a>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    <!-- /.col -->

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

            //bugün ü belirleme
            var today = new Date();
            today.setDate(today.getDate());
            today = today.toISOString().split("T")[0];

            // Düzenleme yapılabilecek günlerin listesini oluşturma(bu günleri farklı renge boyamak için)
            var editable_days = [];
            var bugun = new Date();
            var buHafta = new Date(bugun.setDate(bugun.getDate() - bugun.getDay()));
            var ilk_gun = new Date(buHafta);
            ilk_gun.setDate(ilk_gun.getDate() - 14); // 2 hafta önce başla
            var son_gun = new Date(buHafta);
            son_gun.setDate(son_gun.getDate() + 20); // 2 hafta sonrası
            let gecici = new Date(ilk_gun);
            while (gecici <= son_gun) {
                editable_days.push(gecici.toISOString().split("T")[0]);
                gecici.setDate(gecici.getDate() + 1);
            }



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

                dayCellDidMount: function(info) { // günleri boyama
                    if (today == info.date.toISOString().split("T")[0]) {
                        info.el.style.backgroundColor = "#f9c4c4"; // Açık kırmızı arka plan
                    } else if (editable_days.includes(info.date.toISOString().split('T')[0])) {
                        info.el.style.backgroundColor = "#c4f9ce"; // Açık kırmızı arka plan
                    }
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
                            $colors = ['#215EAA', '#2478C6', '#2A92E4', '#3FA3F7', '#5CAAFD', '#72B5FF', '#1E3A8A']; // Günlere özel renkler
                        @endphp

                        @foreach ($days as $index => $day)
                            @foreach ($week->$day as $event)
                                {
                                    title: "{{ $event['vet_name'] }}",
                                    extendedProps: {
                                        veteriner_id: "{{ $event['vet_id'] }}"
                                    },
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
                    newEvent.setExtendedProp('veteriner_id', info.draggedEl.id);

                    var existingEvents = calendar.getEvents();
                    var isDuplicate = existingEvents.some(event =>
                        event.id !== newEvent.id &&
                        event.start.getTime() === newEvent.start.getTime() &&
                        event.title === newEvent.title
                    );

                    // Sadece günlerin renkli olarak belirtildiği günlerde ekleme yapılabilir
                    var isInEditableArea = !(editable_days.includes(newEvent.start.toISOString().split(
                        "T")[0]));


                    if (isInEditableArea) {
                        alert("Takvimde  belirtilen günler için düzenleme yapılabilir!");
                        info.event.remove();
                    }
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

        function saveWeeks() {
            var existingEvents = calendar.getEvents();
            var today = new Date();

            // Haftanın başlangıcını bul (Pazar)
            var currentWeekStart = new Date(today.setDate(today.getDate() - today.getDay()));

            // 2 hafta öncesi ve 2 hafta sonrası dahil tüm haftaları belirle
            var startDate = new Date(currentWeekStart);
            startDate.setDate(startDate.getDate() - 14); // 2 hafta önce başla

            var endDate = new Date(currentWeekStart);
            endDate.setDate(endDate.getDate() + 20); // 2 hafta sonrası

            let weekGroups = {}; // Haftaları objeye ayıracağız
            let weekNames = [];

            // 📌 **Önce tüm haftaları oluştur** (Boş olsa bile eklenecek)
            let tempDate = new Date(startDate);
            while (tempDate <= endDate) {
                let weekName = getWeekNumber(tempDate); // Haftayı belirle
                if (!weekGroups[weekName]) {
                    weekGroups[weekName] = {
                        weekName: weekName,
                        startOfWeek: getWeekStart(tempDate), // Haftanın başlangıç tarihi (Pazar)
                        endOfWeek: getWeekEnd(tempDate), // Haftanın bitiş tarihi (Cumartesi)
                        events: [] // Başlangıçta boş
                    };
                    weekNames.push(weekName);
                }
                tempDate.setDate(tempDate.getDate() + 7); // Haftaları artır
            }




            // 📌 **Şimdi etkinlikleri ilgili haftalara ekle**
            existingEvents.forEach(event => {
                console.log(event);
                let eventStart = new Date(event.start);
                let eventStartDay = new Date(eventStart.getTime() - eventStart.getTimezoneOffset() * 60000);

                //console.log(eventStartDay, ' Event Day');

                // Etkinlik belirtilen 5 hafta içinde mi?
                if (eventStartDay >= startDate && eventStartDay <= endDate) {
                    let weekName = getWeekNumber(eventStartDay); // Haftayı belirle

                    if (weekNames.includes(weekName)) { // 5 haftalık zaman içindeyse ekle
                        weekGroups[weekName].events.push({
                            vet_id: event._def.extendedProps.veteriner_id,
                            vet_name: event.title,
                            date: eventStartDay.toISOString().split("T")[0],
                        });
                    }
                }
            });

            //Tüm 5 haftayı içeren weekGroups'u logla**
            //console.log(weekGroups, ' Tüm 5 hafta verisi');

            // Haftaları backend'e kaydet**
            save_users(Object.values(weekGroups)); // Artık her hafta var(5 hafta her seferinde), boş olanlar da dahil
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
                        console.log(response.message);
                    }
                },
                error: function(xhr) {
                    console.error("Hata:", xhr.responseText);
                }
            });
        }


        // 📌 Yardımcı Fonksiyonlar

        // Tarihin hangi hafta numarasına ait olduğunu bul
        // Tarihin hangi hafta numarasına ait olduğunu bul (Hafta Pazar günü başlıyor)
        function getWeekNumber(date) {
            let d = new Date(date);
            d.setHours(0, 0, 0, 0);
            d.setDate(d.getDate() - ((d.getDay() + 6) % 7)); // Haftanın başlangıcını bul (Pazar)
            let startYear = new Date(d.getFullYear(), 0, 1);
            let weekNumber = Math.ceil((((d - startYear) / 86400000) + startYear.getDay()) / 7);
            return `${d.getFullYear()}-W${weekNumber}`;
        }

        // Haftanın başlangıcını bul (Pazar)
        function getWeekStart(date) {
            let d = new Date(date);
            d.setDate(d.getDate() - ((d.getDay() + 6) % 7)); // Pazar'ı bul

            //console.log(d.toISOString().split('T')[0], ' hafta başı');
            return d.toISOString().split('T')[0];
        }

        // Haftanın bitişini bul (Cumartesi)
        function getWeekEnd(date) {
            let d = new Date(date);
            d.setDate(d.getDate() - ((d.getDay() + 6) % 7) + 6); // Cumartesi'yi bul

            //console.log(d.toISOString().split('T')[0], ' hafta sonu');
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
