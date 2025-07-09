$(function () {

  $("#projects").on('draw.dt', function () {
    init_selectpicker();
    // caret hide
    $('.caret').hide();
    init_datepicker();
  });


  //project_status_note open modal
  $(document).on('click', '.project_status_note', function () {
    var projectId = $(this).data('project-id');
    $.post(admin_url + 'task_customize/get_project_comments', {
      project_id: projectId
    }).done(function (response) {
      var res = JSON.parse(response);
      if (res.status == true) {
        var comments = res.comments;
        $('.project-comment-history-body').html(comments);
        $('#project_id_comment').val(projectId);
        $('#project-comment-modal').modal('show');
      }
    });

  });

  //project-comment-form submit
  $('#project-comment-form').submit(function (event) {
    //save button make disabled
    $('#project-comment-form button[type="submit"]').prop('disabled', true);
    event.preventDefault();
    var form = $(this);
    var url = form.attr('action');
    var data = form.serialize();
    $.post(url, data).done(function (success) {
      var res = JSON.parse(success);
      if (res.status == true) {
        alert_float('success', res.message);
        //reload table
        $('#project-comment-modal').modal('hide');
        $('#project-comment-modal').find('textarea').val('');
        $('#project-comment-modal').find('button[type="submit"]').prop('disabled', false);
      } else {
        //save button make enabled
        $('#project-comment-form button[type="submit"]').prop('disabled', false);
        $('#project-comment-modal').find('textarea').val('');

        alert_float('danger', res.message);
      }
    });
  });


  $(document).on('change', '.project_launch_eta', function () {
    var project_id = $(this).data('project_id');
    var value = $(this).val();
    var custom_field_id = 51;
    project_change_custom_notes_field_value(project_id, custom_field_id, value);
  });

  $(document).on('click', '.edit-manual-timesheet-cancel', function () {
    $('.project_timesheet_add_edit').addClass('hide');
    //value empty
  });

  $(document).on('click', '.edit-manual-timesheet-submit', function () {
    var timer_id = $('input[name="timer_id"]').val();
    var project_id = $('input[name="time_project_id"]').val();
    var start_time = $('input[name="start_time"]').val();
    var pause_time = $('input[name="end_time"]').val();
    $.post(admin_url + 'task_customize/save_custome_project_timer', {
      timer_id: timer_id,
      project_id: project_id,
      start_time: start_time,
      pause_time: pause_time
    }, function (response) {
      var response = JSON.parse(response);
      if (response.success) {
        $('.project_timesheet_add_edit').addClass('hide');
        $.post(admin_url + 'task_customize/view_active_days', {
          project_id: project_id
        }, function (response) {
          var response = JSON.parse(response);
          if (response.status) {
            //empty before add
            $('#project_active_days').find('.project_overview_chart').html('');
            $('#project_active_days').find('#project_active_days_count').html('');

            $('#project_active_days').find('#project_id').val(project_id);
            $('#project_active_days').find('.project_overview_chart').html(response.table_data);
            $('#project_active_days').find('#project_active_days_count').html(response.day_count);
          }
        });
      } else {
        alert_float('danger', response.message);
      }
    });

  });

});


// view_active_days
function view_active_days(project_id) {


  //make ajax call and get data from database and append in table
  $.post(admin_url + 'task_customize/view_active_days', {
    project_id: project_id
  }, function (response) {
    var response = JSON.parse(response);
    if (response.status) {
      $('#project_active_days').modal('show');
      $('#project_active_days').find('#project_id').val(project_id);
      $('#project_active_days').find('.project_overview_chart').html(response.table_data);
      $('#project_active_days').find('#project_active_days_count').html(response.day_count);
    }
  });
}

//add_manual_timesheet
function add_manual_timesheet(project_id) {
  // project_timesheet_add_edit
  // project_id

  $("#start_time").val('');
  $("#end_time").val('');
  $('.project_timesheet_add_edit').removeClass('hide');
  $("input[name='time_project_id']").val(project_id);
}

function edit_custome_project_timer(timer_id, project_id) {
  // project_timesheet_add_edit
  // project_id

  $.post(admin_url + 'task_customize/get_custome_project_timer', {
    timer_id: timer_id
  }, function (response) {
    var response = JSON.parse(response);
    if (response.status) {

      $('.project_timesheet_add_edit').removeClass('hide');
      $("input[name='timer_id'").val(response.timer.id);
      $("input[name='time_project_id'").val(response.timer.project_id);
      $("input[name='start_time'").val(response.start_time);
      $("input[name='end_time'").val(response.pause_time);
    } else {
      alert('Timer not found');
    }
  });
}

function delete_custome_project_timer(timer_id, project_id) {
  $.post(admin_url + 'task_customize/delete_custome_project_timer', {
    timer_id: timer_id
  }, function (response) {
    var response = JSON.parse(response);
    if (response.status) {
      $('.project_timesheet_add_edit').addClass('hide');
      $.post(admin_url + 'task_customize/view_active_days', {
        project_id: project_id
      }, function (response) {
        var response = JSON.parse(response);
        if (response.status) {
          //empty before add
          $('#project_active_days').find('.project_overview_chart').html('');
          $('#project_active_days').find('#project_active_days_count').html('');

          $('#project_active_days').find('#project_id').val(project_id);
          $('#project_active_days').find('.project_overview_chart').html(response.table_data);
          $('#project_active_days').find('#project_active_days_count').html(response.day_count);
        }
      });
    } else {
      alert('Timer not found');
    }
  });
}

function project_change_custom_notes_field_value(project_id, custom_field_id, value) {
  url = admin_url + 'task_customize/project_change_custom_notes_field_value/' + project_id + '/' + custom_field_id;
  $("body").append('<div class="dt-loader"></div>');

  $.ajax({
    url: url,
    type: 'POST',
    data: {
      value: value
    },
    success: function (response) {
      var response = JSON.parse(response);
      if (response.success) {
        $("body").find(".dt-loader").remove();
        $('#projects').DataTable().ajax.reload();
        $('#project_status_note').modal('hide');
      } else {
        $("body").find(".dt-loader").remove();
        alert(response.message);
        $('#project_status_note').modal('hide');
      }
    }
  });
}

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
