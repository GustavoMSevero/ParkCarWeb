app.controller("informationsCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

    $scope.typeUser = localStorage.getItem('parkcar_typeUser'); 
    if ($scope.typeUser == 'parking') {
        $scope.parking = $scope.typeUser;
        $scope.id = localStorage.getItem('parkcar_id');
        $scope.name = localStorage.getItem('parkcar_name');
        $scope.parking = localStorage.getItem('parkcar_typeUser');
        // console.log($scope.id +' '+ $scope.name +' '+ $scope.typeUser +' '+ $scope.parking);
    } else {
        $scope.admin = $scope.typeUser;
        $scope.id = localStorage.getItem('parkcar_id');
        $scope.name = localStorage.getItem('parkcar_name');
        $scope.typeUser =  $scope.admin = localStorage.getItem('parkcar_typeUser');
        // console.log($scope.id +' '+ $scope.name +' '+ $scope.typeUser +' '+ $scope.admin);
    }
    

	if(location.hostname == 'localhost'){
		var urlPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/admin.php';
		var urlOptionPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/admin.php?option=';
	} else {
		var urlPrefixAdmin = 'api/admin/admin.php';
		var urlOptionPrefixAdmin = 'api/admin/admin.php?option=';
	}

    $scope.logout = function() {
        // localStorage.clear();
        $location.path('/');
    }

    var getQuantityParkings = function() {
        var option = 'get quantity parkings';
        $http.get(urlOptionPrefixAdmin + option).then(function(response) {
            // console.log(response.data)
            $scope.parkingsQuantity = response.data.parkingsQuantity;
        });
    }
    getQuantityParkings();

    var getUsersQuantity = function() {
        var option = 'get users quantity';
        $http.get(urlOptionPrefixAdmin + option).then(function(response) {
            // console.log(response.data.parkingQuantity)
            $scope.parkingsQuantity = response.data.parkingQuantity;
            $scope.clientsQuantity = response.data.clientQuantity;
            $scope.vehiclesQuantity = response.data.vehicleQuantity;
        });
    }
    getUsersQuantity();
	
	
}]);