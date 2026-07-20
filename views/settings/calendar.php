<div class="main-container ace-save-state" id="main-container">

    <div class="main-content">
        <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="#">Home</a>
                    </li>
                    <li class="active">Calendar</li>
                </ul><!-- /.breadcrumb -->

                <div class="nav-search" id="nav-search">
                    <form class="form-search">
                        <span class="input-icon">
                            <input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input"
                                autocomplete="off" />
                            <i class="ace-icon fa fa-search nav-search-icon"></i>
                        </span>
                    </form>
                </div><!-- /.nav-search -->
            </div>

            <div class="page-content">

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="space"></div>

                                <div id="calendar"></div>
                            </div>

                            <div class="col-sm-3">
                                <div class="widget-box transparent">
                                    <div class="widget-header">
                                        <h4>To Do List</h4>
                                    </div>

                                    <div class="widget-body">
                                        <div class="widget-main no-padding">
                                            <div id="external-events">
                                                <div class="external-event label-grey" data-class="label-grey">
                                                    <i class="ace-icon fa fa-arrows"></i>
                                                    My Event 1
                                                </div>

                                                <div class="external-event label-success" data-class="label-success">
                                                    <i class="ace-icon fa fa-arrows"></i>
                                                    My Event 2
                                                </div>

                                                <div class="external-event label-danger" data-class="label-danger">
                                                    <i class="ace-icon fa fa-arrows"></i>
                                                    My Event 3
                                                </div>

                                                <div class="external-event label-purple" data-class="label-purple">
                                                    <i class="ace-icon fa fa-arrows"></i>
                                                    My Event 4
                                                </div>

                                                <div class="external-event label-yellow" data-class="label-yellow">
                                                    <i class="ace-icon fa fa-arrows"></i>
                                                    My Event 5
                                                </div>

                                                <div class="external-event label-pink" data-class="label-pink">
                                                    <i class="ace-icon fa fa-arrows"></i>
                                                    My Event 6
                                                </div>

                                                <div class="external-event label-info" data-class="label-info">
                                                    <i class="ace-icon fa fa-arrows"></i>
                                                    My Event 7
                                                </div>

                                                <label>
                                                    <input type="checkbox" class="ace ace-checkbox" id="drop-remove" />
                                                    <span class="lbl"> Remove after drop</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div>
</div>
<script src="assets/js/jquery-2.1.4.min.js"></script>

<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery-ui.custom.min.js"></script>
<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/fullcalendar.min.js"></script>
<script src="assets/js/bootbox.js"></script>
<script src="assets/js/ace-elements.min.js"></script>
<script src="assets/js/ace.min.js"></script>


