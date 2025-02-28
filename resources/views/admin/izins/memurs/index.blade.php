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
                        <h1>Memur İzin Takvimi</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.izin.memur.create') }}"><button
                                        class="btn btn-primary">Yeni İzin Ekle</button></a>
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
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        @include('admin.layouts.messages')
                        <div class="card card-primary">
                            <div class="card-body p-0">
                                <!-- THE CALENDAR -->
                                <div id="calendar"></div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-1"></div>

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
            today = today.toLocaleDateString('fr-CA');

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

            var calendarEl = document.getElementById('calendar');

            // initialize the external events
            // -----------------------------------------------------------------


            calendar = new Calendar(calendarEl, {
                locale: "tr",
                buttonText: {
                    today: "Bugün",
                    month: "Aylık",
                    week: "Haftalık",
                    day: "Günlük",
                    list: "Listele",
                },

                dayCellDidMount: function(info) { // günleri boyama
                    const date = new Date(info.date);
                    const formattedDate = date.toLocaleDateString('fr-CA');
                    if (today === formattedDate) {
                        info.el.style.backgroundColor = "#f9c4c4"; // Açık kırmızı arka plan
                    }

                },

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,list', //timeGridWeek
                },
                themeSystem: 'bootstrap',
                //Random default events
                events: [

                    @foreach ($memurs as $memur)
                        @foreach ($memur->izins as $izin)
                            {
                                title: "{{ $memur->name }} | {{ $izin->name }}",
                                extendedProps: {
                                    veteriner_id: "{{ $memur->id }}",
                                    izin_id: "{{ $izin->id }}"
                                },
                                start: "{{ \Carbon\Carbon::parse($izin->pivot->startDate)->format('Y-m-d\TH:i:s') }}",
                                end: "{{ \Carbon\Carbon::parse($izin->pivot->endDate)->format('Y-m-d\TH:i:s') }}",
                                backgroundColor: "#2A92E4",
                                borderColor: "#2A92E4",
                            },
                        @endforeach
                    @endforeach
                ],
                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar !!!




                initialView: 'dayGridMonth', // Başlangıçta haftalık görünüm olsun
                slotMinTime: "00:00:00", // En erken gösterilecek saat
                slotMaxTime: "23:59:00", // En geç gösterilecek saat
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
                    deleteDiv.style.justifyContent = "flex-end";
                    deleteDiv.style.alignItems = "center";

                    // Silme butonu oluştur
                    var deleteBtn = document.createElement("span");
                    deleteBtn.innerHTML = "❌"; // Buton simgesi
                    deleteBtn.style.cursor = "pointer";
                    deleteBtn.style.color = "red";
                    deleteBtn.style.position = "end";

                    // Silme butonuna tıklanınca işlemi gerçekleştir
                    deleteBtn.addEventListener("click", function(event) {
                        event.stopPropagation(); // Takvimdeki diğer işlemleri engelle
                        var startD = new Date(info.event.startStr);
                        var endD = new Date(info.event.endStr);
                        endD.setHours(endD.getHours() + 3); // Zaman farkı için
                        startD.setHours(startD.getHours() + 3); // Zaman farkı için

                        var end_date = endD.toISOString().split("T")[0];
                        var end_hour = endD.toISOString().split("T")[1].split(".")[0];
                        var start_date = startD.toISOString().split("T")[0];
                        var start_hour = startD.toISOString().split("T")[1].split(".")[0];

                        // Yeni tarih formatı oluşturma
                        end_date = end_date + ' ' + end_hour;
                        start_date = start_date + ' ' + start_hour;

                        if (confirm("Bu izini silmek istiyor musunuz?")) {
                            delete_izin(info.event._def.extendedProps.veteriner_id, info.event
                                ._def.extendedProps.izin_id, start_date, end_date);
                            info.event.remove();
                        }
                    });

                    // Elemanları birleştir
                    deleteDiv.appendChild(deleteBtn);
                    wrapper.appendChild(titleDiv);
                    wrapper.appendChild(deleteDiv);

                    // Ana elemana ekle
                    info.el.appendChild(wrapper);
                },


            });

            calendar.render();
            // $('#calendar').fullCalendar()

        })


        function delete_izin(user_id, izin_id, start_date, end_date) {
            $.ajax({
                url: "{{ route('admin.izin.memur.delete') }}", // Laravel rotası
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    _token: "{{ csrf_token() }}",
                    user_id: user_id,
                    izin_id: izin_id,
                    start_date: start_date,
                    end_date: end_date
                }),
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        console.log(response);
                    }
                },
                error: function(xhr) {
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
