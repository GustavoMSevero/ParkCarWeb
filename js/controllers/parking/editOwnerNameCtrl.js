app.controller("editOwnerNameCtrl", ['$scope', '$http', '$location', function ($scope, $http, $location) {

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
    

	if(location.hostname == 'localhost'){
		var urlPrefixAdmin = 'http://localhost:8892/Projects/Web/ParkCarWeb/api/admin/adminParking.php';
        var urlOptionPrefixAdmin = 'http://localhost:8892/Projects/Web/ParkCarWeb/api/admin/adminParking.php?option=';
	} else {
		var urlPrefixAdmin = 'api/admin/adminParking.php';
        var urlOptionPrefixAdmin = 'api/admin/adminParking.php?option=';
	}

    $scope.logout = function() {
        localStorage.clear();
        $location.path('/');
    }

    var getOwnerName = function() {
        var option = 'get owner name';
        $http.get(urlOptionPrefixAdmin + option + '&id=' + $scope.id).success(function(response) {
            // console.log(response)
            // $scope.newOwner = response.name;
        })
    }
    getOwnerName();

    $scope.updateOwnerName = function(owner) {
        owner.option = 'update owner name';
        owner.id = $scope.id;

        $http.post(urlPrefixAdmin, owner).success(function(data) {
            // console.log(data.name)
            $scope.name = data.name;
            localStorage.setItem('parkcar_name', $scope.name);
            $location.path('/ownerArea');
        })
    }

}]);