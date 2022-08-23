app.controller("customerCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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
        var urlModality = 'http://localhost:8892/Projects/web/ParkCarWeb/api/parking/modality.php';
        var urlOptionModality = 'http://localhost:8892/Projects/Web/ParkCarWeb/api/parking/modality.php?option=';
        var urlCustomer = 'http://localhost:8892/Projects/web/ParkCarWeb/api/parking/customer.php';
        var urlOptionCustomer = 'http://localhost:8892/Projects/Web/ParkCarWeb/api/parking/customer.php?option=';
	} else {
        var urlModality = 'api/parking/modality.php';
        var urlOptionModality = 'api/parking/modality.php?option=';
        var urlCustomer = 'api/parking/customer.php';
        var urlOptionCustomer = 'api/parking/customer.php?option=';
	}

    $scope.registerModality = function(modality) {
        modality.option = "register modality";
        modality.idParking = $scope.id;
        $http.post(urlModality, modality).success(function(data) {
            getModalities();
        })
    }

    var getModalities = function() {
        var option = "get modalities";
        $http.get(urlOptionModality + option + '&idParking=' + $scope.id).success(function(response) {
            $scope.parkingModalities = response;
        })
    }
    getModalities();


    $scope.registerCustomer = function(customer) {
        customer.option = "register customer";
        customer.idParking = $scope.id;
        $http.post(urlCustomer, customer).success(function(data) {
            getParkingCutomers();
        })
    }

    var getParkingCutomers = function() {
        var option = "get parking customers"
        $http.get(urlOptionCustomer + option + '&idParking=' + $scope.id).success(function(response) {
            $scope.parkingCustomers = response;
        })
    }
    getParkingCutomers();
	
}]);