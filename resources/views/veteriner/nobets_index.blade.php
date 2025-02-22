@extends('veteriner.layouts.app')
@section('veteriner.customCSS')
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/fullcalendar/main.css">
@endsection

@section('veteriner.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Nöbet Takvimi</h1>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-body p-0">
                                <!-- THE CALENDAR -->
                                <div id="calendar"></div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('veteriner.customJS')
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
                        info.el.style.backgroundColor = "#f9c4c4"; // Açık kırmızı arka plan
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

                    @foreach ($weeks as $week)
                        @php
                            $days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                            $colors = ['#215EAA', '#2478C6', '#2A92E4', '#3FA3F7', '#5CAAFD', '#72B5FF', '#1E3A8A']; // Günlere özel renkler
                        @endphp

                        @foreach ($days as $index => $day)
                            @foreach ($week->$day as $event)
                                @if ($event['vet_id'] == Auth::id())
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
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach
                ],
                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar !!!


                initialView: 'dayGridMonth', // Başlangıçta haftalık görünüm olsun
                slotMinTime: "16:00:00", // En erken gösterilecek saat
                slotMaxTime: "23:00:00", // En geç gösterilecek saat

            });

            calendar.render();





        })
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