<script type="text/javascript">
    jQuery(function($) {
        $('#external-events div.external-event').each(function() {
            var eventObject = {
                title: $.trim($(this).text())
            };
            $(this).data('eventObject', eventObject);
            $(this).draggable({
                zIndex: 999,
                revert: true,
                revertDuration: 0
            });
        });

        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        var calendar = $('#calendar').fullCalendar({
            buttonHtml: {
                prev: '<i class="ace-icon fa fa-chevron-left"></i>',
                next: '<i class="ace-icon fa fa-chevron-right"></i>'
            },

            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },

            events: [
                <?php echo $events ?>
            ],

            editable: true,
            droppable: true,
            drop: function(date) {
                var originalEventObject = $(this).data('eventObject');
                var $extraEventClass = $(this).attr('data-class');
                var copiedEventObject = $.extend({}, originalEventObject);
                copiedEventObject.start = date;
                copiedEventObject.allDay = false;
                if ($extraEventClass) copiedEventObject['className'] = [$extraEventClass];
                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                if ($('#drop-remove').is(':checked')) {
                    $(this).remove();
                }
            },

            selectable: true,
            selectHelper: true,

            select: function(start, end, allDay) {
                // Create the modal dynamically
                var modal =
                    '<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">\
            <div class="modal-dialog">\
                <div class="modal-content">\
                    <div class="widget-header">\
                        <h5 class="widget-title" id="modalTitle">New Event</h5>\
                        <div class="widget-toolbar">\
                            <span class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true" style="padding: 8px;font-size: 20px;color: black;">&times;</span>\
                        </div>\
                    </div>\
                    <form class="no-margin" id="newEventForm">\
                        <div class="modal-body">\
                            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />\
                            <input type="hidden" name="start_datetime" id="start_datetime" value="' + moment(start)
                    .format('YYYY-MM-DDTHH:mm') + '" />\
                            <input type="hidden" name="end_datetime" id="end_datetime" value="' + moment(end).format(
                        'YYYY-MM-DDTHH:mm') +
                    '" />\
\
                            <div class="form-group">\
                                <label for="title">Event Title</label>\
                                <input type="text" name="title" id="title" class="form-control" placeholder="Enter Event Title" required />\
                            </div>\
                            <div class="form-group">\
                                <label for="description">Description</label>\
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter Description"></textarea>\
                            </div>\
                            <div class="form-group">\
                                <label for="location">Location</label>\
                                <input type="text" name="location" id="location" class="form-control" placeholder="Enter Location" />\
                            </div>\
                            <div class="form-group">\
                                <label for="event_color">Event Color</label>\
                                <input class="form-control" type="color" name="event_color" id="event_color" value="#3fb50f" />\
                            </div>\
\
                            <!-- Start Date & Time Inputs -->\
                            <div class="form-group">\
                                <label for="start_time">Start Date & Time</label>\
                                <input class="form-control" type="datetime-local" name="start_time" id="start_time" value="' +
                    moment(start)
                    .format('YYYY-MM-DDTHH:mm') +
                    '" required />\
                            </div>\
\
                            <!-- End Date & Time Inputs -->\
                            <div class="form-group">\
                                <label for="end_time">End Date & Time</label>\
                                <input class="form-control" type="datetime-local" name="end_time" id="end_time" value="' +
                    moment(end).format(
                        'YYYY-MM-DDTHH:mm') + '" required />\
                            </div>\
                        </div>\
                        <div class="modal-footer">\
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>\
                            <button type="submit" class="btn btn-primary">Save Event</button>\
                        </div>\
                    </form>\
                </div>\
            </div>\
        </div>';

                // Append the modal to the body
                var eventModal = $(modal).appendTo('body');

                // Show the modal
                eventModal.modal('show');

                // Handle form submission to save the event
                eventModal.find('form').on('submit', function(ev) {
                    ev.preventDefault();

                    // Get event data from the form
                    var eventData = {
                        title: $('#title').val(),
                        description: $('#description').val(),
                        location: $('#location').val(),
                        event_color: $('#event_color').val(),
                        start_time: $('#start_time').val(),
                        end_time: $('#end_time').val()
                    };

                    // Add the event to FullCalendar
                    calendar.fullCalendar('renderEvent', {
                        title: eventData.title,
                        start: eventData.start_time,
                        end: eventData.end_time,
                        description: eventData.description,
                        location: eventData.location,
                        event_color: eventData.event_color,
                        className: 'label-info'
                    }, true);

                    // Close the modal
                    eventModal.modal('hide');

                    // Optionally, you can send the data to your backend here to persist the event in the database
                    $.ajax({
                        url: 'index.php?r=settings/calendar', // Adjust URL for your backend
                        method: 'POST',
                        data: eventData,
                        success: function(response) {
                            if (response.success) {
                                alert('Event saved successfully!');
                            } else {
                                alert('Failed to save the event.');
                            }
                        },
                        error: function() {
                            alert('Error while saving the event.');
                        }
                    });

                    // Optionally, unselect the calendar after saving
                    calendar.fullCalendar('unselect');
                });

                // Close the modal and remove it from the DOM once it’s hidden
                eventModal.on('hidden.bs.modal', function() {
                    eventModal.remove();
                });
            },


            eventClick: function(calEvent, jsEvent, view) {
                // Fetch additional event details from the server
                $.ajax({
                    url: 'index.php?r=settings/calendar',
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        id: calEvent.id // Send event ID to get specific details
                    },
                    success: function(data) {
                        if (data.success) {
                            // Create and show modal with event data

                            var modal =
                                '<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">\
        <div class="modal-dialog">\
            <div class="modal-content">\
                <div class="widget-header">\
                    <h5 class="widget-title" id="modalTitle">Event</h5>\
                    <div class="widget-toolbar">\
                        <span class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true" style="padding: 8px;font-size: 20px;color: black;">&times;</span>\
                    </div>\
                </div>\
                <form action="index.php?r=settings/calendar" method="POST" class="no-margin">\
                    <div class="modal-body">\
                        <input type="hidden" name="flag" id="flag" value="add_new" required />\
                        <input type="hidden" name="event_id" id="event_id" />\
\
                        <div class="form-group">\
                            <label for="title">Event Title</label>\
                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter Event Title" value="' +
                                data.event.title +
                                '" required />\
                        </div>\
\
                        <div class="form-group">\
                            <label for="description">Description</label>\
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter Description" required>' +
                                data.event.description +
                                '</textarea>\
                        </div>\
\
                        <div class="form-group">\
                            <label for="location">Location</label>\
                            <input type="text" name="location" id="location" class="form-control" placeholder="Enter Location" value="' +
                                data
                                .event.location +
                                '" required />\
                        </div>\
\
                        <div class="form-group">\
                            <label for="start_datetime">Start Date & Time</label>\
                            <input class="form-control" type="datetime-local" name="start_datetime" id="start_datetime" value="' +
                                moment(data.event.start_datetime).format(
                                    'YYYY-MM-DDTHH:mm') +
                                '" required />\
                        </div>\
\
                        <div class="form-group">\
                            <label for="end_datetime">End Date & Time</label>\
                            <input class="form-control" type="datetime-local" name="end_datetime" id="end_datetime" value="' +
                                moment(data
                                    .event.end_datetime).format('YYYY-MM-DDTHH:mm') + '" required />\
                        </div>\
\
                        <div class="form-group">\
                            <label for="event_color">Event Color</label>\
                            <input class="form-control" type="color" name="event_color" id="event_color" value="' +
                                data.event.event_color + '" />\
                        </div>\
\
                    </div>\
                    <div class="modal-footer">\
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>\
                        <button type="submit" name="save_event" value="true" class="btn btn-primary">Save Event</button>\
                    </div>\
                </form>\
            </div>\
        </div>\
    </div>';



                            var modal = $(modal).appendTo('body');

                            // Handle form submission for saving changes
                            modal.find('form').on('submit', function(ev) {
                                ev.preventDefault();

                                // Get updated event data from the form
                                var updatedEvent = {
                                    id: calEvent.id,
                                    title: $(this).find("input[type=text]")
                                        .val(),
                                    description: $(this).find("textarea").val(),
                                    location: $(this).find("input[type=text]")
                                        .eq(1).val(),
                                    start_datetime: $(this).find(
                                            "input[type=datetime-local]").eq(0)
                                        .val(),
                                    end_datetime: $(this).find(
                                            "input[type=datetime-local]").eq(1)
                                        .val(),
                                    event_color: $(this).find(
                                        "input[type=color]").val(),
                                };

                                // Send updated event data to the server for saving
                                $.ajax({
                                    url: 'index.php?r=settings/calendar', // Adjust URL for updating event in the database
                                    dataType: 'json',
                                    method: 'POST',
                                    data: updatedEvent,
                                    success: function(response) {
                                        if (response.success) {
                                            // Update FullCalendar event data after successful save
                                            calEvent.title =
                                                updatedEvent.title;
                                            calEvent.description =
                                                updatedEvent
                                                .description;
                                            calEvent.start_datetime =
                                                updatedEvent
                                                .start_datetime;
                                            calEvent.end_datetime =
                                                updatedEvent
                                                .end_datetime;
                                            calEvent.location =
                                                updatedEvent.location;
                                            calEvent.event_color =
                                                updatedEvent
                                                .event_color;

                                            calendar.fullCalendar(
                                                'updateEvent',
                                                calEvent);
                                            modal.modal("hide");
                                        } else {
                                            alert(
                                                'Failed to update the event.'
                                            );
                                        }
                                    },
                                    error: function() {
                                        alert(
                                            'Error updating the event.'
                                        );
                                    }
                                });
                            });

                            // Handle event deletion
                            modal.find('button[data-action=delete]').on('click',
                                function() {
                                    $.ajax({
                                        url: 'index.php?r=settings/calendar', // Adjust URL for deleting the event
                                        dataType: 'json',
                                        method: 'POST',
                                        data: {
                                            id: calEvent.id
                                        },
                                        success: function(response) {
                                            if (response.success) {
                                                calendar.fullCalendar(
                                                    'removeEvents',
                                                    calEvent.id);
                                                modal.modal("hide");
                                            } else {
                                                alert(
                                                    'Failed to delete the event.'
                                                );
                                            }
                                        },
                                        error: function() {
                                            alert(
                                                'Error deleting the event.'
                                            );
                                        }
                                    });
                                });

                            // Show the modal
                            modal.modal('show').on('hidden', function() {
                                modal.remove();
                            });
                        } else {
                            alert('Failed to load event details.');
                        }
                    },
                    error: function() {
                        alert('Error fetching event details.');
                    }
                });
            }
        });

    });
</script>