$('#reminder-form').submit(function(e) {
    e.preventDefault();
    var description = $('#description').val();
    var startDate = $('#start-date').val();
    var frequency = $('#frequency').val();

    $.ajax({

        url: 'add_reminder.php',
        type: 'POST',
        data: {
            description: description,
            startDate: startDate,
            frequency: frequency
        },
        success: function(response) {
            console.log("Dise gut");
        },
        error: function() {
            console.log("Dise nit gut");
        }
        
    });

});



