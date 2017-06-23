// Copyright (c) 2014 The Chromium Authors. All rights reserved.
// Use of this source code is governed by a BSD-style license that can be
// found in the LICENSE file.


var host = "http://scude.hteen.cn";

function getSceduCookies() {
    var cookieArray = new Array();

    chrome.cookies.getAll({}, function(cookies) {

        cookies.map(function(cookie) {
            if (cookie.path == '/' && cookie.domain.indexOf('scude.cc') > 0) {
                cookieArray.push(cookie);
            }
        });

        $('.loading').show();

        $.getJSON(host + '/login', {
            cookies: cookieArray
        }, function(json) {

            if (json.code) {
                $('.error').html(json.msg);
            } else {

                var tr = "";

                json.data.map(function(v) {

                    tr += '<tr>' +
                        '    <td>' + v.student_id + '</td>' +
                        '    <td>' + v.course_name + '</td>' +
                        '    <td>' + v.counter_name + '</td>' +
                        '    <td>' + v.counter_num + '</td>' +
                        '    <td>' + v.counter_minutes + '分钟</td>' +
                        '    <td width="10%">' +
                        '        <a href="javascript:;" class="test" data-student="' + v.student_id + '" ' +
                        'data-id="' + v.counter_id + '" data-url="' + v.counter_url + '">前往上课</a>' +
                        // '        <a>后台刷<input />次, <button>确定</button></a>'+
                        '    </td>' +
                        '</tr>';
                });

                $("table tbody").append(tr);

                $("tbody").on('click', '.test', function(event) {
                    var url = $(this).data('url');
                    var counter_id = $(this).data('id');
                    var student_id = $(this).data('student');

                    $.getJSON(host + '/incr', {
                        student_id: student_id,
                        counter_id: counter_id
                    }, function(res) {

                        if (res.code) {
                            $('.error').html(json.msg);
                        } else {
                            chrome.tabs.create({
                                url: url
                            });
                        }
                    });


                });

                $(".main").show();
            }

            $('.loading').hide();

        });

    });
}

document.addEventListener('DOMContentLoaded', function() {
    getSceduCookies();
});
