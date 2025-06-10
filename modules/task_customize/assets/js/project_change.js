$(function () {
    
  $("#projects").on('draw.dt', function () {
    init_selectpicker();
    // caret hide
    $('.caret').hide();
  });
  // status_notes textarea
  $(document).on('keyup', '.status_notes', function () {
    var custom_field_id = $(this).data('custom-field-id');
    var project_id = $(this).data('project-id');
    var value = $(this).val();
    // Replace spaces with dashes
    if (value.indexOf(' ') !== -1) {
      value = value.replace(/ /g, '-');
    }
    project_change_custom_field_value(project_id, custom_field_id, value);
  });

});

// project_mark_as function
function project_mark_as(status, project_id) {
  $.post(admin_url + 'projects/mark_as', {
    status_id: status,
    project_id: project_id,
    notify_project_members_status_change: 1,
    mark_all_tasks_as_completed: 0,
    cancel_recurring_tasks: 'false',
    send_project_marked_as_finished_email_to_contacts: 0
  }, function (response) {
    $("body").find(".dt-loader").remove();
    if (response.success) {
      $('#projects').DataTable().ajax.reload();
    } else {
      $('#projects').DataTable().ajax.reload();
    }
  });
}

// project_change_custom_field_value function
function project_change_custom_field_value(project_id, custom_field_id, value) {
  url = admin_url + 'task_customize/project_change_custom_field_value/' + project_id + '/' + custom_field_id + '/' + value;
  $("body").append('<div class="dt-loader"></div>');

  $.ajax({
    url: url,
    type: 'POST',
    success: function (response) {
      var response = JSON.parse(response);
      if (response.success) {
        $("body").find(".dt-loader").remove();
        $('#projects').DataTable().ajax.reload();
      } else {
        $("body").find(".dt-loader").remove();
        alert(response.message);
      }
    }
  });
}

function project_change_custom_field_value_multiselect(project_id, custom_field_id, value) {
  url = admin_url + 'task_customize/project_change_custom_field_value_multiselect/' + project_id + '/' + custom_field_id;
  $("body").append('<div class="dt-loader"></div>');
  $.ajax({
    url: url,
    type: 'POST',
    data: {
      value: value
    },
    success: function (response) {
      $("body").find(".dt-loader").remove();
      $('#projects').DataTable().ajax.reload();
    },
    error: function (response) {
      $("body").find(".dt-loader").remove();
      alert(response.message);
    }
  });

}
