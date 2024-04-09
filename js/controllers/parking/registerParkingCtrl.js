app.controller("registerParkingCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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
		var urlPrefixAdmin = 'http://localhost:8888/web/ParkCarWeb/api/parking/parking.php';
        var urlOptionPrefixAdmin = 'http://localhost:8888/web/ParkCarWeb/api/parking/parking.php?option=';
	} else {
		var urlPrefixAdmin = 'api/parking/parking.php';
        var urlOptionPrefixAdmin = 'api/parking/parking.php?option=';
	}

	$scope.getAddress = function(parkingAddress) {
        var zipcode = parkingAddress.zipcode;
        $http.get('https://viacep.com.br/ws/'+zipcode+'/json/').success(function(response) {
            // console.log(response);
            parkingAddress.neighborhood = response.bairro;
            parkingAddress.city = response.localidade;
            parkingAddress.zipcode = response.cep;
            parkingAddress.address = response.logradouro;
            parkingAddress.state = response.uf;
        });
    }

    $scope.parkingAddress = {};
    $scope.register = function(parkingAddress) {
        parkingAddress.option = 'register';
        parkingAddress.idOwnerParking = $scope.id;
        // console.log(parkingAddress)
        $http.post(urlPrefixAdmin, parkingAddress).success(function(data) {
            // console.log(data)
            $scope.parkingAddress = '';
            getMyParkings();
            alert(data.msgRegisterOK);
        })
    }

    var getMyParkings = function() {
        var option = 'get my parkings';
        $http.get(urlOptionPrefixAdmin + option + '&idOwnerParking=' + $scope.id).success(function(response) {
            $scope.listMyParkings = response;
        });
    }
    getMyParkings();
	
}]);