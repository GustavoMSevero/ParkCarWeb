app.controller("editAgreementCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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
		var urlPrefixAgreement = 'http://localhost:8888/web/ParkCarWeb/api/agreement/agreement.php';
		var urlOptionPrefixAgreement = 'http://localhost:8888/web/ParkCarWeb/api/agreement/agreement.php?option=';
	} else {
		var urlPrefixAgreement = 'api/agreements/agreement.php';
        var urlOptionPrefixAgreement = 'api/agreements/agreement.php?option=';
	}

    $scope.idAgreement = $routeParams.idAgreement;

    var getAgreement = function() {
        var option = 'get agreement to edit';
        var idParking = $scope.id;
        $http.get(urlOptionPrefixAgreement + option + '&idParking=' + idParking + '&idAgreement=' + $scope.idAgreement).success(function(response) {
            $scope.newAgreement = response;
        })

    }
    getAgreement();

    $scope.updateAgreement = function(newAgreement) {
        newAgreement.option = 'update agreement';
        newAgreement.idParking = $scope.id;
        newAgreement.idAgreement = $scope.idAgreement;

        $http.post(urlPrefixAgreement, newAgreement).success(function(data) {
            $location.path('/agreements')
        })
    }
	
}]);