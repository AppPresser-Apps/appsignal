jQuery(document).ready(function($) {
    $('#send-test-message').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $messageField = $('#onesignal_message');
        var message = $messageField.val().trim();
        
        if (!message) {
            alert('Please enter a message to send');
            return;
        }
        
        // Disable button and show loading state
        $button.prop('disabled', true).text('Sending...');
        
        // Send AJAX request
        $.ajax({
            url: appsignal.ajax_url,
            type: 'POST',
            data: {
                action: 'appsignal_send_test_message',
                message: message,
                nonce: appsignal.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Message sent successfully!');
                    $messageField.val('');
                } else {
                    alert('Error: ' + (response.data || 'Failed to send message'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + (xhr.responseJSON && xhr.responseJSON.data ? xhr.responseJSON.data : 'Failed to send message'));
            },
            complete: function() {
                // Re-enable button
                $button.prop('disabled', false).text('Send Test Message');
            }
        });
    });
});
