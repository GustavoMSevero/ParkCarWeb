app.controller("registerCtrl", ['$scope', '$http', '$window', '$location', '$rootScope', function ($scope, $http, $window, $location, $rootScope) {

	if(location.hostname == 'localhost'){
		var urlPrefixAdminParking = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/adminParking.php';
	} else {
		var urlPrefixAdminParking = 'api/admin/adminParking.php';
	}

    $scope.register = function(parking) {
        parking.option = 'register owner parking';
        $http.post(urlPrefixAdminParking, parking).success(function(data) {
            if (data.status === 1) {
                localStorage.setItem('parkcar_id', data.id);
                localStorage.setItem('parkcar_name', data.name);
                localStorage.setItem('parkcar_typeUser', 'parking');
                localStorage.setItem('parkcar_moreParkings', data.moreParkings);
                $location.path('/account');
            } else {
                alert(data.existsMessagem)
                return;
            }
        });
    }
	
}]);