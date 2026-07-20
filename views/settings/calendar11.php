<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Calendar</title>
    <!-- jQuery (required by FullCalendar) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@3.2.0/main.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@3.2.0/main.min.css" rel="stylesheet" />

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@3.2.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@3.2.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@3.2.0/main.min.js"></script>

</head>

<body>

    <!-- Calendar Container -->
    <div id="calendar"></div>
    <!-- jQuery (required by FullCalendar) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@3.2.0/main.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@3.2.0/main.min.css" rel="stylesheet" />

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@3.2.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@3.2.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@3.2.0/main.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: function(start, end, timezone, callback) {
                    $.ajax({
                        url: 'index.php?r=settings/calendar', // Update with your actual PHP endpoint
                        dataType: 'json',
                        method: 'POST',
                        success: function(data) {
                            var events = data.map(function(event) {
                                return {
                                    id: event.id,
                                    title: event.title,
                                    start: event.start,
                                    end: event.end,
                                    location: event.location,
                                    color: event.color
                                };
                            });
                            callback(events);
                        }
                    });
                },
                eventRender: function(event, element) {
                    element.attr('title', event.location); // Optionally, show location in the tooltip
                }
            });
        });
    </script>

</body>

</html>