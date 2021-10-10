Admin.controller('commandControlDashboard', function ($scope, $http, $timeout, $interval) {
    $scope.selectCommand = {"command": "", "title": ""};
    $scope.setCommand = function (title, command) {

        $scope.selectCommand.command = command;
        $scope.selectCommand.title = title;
    }

    $scope.applist = {"list": [], "notifications":[]};

    $scope.appnotification = function () {
        $http.get(rootBaseUrl + "Api/getAppsList/" + device_id).then(function (result) {
            $scope.applist.list = result.data;
        }, function () {});
        $http.get(rootBaseUrl + "Api/recentNotifications/" + device_id).then(function (result) {
            $scope.applist.notifications = result.data;
        }, function () {});
    }
    $scope.appnotification();
    $interval(function () {
        $scope.appnotification();
    }, 5000)



})