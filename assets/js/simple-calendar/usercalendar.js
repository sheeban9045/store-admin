$(document).ready(function() {

    var calendar = $('#calendar').fullCalendar({
        allDayDefault: false,
        editable: true,
        resizeable : true,
        // crm_event: site_url + "/event",
        // displayEventTime: true,
        
        

        events: available_events,

        // eventRender: function(event, element, view) {
        //     if (event.allDay === 'true') {
        //         event.allDay = true;
        //     } else {
        //         event.allDay = false;
        //     }
        // },
        selectable: true,
        // selectHelper: true,
        select: function(start, end, allDay) {

            var title = prompt('Add Leave Title:');

            if (title) {
                var start = $.fullCalendar.formatDate(start, "Y-MM-DD");
                var end = $.fullCalendar.formatDate(end, "Y-MM-DD");
                $.ajax({
                    url: site_url + "/vacations/add",
                    data: {
                        title: title,
                        start: start,
                        end: end,
                        client_id: client_id,
                        type: 'add'
                    },
                    type: "POST",
                    success: function(data) {
                        displayMessage("Leave Added Successfully!");

                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);

                        // alert(data);

                        // calendar.fullCalendar('renderEvent', {
                        //     // id: 5, //data.id,
                        //     title: title,
                        //     start: start,
                        //     end: end,
                        //     allDay: allDay
                        // }, true);

                        // calendar.fullCalendar('unselect');
                    }
                });
            }
        },

        eventDrop: function(event, delta) {
            var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
            var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD");

            $.ajax({
                url: site_url + '/vacations/update',
                data: {
                    title: event.title,
                    start: start,
                    end: end,
                    client_id: client_id,
                    type: 'update'
                },
                type: "POST",
                success: function(response) {
                    displayMessage("Event Updated Successfully");
                }
            });
        },
        eventClick: function(event) {
            var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
            var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD");

            var deleteMsg = confirm("Do you really want to delete?");
            if (deleteMsg) {
                $.ajax({
                    type: "POST",
                    url: site_url + '/vacations/delete',
                    data: {
                        id: event.id,
                        start: start,
                        end: end,
                        client_id: client_id,
                        type: 'delete'
                    },
                    success: function(response) {
                        displayMessage("Leave Deleted Successfully!");

                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);

                        // calendar.fullCalendar('removeEvents', event.id);
                        // displayMessage("Event Deleted Successfully");
                    }
                });
            }
        }

    });

});

function displayMessage(message) {
    toastr.success(message, 'Event');
}