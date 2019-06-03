var Bucket = 'hk-1251279962';
var Region = 'ap-hongkong';

var cos = new COS({
    FileParallelLimit: 5,
    ChunkParallelLimit: 5,
    ChunkMbSize: 8 * 1024 * 1024,
    getAuthorization: function (options, callback) {
        var url = '/index/cos_key/info';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function (e) {
            try {
                var data = JSON.parse(e.target.responseText);
            } catch (e) {
            }
            callback({
                TmpSecretId: data.credentials && data.credentials.tmpSecretId,
                TmpSecretKey: data.credentials && data.credentials.tmpSecretKey,
                XCosSecurityToken: data.credentials && data.credentials.sessionToken,
                ExpiredTime: data.expiredTime
            });
        };
        xhr.send();
    }
});

new Vue({
    el: '#app',
    data: function () {
        return {
            FileParallelLimit: 5,
            ChunkParallelLimit: 16,
            ChunkMbSize: 2,
            list: [],
            total: 0,
        };
    },
    created: function () {
        var self = this;
        cos.on('list-update', function (data) {
            self.list = data.list;
            self.total = data.list.length;
        });
    },
    methods: {
        formatSize: function (size) {
            var i, unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
            for (i = 0; i < unit.length && size >= 1024; i++) {
                size /= 1024;
            }
            return (Math.round(size * 100) / 100 || 0) + unit[i];
        },
        selectedFile: function (e) {
            var files = e.target.files;
            var list = [].map.call(files, function (f) {
                var myDate = new Date();
                var year = myDate.getFullYear();
                var month = myDate.getMonth()+1;
                var day = myDate.getDate();
                var time = myDate.getTime();
                var filename = Math.ceil(Math.random()*10000) + time;
                return {
                    Bucket: Bucket,
                    Region: Region,
                    Key: 'files/' + year + '/' + month + '/' + day + '/' + filename + f.name,
                    Body: f,
                };
            });
            cos.uploadFiles({files: list});
            document.getElementById('form').reset();
        },
        pauseTask: function (task) {
            cos.pauseTask(task.id);
        },
        restartTask: function (task) {
            cos.restartTask(task.id);
        },
        cancelTask: function (task) {
            cos.cancelTask(task.id);
        },
    },
});
