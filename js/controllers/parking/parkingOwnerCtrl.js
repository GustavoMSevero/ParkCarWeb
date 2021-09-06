app.controller("parkingOwnerCtrl", ['$scope', '$http', '$location', function ($scope, $http, $location) {

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
    

	if(location.hostname == 'localhost'){
		var urlPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/adminParking.php';
        var urlOptionPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/adminParking.php?option=';
	} else {
		var urlPrefixAdmin = 'api/admin/adminParking.php';
        var urlOptionPrefixAdmin = 'api/admin/adminParking.php?option=';
	}

	$scope.getAccess = function(superAdmin) {
        superAdmin.option = 'admin area';
        // console.log(superAdmin)
        $http.post(urlPrefixAdmin, superAdmin).success(function(response) {
            // console.log(response);
            if(response.status == 0) {
                alert(response.msg);
            } else {
                $location.path('/ownerArea');
            }
        })
    }
	
}]);