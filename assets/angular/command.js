Admin.controller('commandControlDashboard', function ($scope, $http, $timeout, $interval) {
    $scope.selectCommand = {"command": "", "title": ""};
    $scope.setCommand = function (title, command) {

        $scope.selectCommand.command = command;
        $scope.selectCommand.title = title;
    }

})