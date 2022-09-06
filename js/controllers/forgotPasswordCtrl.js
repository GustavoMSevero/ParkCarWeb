app.controller("forgotPasswordCtrl", ['$scope', '$http', '$window', '$location', '$rootScope', function ($scope, $http, $window, $location, $rootScope) {

	if(location.hostname == 'localhost'){
		var urlPrefixAdmin = 'http://localhost:8892/Projects/Web/ParkCarWeb/api/admin/forgotPassword.php';
	} else {
		var urlPrefixAdmin = 'api/admin/forgotPassword.php';
	}

	$scope.forgotPassword = function(user){
        user.option = 'forgot password';
        // console.log(user)
        $http.post(urlPrefixAdmin, user).success(function(response) {
            console.log(response)
            if (response.status == 0) {
                $scope.msgErro = response.msg;
            } else {
                $scope.msg = response.msg;
            }
        })
    }
	
}]);