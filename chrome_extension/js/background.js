var host = "http://scude.hteen.cn";

chrome.alarms.create('course_time_listen', {
    when: Date.now(),
    periodInMinutes: 1
});

chrome.alarms.onAlarm.addListener(function(alarm){

    var cookieArray = new Array();
    chrome.cookies.getAll({}, function(cookies) {
        cookies.map(function(cookie) {
            if (cookie.path == '/' && cookie.domain.indexOf('scude.cc') > 0) {
                cookieArray.push(cookie);
            }
        });

        chrome.tabs.query({
            status: "complete",
            url: "http://resource.scude.cc/*",
        }, function (result){
            console.log(result);

            if (result.length > 0) {
                $.getJSON(host + '/minutes', {cookies: cookieArray, urls: result}, function(json) {

                });
            }

        });

    });

});
