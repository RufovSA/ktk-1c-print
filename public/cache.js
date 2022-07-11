$.ajax({
    xhr: function() {
        var xhr = new window.XMLHttpRequest();

        xhr.addEventListener("progress", function(evt) {
            if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                document.getElementById('un_progress').innerHTML = 'Обновление данных на сайте: ' + percentComplete + '%';
            }
        }, false);

        return xhr;
    },
    type: 'GET',
    url: "/site/priem/table.html?noCache=1",
    data: {},
    success: function(data){
        document.getElementById('un_progress').style.display = 'none';
        document.getElementById('un_info').style.display = 'block';
    }
});