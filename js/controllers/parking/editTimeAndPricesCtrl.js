app.controller("editTimeAndPricesCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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

    $scope.parkingName = $scope.name;
    $scope.idParkingTimeAndPrices = $routeParams.idParkingTimeAndPrices;
    $scope.idSubparking = $routeParams.idSubparking;

	if(location.hostname == 'localhost'){
		var urlPrefixParkingTimeAndPrices = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/parkingTimeAndPrices.php';
		var urlOptionPrefixParkingTimeAndPrices = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/parkingTimeAndPrices.php?option=';
	} else {
		var urlPrefixAdmin = 'api/parking/adminParking.php';
		var urlOptionPrefixAdmin = 'api/parking/adminParking.php?option=';
	}

    var getTimeAndPricesToEdit = function() {
        var option = 'get time and prices to edit';
        var idParking = $scope.id;
        var idSubparking = $scope.idSubparking;

        $http.get(urlOptionPrefixParkingTimeAndPrices + option + '&idParking=' + idParking + '&idSubparking=' + idSubparking).success(function(response) {
            // console.log(response)
            $scope.newParkingTimeAndPrices = response;
        })
    }
    getTimeAndPricesToEdit();

    $scope.updateTimeAndPrice = function(newParkingTimeAndPrices) {
        // console.log(newParkingTimeAndPrices)
        newParkingTimeAndPrices.idParkingTimeAndPrices = $scope.idParkingTimeAndPrices;
        newParkingTimeAndPrices.option = 'update time and prices';
        // var daysOfWeek = ["", "Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"];
        // newParkingTimeAndPrices.dayOfWeek = daysOfWeek[newParkingTimeAndPrices.dayOfWeek];
        // console.log(newParkingTimeAndPrices)
        $http.put(urlPrefixParkingTimeAndPrices, newParkingTimeAndPrices).success(function(response) {
            // console.log(response)
            if (response.status == 1) {
                alert(response.msg)
            } else {
                alert(response.msg)
            }
        })
    }

}]);