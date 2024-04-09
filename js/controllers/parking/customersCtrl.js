app.controller("customersCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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
        var urlContract = 'http://localhost:8888/web/ParkCarWeb/api/parking/contract.php';
        var urlOptionContract = 'http://localhost:8888/web/ParkCarWeb/api/parking/contract.php?option=';
        var urlCustomer = 'http://localhost:8888/web/ParkCarWeb/api/parking/customer.php';
        var urlOptionCustomer = 'http://localhost:8888/web/ParkCarWeb/api/parking/customer.php?option=';
	} else {
        var urlContract = 'api/parking/contract.php';
        var urlOptionContract = 'api/parking/contract.php?option=';
        var urlCustomer = 'api/parking/customer.php';
        var urlOptionCustomer = 'api/parking/customer.php?option=';
	}

    $scope.contract = {};
    $scope.registerContract = function(contract) {
        contract.option = "register contract";
        contract.idParking = $scope.id;
        $http.post(urlContract, contract).success(function(data) {
            // console.log(data);
            $scope.contract = {};
            getContracts();
        })
    }

    var getContracts = function() {
        var option = "get contracts";
        $http.get(urlOptionContract + option + '&idParking=' + $scope.id).success(function(response) {
            // console.log(response)
            $scope.customerContracts = response;
        })
    }
    getContracts();


    $scope.customer = {};
    $scope.registerCustomer = function(customer) {
        customer.option = "register customer";
        customer.idParking = $scope.id;
        $http.post(urlCustomer, customer).success(function(data) {
            $scope.customer = {};
            getParkingCutomers();
        })
    }

    var getParkingCutomers = function() {
        var option = "get parking customers"
        $http.get(urlOptionCustomer + option + '&idParking=' + $scope.id).success(function(response) {
            // console.log(response)
            $scope.parkingCustomers = response;
        })
    }
    getParkingCutomers();
	
}]);