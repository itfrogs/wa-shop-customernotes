/**
 * Created by snark on 9/28/15.
 */

(function ($) {
    $.Customernotes_settings = {
        tables: null,
        field: null,
        init: function () {
            var self = this;
            self.initTabs();


            var url = new URL(window.location);
            var code = self.getUrlValue('code');
            if (code) {
                $.post(
                    '?plugin=customernotes&action=login', {
                        code: code
                    }, function (d) {
                        if (d.status === 'ok') {
                            //$('#customernotes-login').html(d.data.login_template);
                            location.href = self.removeURLParameter(location.href, 'code') + location.hash;
                        }
                    }, 'json'
                );
            }
        },
        initTabs: function () {
            var self = this, tabs = $("#customernotes-tabs").children(), cnt = $("#customernotes-tabs-content").children();
            cnt.hide().first().show();
            tabs.click(
                function (e) {
                    if (!$(this).hasClass('selected')) {
                        $(this).addClass("selected").siblings().removeClass("selected");
                        var tab = $("a", this).attr("href");
                        $(tab).fadeToggle().siblings().hide();
                    }
                    return false;
                }
            );
        },
        logout: function () {
            $.post(
                '?plugin=customernotes&action=login', {
                    'logout': 1
                }, function (d) {
                    if (d.status === 'ok') {
                        $('#customernotes-login').html(d.data.login_template);
                    }
                }, 'json'
            );
        },
        getUrlValue: function (key) {
            var matches = location.href.match(new RegExp(key+'=([^&]*)'));
            if (matches) {
                var split = matches[1].split('#');
                if (split) {
                    return split[0];
                }
                else return null;
            }
            else return null;
        },
        removeURLParameter: function(url, parameter) {
        //prefer to use l.search if you have a location/link object
        var urlparts= url.split('?');
        if (urlparts.length>=2) {

            var prefix= encodeURIComponent(parameter)+'=';
            var pars= urlparts[1].split(/[&;]/g);

            //reverse iteration as may be destructive
            for (var i= pars.length; i-- > 0;) {
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    pars.splice(i, 1);
                }
            }

            return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
        }
        return url;
    }
    }
})(jQuery);




