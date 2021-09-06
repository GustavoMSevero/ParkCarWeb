app.controller("checkParkingsCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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
    

	if(location.hostname == 'localhost'){
		var urlPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin.php';
		var urlOptionPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin.php?option=';
	} else {
		var urlPrefixAdmin = 'api/admin.php';
		var urlOptionPrefixAdmin = 'api/admin.php?option=';
	}

    $scope.logout = function() {
        // localStorage.clear();
        $location.path('/');
    }

    var getParkings = function() {
        var option = 'get parkings';
        $http.get(urlOptionPrefixAdmin + option).then(function(response) {
            // console.log(response.data)
            $scope.listOfParkings = response.data;
        });

    }
    getParkings();
	
}]);