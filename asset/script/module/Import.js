/*
 * The MIT License (MIT)
 * Copyright (c) 2020 GameplayJDK
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

'use strict';

Module('App.Import', (function () {
    // Set variables for the scope:
    var button = $('button#import');
    var modal = $('div#modalImport');

    function setButtonState(active) {
        button.attr('disabled', !active);
    }

    function showModal(text) {
        modal.find('code#modalImportContent')
            .text(text);
        modal.modal('show');
    }

    function handleClick(event) {
        setButtonState(false);

        var url = button.data('url');

        if (!!url) {
            $.getJSON(url, function (data) {
                var result = !!data.result;

                setButtonState(true);
                showModal(result.toString());
            });
        } else {
            setButtonState(true);
        }
    }

    function registerEvent() {
        button.on('click', handleClick);
    }

    // Do everything for initialization:
    function initialize() {
        registerEvent();
    }

    return {
        initialize: initialize,
    };
}));
