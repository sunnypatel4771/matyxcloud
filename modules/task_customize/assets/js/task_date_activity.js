// Override the task single inline field change handler to support activity refresh
$(function () {
    // Remove the default handler from main.js
    $("body").off("change", ".task-single-inline-field");

    // Add our custom handler that processes the response
    $("body").on("change", ".task-single-inline-field", function () {
        var singleDateInputs = $("body").find(".task-single-inline-field");
        var data = {};
        $.each(singleDateInputs, function () {
            var name = $(this).attr("name");
            var val = $(this).val();
            var $parentwrap = $(this).parents(".task-single-inline-wrap");
            if (name == "startdate" && val === "") {
                $parentwrap.addClass("text-danger");
            } else if (name == "startdate" && val !== "") {
                $parentwrap.removeClass("text-danger");
            }
            if ((name == "startdate" && val !== "") || name != "startdate") {
                data[$(this).attr("name")] = val;
                // Name is required
                if (name != "startdate" && val === "") {
                    $parentwrap.css("opacity", 0.5);
                } else {
                    $parentwrap.css("opacity", 1);
                }
            }
        });
        var $taskModal = $("#task-modal");
        var dTaskID = $taskModal
            .find("[data-task-single-id]")
            .attr("data-task-single-id");

        // Post and handle response like add_task_assignees does
        $.post(admin_url + "tasks/task_single_inline_update/" + dTaskID, data).done(function (response) {
            if (response) {
                try {
                    var res = JSON.parse(response);
                    if (res.success && res.taskHtml) {
                        // Refresh the task modal with new HTML (includes updated activity)
                        _task_append_html(res.taskHtml);
                    }
                } catch (e) {
                    // Response might not be JSON if no date was changed
                    console.log("Task updated");
                }
            }
        });
    });

    // Handle work_planned date changes
    $(document).on("change", "#task-single-work_planned", function (event) {
        event.stopImmediatePropagation();

        var $this = $(this);
        var newVal = $this.val();
        var oldVal = $this.data("lastValue");

        if (newVal === oldVal) {
            return;
        }

        $this.data("lastValue", newVal);
        var task_id = $this.data("task_id");
        var field_id = $this.data("field_id");

        if ($this.data("processing")) return;
        $this.data("processing", true);

        $.ajax({
            url: admin_url + "task_customize/update_custom_field_value",
            type: "POST",
            data: {
                val: newVal,
                task_id: task_id,
                field_id: field_id
            },
            success: function (res) {
                reload_tasks_tables();

                // Reload the task modal to show updated activity
                // setTimeout(function () {
                //     init_task_modal(task_id);
                // }, 500);
            },
            complete: function () {
                $this.data("processing", false);
            }
        });
    });
});
