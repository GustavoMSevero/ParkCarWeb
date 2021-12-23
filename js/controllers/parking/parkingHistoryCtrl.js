app.controller("parkingHistoryCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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
        // console.log($scope.id +' '+ $scope.name +' '+ $scope.typeUser +' '+ $scope.admin);
    }

    $scope.logout = function() {
        localStorage.clear();
        $location.path('/');
    }
    

	if(location.hostname == 'localhost'){
        var urlOptionParking = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/parking.php?option=';
	} else {
        var urlOptionParking = 'api/parking/parking.php?option=';
	}

    var getParkingHistoryByParking = function() {
        var option = 'get parking history by parking';
        $http.get(urlOptionParking + option + '&idParking=' + $scope.id).success(function(response) {
            // console.log(response)
            $scope.parkingHistory = response;
        })

    }
    getParkingHistoryByParking();
	
}]);