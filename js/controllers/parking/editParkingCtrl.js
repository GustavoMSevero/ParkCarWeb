app.controller("editParkingCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

    $scope.typeUser = localStorage.getItem('parkcar_typeUser'); 
    if ($scope.typeUser == 'parking') {
        $scope.parking = $scope.typeUser;
        $scope.id = localStorage.getItem('parkcar_id');
        $scope.name = localStorage.getItem('parkcar_name');
        console.log($scope.typeUser+' '+$scope.id+' '+$scope.name);
    } else {
        $scope.admin = $scope.typeUser;
        $scope.id = localStorage.getItem('parkcar_id');
        $scope.name = localStorage.getItem('parkcar_name');
        $scope.typeUser =  $scope.admin = localStorage.getItem('parkcar_typeUser');
        console.log($scope.id +' '+ $scope.name +' '+ $scope.typeUser +' '+ $scope.admin);
    }

    $scope.idParking = $routeParams.idParking;
    

	if(location.hostname == 'localhost'){
		var urlPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/editParking.php';
		var urlOptionPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/editParking.php?option=';
	} else {
		var urlPrefixAdmin = 'api/parking/editParking.php';
        var urlOptionPrefixAdmin = 'api/parking/editParking.php?option=';
	}

    $scope.logout = function() {
        localStorage.clear();
        $location.path('/');
    }

    var getParkingToUpdate = function() {
        var option = 'get parking to update';
        $http.get(urlOptionPrefixAdmin + option + '&idParking=' + $scope.idParking).success(function(response){
            // console.log(response);
            $scope.newParkingAddress = response;
        })
    }
    getParkingToUpdate();

    $scope.getNewAddress = function(newParkingAddress) {
        var zipcode = newParkingAddress.cep;
        $http.get('https://viacep.com.br/ws/'+zipcode+'/json/').success(function(response) {
            newParkingAddress.bairro = response.bairro;
            newParkingAddress.localidade = response.localidade;
            newParkingAddress.cep = response.cep;
            newParkingAddress.logradouro = response.logradouro;
            newParkingAddress.uf = response.uf;
        });
    }

    $scope.updateParking = function(newParkingAddress) {
        newParkingAddress.option = 'update parking';
        newParkingAddress.idParking = $scope.idParking;
        console.log(newParkingAddress);
        // $http.put(urlPrefixAdmin, newParkingAddress).success(function(data) {
        //     console.log(data);
        //     alert(data.msg);
        // })
    }
	
}]);