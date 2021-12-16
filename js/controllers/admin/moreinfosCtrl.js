app.controller("moreinfosCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

    $scope.typeUser = localStorage.getItem('parkcar_typeUser'); 
    if ($scope.typeUser == 'parking') {
        $scope.parking = $scope.typeUser;
        $scope.id = localStorage.getItem('parkcar_id');
        $scope.name = localStorage.getItem('parkcar_name');
        $scope.parking = localStorage.getItem('parkcar_typeUser');
        // console.log($scope.id +' '+ $scope.name +' '+ $scope.typeUser +' '+ $scope.parking);
    } else {
        $scope.admin = $scope.typeUser;
        $scope.id = localStorage.getItem('parkcar_id');
        $scope.name = localStorage.getItem('parkcar_name');
        $scope.typeUser =  $scope.admin = localStorage.getItem('parkcar_typeUser');
        // console.log($scope.id +' '+ $scope.name +' '+ $scope.typeUser +' '+ $scope.admin);
    }
    
    $scope.idParking = $routeParams.idParking;
    // console.log(`idParking ${$scope.idParking}`)

	if(location.hostname == 'localhost'){
		var urlPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/admin.php';
		var urlOptionPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/admin.php?option=';
	} else {
		var urlPrefixAdmin = 'api/admin/admin.php';
		var urlOptionPrefixAdmin = 'api/admin/admin.php?option=';
	}

    $scope.logout = function() {
        // localStorage.clear();
        $location.path('/');
    }

    var getDataParkingById = function() {
        var option = 'get data parking by id';
        $http.get(urlOptionPrefixAdmin + option + '&idParking=' + $scope.idParking).success(function(data) {
            $scope.parkingData = data;
        })
    }
    getDataParkingById();
    
    $scope.saveFee = function(fee) {
        fee.option = 'save fee';
        fee.idParking = $scope.idParking;
        $http.post(urlPrefixAdmin, fee).success(function(data) {
            getFeeParking();
        })
    }

    var getFeeParking = function() {
        var option = 'get fee parking';
        $http.get(urlOptionPrefixAdmin + option + '&idParking=' + $scope.idParking).success(function(data) {
            $scope.fees = data;
        })
    }
    getFeeParking();
	
}]);