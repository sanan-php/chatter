$(function(){
    setInterval(function () {$('#error-container').animate({
        visibility : 'hidden',
        top: '-300px'
    },800)},1800);
    var wrapper = $( ".file_upload" ),
        inp = wrapper.find( "input" ),
        btn = wrapper.find( "button" ),
        lbl = wrapper.find( "div" );

    btn.focus(function(){
        inp.focus()
    });
    // Crutches for the :focus style:
    inp.focus(function(){
        wrapper.addClass( "focus" );
    }).blur(function(){
        wrapper.removeClass( "focus" );
    });

    var file_api = ( window.File && window.FileReader && window.FileList && window.Blob ) ? true : false;

    inp.change(function(){
        var file_name;
        if( file_api && inp[ 0 ].files[ 0 ] )
            file_name = inp[ 0 ].files[ 0 ].name;
        else
            file_name = inp.val().replace( "C:\\fakepath\\", '' );

        if( ! file_name.length )
            return;

        if( lbl.is( ":visible" ) ){
            lbl.text( file_name );
            btn.text( "Выбрать" );
        }else
            btn.text( file_name );
    }).change();

    $('#favorite').click(function() {
        favoriteId = $('#favoriteId').val();
        action  = $('#favorite #action').val();
        if(action==='create') {
            newAction = 'delete';
        } else {
            newAction = 'create';
        }
        $.ajax({
            url: '/?route=favorite&action='+action,
            type: 'post',
            data: {
                'favoriteId': favoriteId
            },
            success: function(response) {
                if(response.success) {
                    $('#favorite').removeAttr('class').attr('class',newAction);
                    $('#favorite #action').val(newAction);
                }
            }
        });
    });
});
$( window ).resize(function(){
    $( ".file_upload input" ).triggerHandler( "change" );
});
