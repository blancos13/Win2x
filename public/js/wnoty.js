/**
 * notofy.js v0.1
 * https://qcode.site
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 */

!(function($, win, doc) {
    'use strict';
    var _doc = $(doc),
        notify = 'notify',
        error = function(e) {
            throw 'error: Cannot Notify => ' + e;
        },
        warn = function(l) {
            (console.warn == 'undefiend') ? console.log('Notify Warning: ' + l) : console.warn('Notify Warning: ' + l);
        },
        in_array = function(array, value) {
            for (var i = 0; i < array.length; i++) {
                if (array[i] === value) return true;
            }
            return false;
        },
        closeNotify = function(button) {
			var timer;
			clearTimeout(timer);
            button.parents('.' + notify + '__item').removeClass('show');
            setTimeout(function() {
                button.parents('.' + notify + '__item').addClass('hide');
            }, 25);
            timer = setTimeout(function() {
                button.parents('.' + notify).remove();
            }, 300);
        },
        initialize = function(set) {
            var noty = doc.createElement('div'),
                main = doc.createElement('div'),
                wrapper = doc.createElement('div'),
                aside = doc.createElement('aside'),
                img = doc.createElement('img'),
                title = doc.createElement('div'),
                span = doc.createElement('span'),
                text = doc.createElement('div'),
                close = doc.createElement('button');
            noty.className = '' + notify + '';
            main.className = '' + notify + '__item show';
            if(set.type == 'error') {
                var icon = 'data:image/svg+xml;base64, PHN2ZyB3aWR0aD0iMjciIGhlaWdodD0iMjciIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTE5LjY0IDUuNmwtLjA4LjA4TDYgMTkuMjRjLS4xNi4xNi0uMjcuMy0uMzUuNDRhMTAuMSAxMC4xIDAgMCAxLTIuMDctNi4yMiA5LjcgOS43IDAgMCAxIDkuODgtOS45MmMyLjM3IDAgNC41Ljc2IDYuMTggMi4wNnpNNy43IDIxLjY0Yy4xMi0uMDcuMjUtLjE4LjM5LS4zMkwyMS42NCA3Ljc2bC4wMi0uMDNhMTAuMDYgMTAuMDYgMCAwIDEgMS43NiA1Ljc3YzAgNS42My00LjI5IDkuOTYtOS45MiA5Ljk2YTkuOSA5LjkgMCAwIDEtNS44MS0xLjgyek0xIDEzLjVhMTIuNSAxMi41IDAgMSAwIDI1IDAgMTIuNSAxMi41IDAgMCAwLTI1IDB6IiBzdHJva2U9IiNmMTAyNjAiIGZpbGw9IiNmMTAyNjAiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==';
                var type = 'Error';
            } else if(set.type == 'success') {
                var icon = 'data:image/svg+xml;base64, PHN2ZyB3aWR0aD0iMjIiIGhlaWdodD0iMTciIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTS4zNiA3LjljLS41LjUzLS40NyAxLjU3LjAzIDIuMDhsNi4wMyA2LjEyYy41MS41MiAxLjUzLjUzIDIuMDUuMDRsLjk2LS45MiAyLjA4LTJMMjEuNiAzLjZjLjUyLS41LjU0LTEuNTEuMDMtMi4wMkwyMC40Ni4zOGMtLjUxLS41LTEuNTMtLjUtMi4wNC0uMDFsLTkuODcgOS44NmMtLjUuNS0xLjUyLjUtMi4wMi0uMDJMMy4yNCA2LjljLS40OS0uNTItMS40OC0uNS0xLjk3LjAzbC0uOTEuOTd6IiBmaWxsPSIjOTZDNzczIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiLz48L3N2Zz4=';
                var type = 'Successfully';
            } else if(set.type == 'warning') {
                var icon = 'data:image/svg+xml;base64, PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJzdmdfcmVzaXplIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ5Ny40NzIgNDk3LjQ3MiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDk3LjQ3MiA0OTcuNDcyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjI3IiBoZWlnaHQ9IjI3Ij4NCjxnIHRyYW5zZm9ybT0ibWF0cml4KDEuMjUgMCAwIC0xLjI1IDAgNDUpIj4NCgk8Zz4NCgkJPGc+DQoJCQk8cGF0aCBzdHlsZT0iZmlsbDojRkZDQzREOyIgZD0iTTI0LjM3NC0zNTcuODU3Yy0yMC45NTgsMC0zMC4xOTcsMTUuMjIzLTIwLjU0OCwzMy44MjZMMTgxLjQyMSwxNy45MjgNCgkJCQljOS42NDgsMTguNjAzLDI1LjQ2MywxOC42MDMsMzUuMTIzLDBMMzk0LjE0LTMyNC4wMzFjOS42NzEtMTguNjAzLDAuNDIxLTMzLjgyNi0yMC41NDgtMzMuODI2SDI0LjM3NHoiPjwvcGF0aD4NCgkJCTxwYXRoIHN0eWxlPSJmaWxsOiMyMzFGMjA7IiBkPSJNMTczLjYwNS04MC45MjJjMCwxNC44MTQsMTAuOTM0LDIzLjk4NCwyNS4zOTUsMjMuOTg0YzE0LjEyLDAsMjUuNDA3LTkuNTEyLDI1LjQwNy0yMy45ODQNCgkJCQlWLTIxNi43NWMwLTE0LjQ2MS0xMS4yODctMjMuOTg0LTI1LjQwNy0yMy45ODRjLTE0LjQ2MSwwLTI1LjM5NSw5LjE4Mi0yNS4zOTUsMjMuOTg0Vi04MC45MjJ6IE0xNzEuNDg5LTI4OS4wNTYNCgkJCQljMCwxNS4xNjcsMTIuMzQ1LDI3LjUxMSwyNy41MTEsMjcuNTExYzE1LjE2NywwLDI3LjUyMy0xMi4zNDUsMjcuNTIzLTI3LjUxMWMwLTE1LjE3OC0xMi4zNTYtMjcuNTIzLTI3LjUyMy0yNy41MjMNCgkJCQlDMTgzLjgzNC0zMTYuNTc5LDE3MS40ODktMzA0LjIzNCwxNzEuNDg5LTI4OS4wNTYiPjwvcGF0aD4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg==';
                var type = 'Attention';
            } else if(set.type == 'info') {
                var icon = 'data:image/svg+xml;base64, PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDMzMCAzMzAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDMzMCAzMzA7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMjdweCIgaGVpZ2h0PSIyN3B4IiBjbGFzcz0iIj48Zz48Zz4KCTxwYXRoIGQ9Ik0xNjUsMEM3NC4wMTksMCwwLDc0LjAyLDAsMTY1LjAwMUMwLDI1NS45ODIsNzQuMDE5LDMzMCwxNjUsMzMwczE2NS03NC4wMTgsMTY1LTE2NC45OTlDMzMwLDc0LjAyLDI1NS45ODEsMCwxNjUsMHogICAgTTE2NSwzMDBjLTc0LjQ0LDAtMTM1LTYwLjU2LTEzNS0xMzQuOTk5QzMwLDkwLjU2Miw5MC41NiwzMCwxNjUsMzBzMTM1LDYwLjU2MiwxMzUsMTM1LjAwMUMzMDAsMjM5LjQ0LDIzOS40MzksMzAwLDE2NSwzMDB6IiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIGRhdGEtb2xkX2NvbG9yPSIjMDA4OUZGIiBmaWxsPSIjMDA5MUZGIi8+Cgk8cGF0aCBkPSJNMTY0Ljk5OCw3MGMtMTEuMDI2LDAtMTkuOTk2LDguOTc2LTE5Ljk5NiwyMC4wMDljMCwxMS4wMjMsOC45NywxOS45OTEsMTkuOTk2LDE5Ljk5MSAgIGMxMS4wMjYsMCwxOS45OTYtOC45NjgsMTkuOTk2LTE5Ljk5MUMxODQuOTk0LDc4Ljk3NiwxNzYuMDI0LDcwLDE2NC45OTgsNzB6IiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIGRhdGEtb2xkX2NvbG9yPSIjMDA4OUZGIiBmaWxsPSIjMDA5MUZGIi8+Cgk8cGF0aCBkPSJNMTY1LDE0MGMtOC4yODQsMC0xNSw2LjcxNi0xNSwxNXY5MGMwLDguMjg0LDYuNzE2LDE1LDE1LDE1YzguMjg0LDAsMTUtNi43MTYsMTUtMTV2LTkwQzE4MCwxNDYuNzE2LDE3My4yODQsMTQwLDE2NSwxNDB6ICAgIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIGRhdGEtb2xkX2NvbG9yPSIjMDA4OUZGIiBmaWxsPSIjMDA5MUZGIi8+CjwvZz48L2c+IDwvc3ZnPgo=';
                var type = 'Information';
            };
            wrapper.className = notify + '__item-wrap';
            aside.className = notify + '__aside';
            title.className = notify + '__title';
            text.className = notify + '__message';
            close.className = notify + '__close';
			img.setAttribute('src', icon);
            doc.body.appendChild(noty);
            noty.appendChild(main);
            main.appendChild(wrapper);
            aside.appendChild(img);
            title.appendChild(span);
            span.innerText = type;
            close.innerText = '×';
            wrapper.appendChild(aside);
            wrapper.appendChild(title);
            wrapper.appendChild(text);
            wrapper.appendChild(close);
            text.innerHTML = set.message;
            if(set.autohide == true) {
                setTimeout(function() {
                    closeNotify($(close));
                }, set.autohideDelay)
            }
        };
    $.notify = function(options) {
        var types = ['error', 'success', 'warning', 'info'],
            settings = {
                message: '',
                type: '',
                autohide: true,
                autohideDelay: 3000,
            };
        $.extend(settings, options);
        if(settings.type == '' && !settings.type.length) error('Type is not defined!');
        if(!in_array(types, settings.type)) error('Uhh, invalid notify type!');
        if(settings.message == '' && !settings.message.length) error('Hmmm, Message seems to be empty or not defined!');
        if($('.' + notify).length) {
            closeNotify($('.' + notify).find('.' + notify + '__close'));
        }
        initialize(settings);
    };
    _doc.on('click', '.notify__close', function() {
        closeNotify($(this));
    });
    console.log('Notify by WOLK! %c vegadev.ru ', 'background:#7266ba;color:#fff');
})(window.jQuery, window, document)