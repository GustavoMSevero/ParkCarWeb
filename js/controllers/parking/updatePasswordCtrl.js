app.controller("updatePasswordCtrl", ['$scope', '$http', '$location', function ($scope, $http, $location) {

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

    $scope.logout = function() {
        localStorage.clear();
        $location.path('/');
    }
    

	if(location.hostname == 'localhost'){
		var urlPrefixAdmin = 'http://localhost:8888/web/ParkCarWeb/api/parking/editParking.php';
        var urlOptionPrefixAdmin = 'http://localhost:8888/web/ParkCarWeb/api/parking/editParking.php?option=';
	} else {
		var urlPrefixAdmin = 'api/parking/editParking.php';
        var urlOptionPrefixAdmin = 'api/parking/editParking.php?option=';
	}

	var getEmailFromParkingLotToChange = function() {
        var option = 'get email from parking lot to change';
        $http.get(urlOptionPrefixAdmin + option + '&idParking=' + $scope.id).success(function(response) {
            // console.log(response)
            $scope.newDataToApp = response;
        })

    }
    getEmailFromParkingLotToChange();

    $scope.updateDataToApp = function(newDataToApp) {
        newDataToApp.option = 'update email and password';
        newDataToApp.idParking = $scope.id;
        // console.log(newDataToApp)
        $http.post(urlPrefixAdmin, newDataToApp).success(function(data) {
            // console.log(data)
            alert(data.msg)
        })
    }
	
}]);