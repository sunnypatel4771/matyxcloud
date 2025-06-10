function edit_client_note(id) {
    
    var editor = tinymce.get($('[data-note-edit-textarea="' + id + '"] textarea').attr('id'));

    if (!editor) {
      console.log("TinyMCE editor not found for ID:", id);
      return;
    }
  
    var description = editor.getContent();

    if (description !== "") {
        $.post(admin_url + "client_notes/edit_note/" + id, {
            description: description,
        }).done(function (response) {
            response = JSON.parse(response);
            if (response.success === true || response.success == "true") {
                alert_float("success", response.message);
                $("body")
                    .find('[data-note-description="' + id + '"]')
                    .html(description);
            }
        });
        
        toggle_edit_note(id);
    }
}