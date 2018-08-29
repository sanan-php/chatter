'use strct';
$(function(){
    var chatWith = $('#chat-with').val();
    var currentUser = $('#current-user').val();
    var ws = 'ws://127.0.0.1:8000/?route=socket&action=worker&uid='+currentUser;
    var rand = $('#rand').val();
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
        url:'/?route=message&action=all&rand='+rand,
        type: 'post',
        data: {
            to: chatWith,
            endPosition: 500,
        },
        success: function (response) {
            if(response.content) {
                var messages = response.content;
                var i;
                for (i = 0; i < messages.length; i++) {
                    if(messages[i].id) {
                        if (messages[i].to === chatWith) {
                            message = '<div class="outbox">' + messages[i].text + '</div>';
                        } else if (messages[i].to === currentUser) {
                            message = '<div class="inbox">' + messages[i].text + '</div>';
                        }
                        $('#empty-dialogue').css('display', 'none');
                        $('#messages #new-loaded').append('<div id="message" class="id' + messages[i].id + '" title="'+messages[i].createdAt+'">' + message + '</div>');
                    } else {
                        emptyText = $('input.emptyText').val();
                        $('#messages #older').append('<div id="empty-dialogue">'+emptyText+'</div>');
                    }
                }
           }
        }
    });

    // write new message:
    $('#message-form').submit(function(e) {
        var $this = $(this);
        e.preventDefault();

        textMess = $('#new-message').val();
        textMessEncoded = $.base64Encode(textMess);
        console.log(chatWith+':'+textMess);
        $('#new-message').val('');
        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: {
                to: chatWith,
                message: textMessEncoded
            },
            success: function(response) {
                if(response.content) {
                    $('#empty-dialogue').css('dispay','none');
                    message = '<div class="outbox">'+textMess+'</div>';
                    $('#messages').append('<div id="message" class="id'+response.content.id+'">'+message+'</div>')
                }
            }
        });
    });

    // check new messages
    ws = new WebSocket(ws);
    ws.onmessage = function(evt) {
        $('#empty-dialogue').css('dispay','none');
        message = '<div class="inbox">'+evt.text+'</div>';
        $('#messages').append('<div id="message" class="id'+evt.id+'" title="'+evt.createdAt+'">'+message+'</div>');
    }

});