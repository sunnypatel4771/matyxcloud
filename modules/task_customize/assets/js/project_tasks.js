$(function() {

    init_editor("#comment");
    $(".table-rel-tasks").on('draw.dt', function() {
        init_selectpicker();
        init_datepicker();

    });

    $(document).on("change", "#task-single-work_planned", function(event) {
        event.stopImmediatePropagation(); // Prevent duplicate triggers

        var $this = $(this);
        var newVal = $this.val();
        var oldVal = $this.data("lastValue"); // Store last value

        // Prevent duplicate calls if value hasn't actually changed
        if (newVal === oldVal) {
            console.log("Duplicate value detected, ignoring...");
            return;
        }

        $this.data("lastValue", newVal); // Update stored value
        var task_id = $this.data("task_id");
        var field_id = $this.data("field_id");

        if ($this.data("processing")) return;
        $this.data("processing", true);

        console.log("Event Triggered on Change:", newVal); // Debugging

        $.ajax({
            url: admin_url + "task_customize/update_custom_field_value",
            type: "POST",
            data: {
                val: newVal,
                task_id: task_id,
                field_id: field_id
            },
            success: function(res) {
                console.log("AJAX Success", res);
                reload_tasks_tables();
            },
            complete: function() {
                $this.data("processing", false);
            }
        });
    });


    $('#task-comment-modal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var task_id = button.data('task-id');

        //get task comments
        $.post(admin_url + 'task_customize/get_task_comments', {
            task_id: task_id
        }).done(function(response) {
            var res = JSON.parse(response);
            if (res.status == true) {
                var comments = res.comments;
                $('.task-comment-history-body').html(comments);
            }
        });

        $('#task_id_comment').val(task_id);
    });

    //model hidden reset form
    $('#task-comment-modal').on('hidden.bs.modal', function() {
        $('#task-comment-form').trigger("reset");
        $('#task-comment-form button[type="submit"]').prop('disabled', false);

    });

    //task-comment-form submit
    $('#task-comment-form').submit(function(event) {
        //save button make disabled
        $('#task-comment-form button[type="submit"]').prop('disabled', true);
        event.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        $.post(url, data).done(function(success) {
            var res = JSON.parse(success);
            if (res.status == true) {
                alert_float('success', res.message);
                //reload table
                reload_tasks_tables();
                $('#task-comment-modal').modal('hide');
            } else {
                //save button make enabled
                $('#task-comment-form button[type="submit"]').prop('disabled', false);

                alert_float('danger', res.message);
            }
        });
    });


});