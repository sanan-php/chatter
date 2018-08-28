$(function(){
    var chatWith = $('#chat-with').val();
    var currentUser = $('#current-user').val();
    w=$('#write-form').width();
    $('#new-message').width(w-20);
    messages = $('#messages');
    messages.scrollTop(messages.height());

    $('#new-message').on('keydown', function(e) {
        if (e.ctrlKey && e.keyCode === 13) {
            $('#comment_submit').trigger('submit');
        }
    });
    $.ajax({
        url:'/?route=message&action=all',
        type: 'post',
        data: {
            to: chatWith,
            limit: 500
        },

        success: function (response) {
            if(response.content) {
                var messages = response.content;
                var i;
                for (i = 0; i < messages.length; i++) {
                    if(messages[i].to == chatWith) {
                        message = '<div class="outbox">' + messages[i].text + '</div>';
                    } else if(messages[i].to == currentUser) {
                        message = '<div class="inbox">' + messages[i].text + '</div>';
                    }
                    $('#empty-dialogue').css('display','none');
                    $('#messages').append('<div id="message" class="id' + messages[i].id + '">' + message + '</div>')
                }
            }
        }
    });

    emptyText = $('input.emptyText').val();
    $('#messages').append('<div id="empty-dialogue">'+emptyText+'</div>');

    // write new message:
    $('#message-form').submit(function(e) {
        var $this = $(this);
        e.preventDefault();

        textMess = $('#new-message').val();
        textMess = $.base64Encode(textMess);
        console.log(chatWith+':'+textMess);
        $('#chat-with').html('');
        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: {
                to: chatWith,
                message: textMess
            },
            success: function(response) {
                if(response.content) {
                    $('#empty-dialogue').css('dispay','none');
                    message = '<div class="outbox">'+response.content.message+'</div>';
                    $('#messages').append('<div id="message" class="id'+response.content.id+'">'+message+'</div>')
                }
            }
        });
    });
});