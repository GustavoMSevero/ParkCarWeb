app.controller("editCustomerCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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
        var urlOptionContract = 'http://localhost:8888/web/ParkCarWeb/api/parking/contract.php?option=';
        var urlCustomer = 'http://localhost:8888/web/ParkCarWeb/api/parking/customer.php';
        var urlOptionCustomer = 'http://localhost:8888/web/ParkCarWeb/api/parking/customer.php?option=';
	} else {
        var urlOptionContract = 'api/parking/contract.php?option=';
        var urlCustomer = 'api/parking/customer.php';
        var urlOptionCustomer = 'api/parking/customer.php?option=';
	}

    $scope.idParkingCustomer = $routeParams.idParkingCustomer;

    var getContracts = function() {
        var option = "get contracts";
        $http.get(urlOptionContract + option + '&idParking=' + $scope.id).success(function(response) {
            // console.log(response)
            $scope.customerContracts = response;
        })
    }
    getContracts();

    var getParkingCustomer = function() {
        var option = "get parking customer to edit"
        $http.get(urlOptionCustomer + option + '&idParking=' + $scope.id + '&idParkingCustomer=' + $scope.idParkingCustomer).success(function(response) {
            // console.log(response)
            $scope.newCustomer = response;
        })
    }
    getParkingCustomer();

    $scope.updateCustomer = function(newCustomer) {
        newCustomer.option = "update customer";
        newCustomer.idParking = $scope.id;
        newCustomer.idParkingCustomer = $scope.idParkingCustomer;
        $http.post(urlCustomer, newCustomer).success(function(data) {
            // console.log(data)
            $location.path('/customers');
        })

    }
	
}]);