app.controller("financialParkingCtrl", ['$scope', '$http', '$location', function ($scope, $http, $location) {

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
		var urlPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/financialParking.php';
		var urlOptionPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/financialParking.php?option=';
	} else {
		var urlPrefixAdmin = 'api/parking/financialParking.php';
        var urlOptionPrefixAdmin = 'api/parking/financialParking.php?option=';
	}

    $scope.logout = function() {
        localStorage.clear();
        $location.path('/');
    }


    $scope.getTotalValue = function(month) {
        month.option = 'get total value';
        month.idParking = $scope.id;
        month.month = parseInt(month.month, 10)
        
        $http.post(urlPrefixAdmin, month ).success(function(data) {
            $scope.totalValuePaid = data.totalValuePaid;
            monthIndex = ['nada', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto',
                        'setembro', 'outubro', 'novembro', 'dezembro',];
            $scope.moth = monthIndex[parseInt(month.month, 10)];
        });
    }


}]);