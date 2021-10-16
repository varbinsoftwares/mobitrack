Admin.controller('commandControlDashboard', function ($scope, $http, $timeout, $interval) {
    $scope.selectCommand = {"command": "", "title": ""};
    $scope.setCommand = function (title, command) {

        $scope.selectCommand.command = command;
        $scope.selectCommand.title = title;
    }

    $scope.applist = {"list": [], "notifications": [], "location": {}, "recentfiles": [], "soundfiles": []};

    $scope.appnotification = function () {
        $http.get(rootBaseUrl + "Api/getAppsList/" + device_id).then(function (result) {
            $scope.applist.list = result.data;
        }, function () {});
        $http.get(rootBaseUrl + "Api/recentNotifications/" + device_id).then(function (result) {
            $scope.applist.notifications = result.data;
        }, function () {});

        $http.get(rootBaseUrl + "Api/recentFiles/" + device_id + "/gallary").then(function (result) {
            $scope.applist.recentfiles = result.data;
        }, function () {});

        $http.get(rootBaseUrl + "Api/recentFiles/" + device_id + "/sound_record").then(function (result) {
            $scope.applist.soundfiles = result.data;
        }, function () {});


        $http.get(rootBaseUrl + "Api/recentLocation/" + device_id).then(function (result) {
            var locationdata = result.data;

            if (locationdata.latitude != $scope.applist.location.latitude) {
                var locationsrc = "https://maps.google.com/maps?q=" + locationdata.latitude + "," + locationdata.longitude + "&z=15&output=embed";
                var iframdata = '<iframe src="' + locationsrc + '" width="100%" height="270" frameborder="0" style="border:0"></iframe>';
                $("#locationframdata").html(iframdata);
            }
            $scope.applist.location = locationdata;
        }, function () {});
    }

    $scope.appnotification();
    $interval(function () {
//        $scope.appnotification();
    }, 5000)


    $scope.getFileDownload = function (index, mtype) {
        if (mtype == 'sound_record') {
             $scope.applist.soundfiles[index].status = "download";
            $http.get(rootBaseUrl + "Api/downlaodFile/" + $scope.applist.soundfiles[index].id).then(function (result) {
                $scope.appnotification();
            }, function () {});
        } else {
            $scope.applist.recentfiles[index].status = "download";
            $http.get(rootBaseUrl + "Api/downlaodFile/" + $scope.applist.recentfiles[index].id).then(function (result) {
                $scope.appnotification();
            }, function () {});
        }

    }


})