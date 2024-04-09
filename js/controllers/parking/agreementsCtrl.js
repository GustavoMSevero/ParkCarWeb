app.controller("agreementsCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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

    $scope.agreement = {};
    $scope.registerAgreement = function(agreement) {
        agreement.option = 'save agreement';
        agreement.idParking =  $scope.id;
        $http.post(urlPrefixAgreement, agreement).success(function(data) {
            getAgreements();
            $scope.agreement = {};
        })
            
    }

    var getAgreements = function() {
        var option = 'get agreements';
        var idParking = $scope.id;
        $http.get(urlOptionPrefixAgreement + option + '&idParking=' + idParking).success(function(response) {
            $scope.agreements = response;
        })

    }
    getAgreements();
	
}]);