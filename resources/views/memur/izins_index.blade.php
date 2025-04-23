@extends('memur.layouts.app')
@section('memur.customCSS')
    <link rel="stylesheet" href="{{ asset('admin_Lte/') }}/plugins/fullcalendar/main.css">
@endsection

@section('memur.content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><b>İzin Takvimi</b></h1>
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
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
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
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('memur.customJS')
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



            calendar = new Calendar(calendarEl, {
                locale: "tr",
                buttonText: {
                    today: "Bugün",
                    month: "Aylık",
                    week: "Hafta",
                    day: "Günlük",
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
                    right: 'dayGridMonth,timeGridWeek,list', //timeGridWeek
                },

                themeSystem: 'bootstrap',

                events: [
                    @if (isset($izins))
                        @foreach ($izins as $izin)
                            {
                                title: "{{ Auth::user()->name }} | {{ $izin->name }}",
                                start: "{{ \Carbon\Carbon::parse($izin->pivot->startDate)->format('Y-m-d\TH:i:s') }}",
                                end: "{{ \Carbon\Carbon::parse($izin->pivot->endDate)->format('Y-m-d\TH:i:s') }}",
                                backgroundColor: "#2A92E4",
                                borderColor: "#2A92E4",
                            },
                        @endforeach
                    @endif
                ],
                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar !!!


                initialView: 'dayGridMonth', // Başlangıçta haftalık görünüm olsun
                slotMinTime: "00:00:00", // En erken gösterilecek saat
                slotMaxTime: "23:59:00", // En geç gösterilecek saat

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
