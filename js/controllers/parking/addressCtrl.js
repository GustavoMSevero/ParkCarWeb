app.controller("addressCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

    $scope.typeUser = localStorage.getItem('parkcar_typeUser'); 
    if ($scope.typeUser == 'parking') {
        $scope.parking = $scope.typeUser;
        $scope.id = localStorage.getItem('parkcar_id');
        $scope.name = localStorage.getItem('parkcar_name');
        $scope.parking = localStorage.getItem('parkcar_typeUser');
        console.log($scope.id +' '+ $scope.name +' '+ $scope.typeUser +' '+ $scope.parking);
    } else {
        $scope.admin = $scope.typeUser;
        $scope.id = localStorage.getItem('parkcar_id');
        $scope.name = localStorage.getItem('parkcar_name');
        $scope.typeUser =  $scope.admin = localStorage.getItem('parkcar_typeUser');
        console.log($scope.id +' '+ $scope.name +' '+ $scope.typeUser +' '+ $scope.admin);
    }
    

	if(location.hostname == 'localhost'){
		var urlPrefixParkingAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/adminParking.php';
		var urlOptionPrefixParkingAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/adminParking.php?option=';
	} else {
		var urlPrefixParkingAdmin = 'api/admin/adminParking.php';
	}

    var getParkingAddress = function() {
        var option = 'get parking address';
        $http.get(urlOptionPrefixParkingAdmin + option + '&idParking=' + $scope.id).then(function(response) {
            $scope.parkingAddress = response.data;
        });
    }
    getParkingAddress();
	
	
}]);