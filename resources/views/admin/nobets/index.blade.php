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
                        <h1>Veteriner Nöbet Takvimi</h1>
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
                                                <div class="external-event bg-warning" id="{{ $vet->id }}"
                                                    data-id="{{ $vet->id }}">
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
            var today = new Date().toLocaleDateString('fr-CA');
            var dayColors = { // Günlere özel renkler
                "Sun": "#1E3A8A",
                "Mon": "#215EAA",
                "Tue": "#2478C6",
                "Wed": "#2A92E4",
                "Thu": "#3FA3F7",
                "Fri": "#5CAAFD",
                "Sat": "#72B5FF",
            };


            function string_to_dayIndex(date) {
                var date = new Date(date);
                return date.toLocaleDateString('en-US', {
                    weekday: 'short'
                });
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
                    const date = new Date(info.date);
                    const formattedDate = date.toLocaleDateString('fr-CA');

                    if (today === formattedDate) {
                        info.el.style.backgroundColor = "#f9c4c4"; // Bugün ün arkaplan rengi
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

                    @foreach ($vets as $vet)
                        @foreach ($vet->nobets as $nobet)
                            {
                                title: "{{ $vet->name }}",
                                extendedProps: {
                                    veteriner_id: "{{ $vet->id }}",
                                    old_date: "{{ $nobet->date }}"
                                },
                                id: "event-" + Date.now() + Math.floor(Math.random() * 10000),
                                start: new Date("{{ $nobet->date }}"),
                                backgroundColor: dayColors[string_to_dayIndex("{{ $nobet->date }}")],
                                borderColor: dayColors[string_to_dayIndex("{{ $nobet->date }}")],
                                allDay: true,
                            },
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

                eventReceive: function(info) { // YENİ BİR ÖĞE EKLENDİĞİNDE

                    var newEvent = info.event;
                    var newEventId = 'event-' + Date.now() + Math.floor(Math.random() * 10000);
                    newEvent.setProp('id', newEventId);
                    newEvent.setExtendedProp('veteriner_id', info.draggedEl.id);
                    newEvent.setExtendedProp('old_date', info.event.startStr);

                    var existingEvents = calendar.getEvents();

                    var ids = existingEvents.map(function(event) {
                        return {
                            'id': event.id,
                            'title': event.title,
                            'date': event.start.toLocaleDateString('fr-CA')
                        }
                    });

                    var isDuplicate = false;

                    // Hata mesajını verebilmek için;
                    // * id'ler farklı olmalı ki yeni bir öğenin eklendiği anlaşılsın
                    // * title ve date aynı olusun ki aynı güne 2 aynı isim eklenmiş olsun
                    ids.forEach(event => {
                        if (event.id !== newEvent.id) {
                            if (event.date == newEvent.start.toLocaleDateString('fr-CA')) {
                                if (event.title.trim().toLowerCase() === newEvent.title.trim()
                                    .toLowerCase()) {
                                    isDuplicate = true;
                                }
                            }
                        }
                    })


                    if (isDuplicate) {
                        alert("Bu gün için seçilen kullanıcı zaten ekli!");
                        info.event.remove(); // Aynı olanı kaldır

                    } else {
                        var date = new Date(info.event.startStr);
                        date = date.toLocaleDateString('fr-CA');

                        var vet_id = info.draggedEl.id;

                        create_nobet(vet_id, date);

                    }
                },

                initialView: 'dayGridMonth', // Başlangıçta haftalık görünüm olsun
                slotMinTime: "16:00:00", // En erken gösterilecek saat
                slotMaxTime: "23:00:00", // En geç gösterilecek saat

                eventDidMount: function(info) {

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
                    deleteDiv.classList.add('delete_btn');


                    // Silme butonu oluştur
                    var deleteBtn = document.createElement("span");
                    deleteBtn.innerHTML = "❌"; // Buton simgesi
                    deleteBtn.style.cursor = "pointer";
                    deleteBtn.style.color = "red";


                    // Silme butonuna tıklanınca işlemi gerçekleştir
                    deleteBtn.addEventListener("click", function(event) {
                        event.stopPropagation(); // Takvimdeki diğer işlemleri engelle

                        var vet_id = info.event._def.extendedProps.veteriner_id;
                        var date = info.event.startStr;

                        if (confirm("Bu nöbeti silmek istiyor musunuz?")) {
                            delete_nobet(vet_id, date, function(result) {
                                if (result === 1) {
                                    console.log("Silme işlemi başarılı");
                                    info.event.remove();
                                } else {
                                    console.log("Silme işlemi başarısız");
                                }
                            });
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

            calendar.on('eventDrop', function(info) { // EKLİ ÖĞENİN YERİ DEĞİŞİRSE
                var new_date = info.event.startStr;

                var old_date = info.event._def.extendedProps.old_date;
                var vet_id = info.event._def.extendedProps.veteriner_id;

                info.event.setExtendedProp('old_date', new_date);

                // Bir nöbet güncellenirse anında değişikliği ilet

                console.log(vet_id, old_date, new_date);
                edit_nobet(vet_id, old_date, new_date);
            });

        })




        // POST FUNCS
        function create_nobet(vet_id, date) {
            $.ajax({
                url: "{{ route('admin.nobet.veteriner.created') }}", // Laravel rotası
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    _token: "{{ csrf_token() }}",
                    vet_id: vet_id,
                    date: date
                }),
                success: function(response) {
                    if (response.success) {
                        alert("Nöbetçi eklendi!");
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

        function edit_nobet(vet_id, old_date, new_date) {
            $.ajax({
                url: "{{ route('admin.nobet.veteriner.edited') }}", // Laravel rotası
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    _token: "{{ csrf_token() }}",
                    vet_id: vet_id,
                    old_date: old_date,
                    new_date: new_date
                }),
                success: function(response) {
                    if (response.success) {
                        alert("Nöbetçi düzenlendi!");
                        //window.location.reload();
                    } else {
                        console.log(response.message);
                    }
                },
                error: function(xhr) {
                    console.error("Hata:", xhr.responseText);
                }
            });
        }

        function delete_nobet(vet_id, date, sonuc) {
            $.ajax({
                url: "{{ route('admin.nobet.veteriner.deleted') }}", // Laravel rotası
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    _token: "{{ csrf_token() }}",
                    vet_id: vet_id,
                    date: date
                }),
                success: function(response) {
                    if (response.success) {
                        sonuc(1);
                        //alert("Nöbetçi silindi!");
                        //window.location.reload();
                    } else {
                        console.log(response.message);
                        sonuc(0);
                    }
                },
                error: function(xhr) {
                    sonuc(0);
                    console.error("Hata:", xhr.responseText);
                }
            });
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
