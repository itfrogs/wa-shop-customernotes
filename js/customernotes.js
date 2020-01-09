/**
 * Created by snark on 8/7/15.
 */

(function ($) {
    $.Customernotes = {
        localization: null,
        waAppUrl: null,
        waUrl: null,
        contact_id: null,
        confirm_message: null,
        comment_message: null,
        init: function () {
            jQuery.ajaxSetup({ cache:false });

            $('.s-toggle-status').iButton( { labelOn : "", labelOff : "", className: 'mini' } ).change(function() {
                var self = $(this);
                var enabled = self.is(':checked');
                if (enabled) {
                    enabled = 1;
                    $( "#customernotesContent" ).show(200);
                } else {
                    enabled = 0;
                    $( "#customernotesContent" ).hide(200);
                }
                $.post('?plugin=customernotes&action=shownotes',
                    {
                        enabled : enabled
                    }
                    , function (d) {
                        if (d.status == 'ok') {
                        }
                    }, 'json');
            });
        },
        rateObject: {
            rate : function(e) {
                    var thisObj = $(e);
                    var thisType = thisObj.hasClass('rateUp') ? 'up' : 'down';
                    var thisOrder = thisObj.attr('data-order_id');

                $.post('?plugin=customernotes&action=rate',
                    {
                        type : thisType,
                        order_id : thisOrder
                    }
                    , function (d) {
                    if (d.status == 'ok') {
                        $('#customernotes-form').html(d.data.form_template);
                        $('#customernotes-notes').html(d.data.notes_template);
                        $('#customernotes-rating').html(d.data.rating_template);
                        thisObj.addClass('active');
                    }
                }, 'json');
            }
        },
        deleteNote: function(e) {
            if (confirm(this.confirm_message)) {
                var li = $(e).closest('li');
                var order_id = li.find('a').data('order_id');
                $.post('?plugin=customernotes&action=deletenote', { order_id : order_id }, function (d) {
                    if (d.status == 'ok') {
                        $('#customernotes-form').html(d.data.form_template);
                        $('#customernotes-rating').html(d.data.rating_template);
                        li.remove();
                    }
                }, 'json');
            }
        },
        apiSendNote: function(order_id) {
            if (confirm(this.comment_message)) {
                $('#customernotes-update-comment-' + order_id).removeClass('update').addClass('loading');
                $.post('?plugin=customernotes&action=sendcomment', { order_id : order_id }, function (d) {
                    if (d.status == 'ok') {
                        $('#customernotes-update-comment-' + order_id).removeClass('loading').addClass('update');
                    }
                }, 'json');
            }
        },
        apiGetNotes: function(order_id) {
            $.post('?plugin=customernotes&action=sendcomment', { order_id : order_id }, function (d) {
                if (d.status == 'ok') {
                    $('#customernotes-update-comment-' + order_id).removeClass('loading').addClass('update');

                    $.post('?plugin=customernotes&action=getcomments', { order_id : order_id }, function (d) {
                        if (d.status == 'ok') {
                            $('#customernotes-bstats').html(d.data.notes_template);
                        }
                    }, 'json');
                }
            }, 'json');


        },
        contactcheck: function() {
            if ($.Customernotes.contact_id) {
                $.post('?plugin=customernotes&action=contactcheck', { contact_id : $.Customernotes.contact_id }, function (d) {
                    if (d.status == 'ok') {
                        $('#customernotes-bstats-customer').html(d.data.customer);
                    }
                }, 'json');
            }
        },
        choose: function(contact_id, customer) {
            $.post('?plugin=customernotes&action=choose',
                {
                    contact_id :    contact_id,
                    customer:       customer
                },
                function (d) {
                if (d.status == 'ok') {
                    $('#customernotes-bstats-customer').html(d.data.customer);
                }
            }, 'json');
        },
        addNote: function (order_id) {
            var form = $('#customernotesAddNote').serialize();
            $.post('?plugin=customernotes&action=addnote', form, function (d) {
                if (d.status == 'ok') {
                    $('#customernotes-notes').html(d.data.notes_template);

                    $(saveButton).addClass('green');
                }
            }, 'json');
        }
    }
})(jQuery);

