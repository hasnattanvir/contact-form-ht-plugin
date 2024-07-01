jQuery(document).ready(function($) {
    $('#contact-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'submit_contact_form');
        formData.append('nonce', contact_form_vars.nonce);

        $.ajax({
            url: contact_form_vars.ajax_url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('#cf-response').html('<p>' + response.data + '</p>');
                    $('#contact-form')[0].reset();
                } else {
                    $('#cf-response').html('<p>' + response.data + '</p>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#cf-response').html('<p>There was an error: ' + errorThrown + '</p>');
                console.log('Error:', textStatus, errorThrown);
            }
        });
    });
});
