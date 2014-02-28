var noteId = 0, noteTimeout = false;

$(document).ready(function() {
    var tmp;

    function getNotes() {
        jQuery.ajax({
            type: 'POST',
            url: stickyPath + '/ajax.php',
            data: {
                'action': 'get',
                'noteId': noteId
            },
            success: function(msg) {
                try {
                    o = JSON.parse(msg);
                } catch (e) {

                }
                for (i in o) {
                    if (typeof o[i] == 'object') {
                        if (o[i].id > noteId)
                            noteId = o[i].id;
                        var xyz = o[i].xyz.split('x');
                        if (xyz[0] == 0)
                            xyz[0] = (Math.random() * (jQuery('body').width() - 172));
                        if (xyz[1] == 0)
                            xyz[1] = (Math.random() * (jQuery('body').height() - 172));

                        e = '<div id="note_' + o[i].id + '" class="note ' + o[i].color + '" style="left:' + xyz[0] + 'px;top:' + xyz[1] + 'px; z-index:' + (o[i].id + 500) + '">';
                        e += '<img style=\'cursor: pointer; position: relative;float: right; top: -8px; right: -8px;\' src=\'' + stickyPath + '/images/close.gif\' onClick=\'closeNote(this);\'/>';
                        e += o[i].text;
                        if (o[i].user && o[i].user != '') {
                            e += '<div class="author">' + o[i].user + '</div>';
                        }
                        e += '<span class="data">' + o[i].date + '</span>';
                        e += '</div>';
                        jQuery('body').append(e);
                    }
                }
                jQuery(document).ready(function() {
                    $('.note').each(function() {
                        tmp = $(this).css('z-index');
                        if (tmp > zIndex)
                            zIndex = tmp;
                    })

                    make_draggable($('.note'));

                });
            }
        });
        noteTimeout = setTimeout(function() {
            getNotes();
        }, 10 * 1000);
    }


    //getNotes();


    jQuery('#addNoteButton').click(function() {
        jQuery('#addNote').dialog({
            resizable: false,
            width: 575,
            height: 390,
            buttons: {
                "Add": function() {
                    if ($('.pr-body').val().length < 4) {
                        alert("The note text is too short!")
                        return false;
                    }

                    if ($('.pr-author').val().length < 1) {
                        alert("You haven't entered your name!")
                        return false;
                    }

                    jQuery.ajax({
                        type: 'POST',
                        url: stickyPath + '/ajax.php',
                        data: {
                            'action': 'add',
                            'zindex': ++zIndex,
                            'body': $('.pr-body').val(),
                            'author': $('.pr-author').val(),
                            'color': $.trim($('#addNote .note').attr('class').replace('note', '').replace('ui-draggable', ''))
                        },
                        success: function(msg) {
                            if (parseInt(msg)) {
                                clearTimeout(noteTimeout);
                                getNotes();
                            }
                            $('#addNote').dialog("close");
                        }
                    });
                },
                Cancel: function() {
                    $(this).dialog("close");
                }
            }
        });
        return false;
    });

    $('.pr-body,.pr-author').live('keyup', function(e) {
        if (!this.preview)
            this.preview = $('#addNote .note');
        this.preview.find($(this).attr('class').replace('pr-', '.')).html($(this).val().replace(/<[^>]+>/ig, ''));
    });

    $('.color').live('click', function() {
        $('#addNote .note').removeClass('yellow green blue').addClass($(this).attr('class').replace('color', ''));
    });

    $('.note-form').live('submit', function(e) {
        e.preventDefault();
    });

    jQuery('#noteMenu').mouseleave(function() {
        jQuery('#noteMenu').hide();
    });
    

    jQuery(document).ready(function(){
        jQuery.ajax({
            type: 'POST',
            url: stickyPath + '/ajax.php',
            data: {
                'action': 'init'
            },
            success: function(msg) {
                jQuery('body').append(msg);
                getNotes();
            }
        });
    });    
});

var zIndex = 0;

function make_draggable(elements) {
    elements.draggable({
        containment: 'parent',
        start: function(e, ui) {
            ui.helper.css('z-index', ++zIndex);
        },
        stop: function(e, ui) {
            $.get(stickyPath + '/ajax.php', {
                'action': 'position',
                x: ui.position.left,
                y: ui.position.top,
                z: zIndex,
                id: parseInt(jQuery(ui.helper[0]).attr('id').replace('note_', ''))
            });
        }
    });
}

function closeNote(o) {
    jQuery(o).parent().remove();
    var note = jQuery(o).parent().attr('id');
    jQuery.ajax({
        type: 'POST',
        url: stickyPath + '/ajax.php',
        data: {
            'action': 'hide',
            'id': note.replace('note_', '')
        },
        success: function(msg) {
            jQuery('#' + note).remove();
        }
    });
    /*
    jQuery('#noteMenu').hide();
    var parentOffset = jQuery(o).offset();
    jQuery('#noteMenu').attr('note', id).css('top', parentOffset.top - 54).css('left', parentOffset.left).show(); */
}

function closeNoteConfirm(o) {
    jQuery('#noteMenu').hide();
    var note = jQuery(o).parent().parent().parent().attr('note');
    jQuery.ajax({
        type: 'POST',
        url: stickyPath + '/ajax.php',
        data: {
            'action': 'hide',
            'id': note.replace('note_', '')
        },
        success: function(msg) {
            jQuery('#' + note).remove();
        }
    });
}
