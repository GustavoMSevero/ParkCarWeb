app.controller("editUserAdminCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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
    
    $scope.idUserAdmin = $routeParams.idUserAdmin;

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

    // console.log('idUserAdmin '+$scope.idUserAdmin)

    var getUserAdminForEdit = function() {
        var option = 'get user admin for edit';
        $http.get(urlOptionPrefixAdmin + option + '&idUserAdmin=' + $scope.idUserAdmin).success(function(response) {
            // console.log(response)
            $scope.newUser = response;
        });
    }
    getUserAdminForEdit();

    $scope.updateUser = function(newUser) {
        newUser.option = 'update user admin';
        newUser.idUserAdmin = $scope.idUserAdmin;
        // console.log(newUser)
        $http.post(urlPrefixAdmin, newUser).success(function(data) {
            $location.path('registerUserAdmin');
        });
    }
	
	
}]);