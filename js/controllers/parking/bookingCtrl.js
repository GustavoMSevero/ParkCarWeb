app.controller("bookingCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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
        var urlParking = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/parkingBooking.php';
        var urlOptionParking = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/parkingBooking.php?option=';
	} else {
        var urlParking = 'api/parking/parkingBooking.php';
        var urlOptionParking = 'api/parking/parkingBooking.php?option=';
	}

    $scope.register = function(allowBooking) {
        allowBooking.option = 'allow booking';
        allowBooking.idParking = $scope.id;
        $http.post(urlParking, allowBooking).success(function(data) {
            // console.log(data)
            getAllowBooking();
        })
    }

    var getAllowBooking = function() {
        var option = 'get allow booking';
        $http.get(urlOptionParking + option + '&idParking=' + $scope.id).success(function(response) {
            // console.log(response)
            $scope.allow = response.allow;
        })

    }
    getAllowBooking();
	
}]);