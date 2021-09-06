app.controller("timeAndPricesMotorcycleCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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

    $scope.idParkingBranch = $routeParams.idParking;

	if(location.hostname == 'localhost'){
		var urlOptionPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/adminParking.php?option=';
		var urlPrefixParkingTimeAndPrices = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/parkingTimeAndPrices.php';
		var urlOptionPrefixParkingTimeAndPrices = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/parkingTimeAndPrices.php?option=';
	} else {
		var urlOptionPrefixAdmin = 'api/admin/adminParking.php?option=';
        var urlPrefixParkingTimeAndPrices = 'api/parking/parkingTimeAndPrices.php';
		var urlOptionPrefixParkingTimeAndPrices = 'api/parking/parkingTimeAndPrices.php?option=';
	}

    var getParkingBranch = function() {
        var option = 'get parking branch';
        $http.get(urlOptionPrefixAdmin + option + '&idParking=' + $scope.idParkingBranch).success(function(response) {
            // console.log(response)
            $scope.parkingName = response.parkingName;
        })
    }
    getParkingBranch();

    // $scope.parkingTimeAndPrices = {};
    // $scope.registerTimeAndPrice = function(parkingTimeAndPrices) {
    //     var weekDay = parseInt(parkingTimeAndPrices.dayOfWeek);
    //     var daysOfWeek = ["", "Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"];
    //     parkingTimeAndPrices.dayOfWeek = daysOfWeek[weekDay];

    //     parkingTimeAndPrices.option = 'register time and prices',
    //     parkingTimeAndPrices.idParking = $scope.id,
    //     parkingTimeAndPrices.idParkingBranch = $scope.idParkingBranch
    //     $http.post(urlPrefixParkingTimeAndPrices, parkingTimeAndPrices).success(function(response) {
    //         // console.log(response);
    //         $scope.parkingTimeAndPrices = '';
    //         getParkingTimeAndPrices();
    //     })
    // }

    // var getParkingTimeAndPrices = function() {
    //     var option = 'get parking time and prices';
    //     $http.get(urlOptionPrefixParkingTimeAndPrices + option + '&id_parking=' + $scope.id + '&idParkingBranch='+ $scope.idParkingBranch).success(function(response) {
    //         // console.log(response);
    //         $scope.timeAndPrices = response;
    //     })

    // }
    // getParkingTimeAndPrices();

}]);