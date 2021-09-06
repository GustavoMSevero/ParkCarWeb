app.controller("ownerAreaCtrl", ['$scope', '$http', '$location', function ($scope, $http, $location) {

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
		var urlPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/adminParking.php';
        var urlOptionPrefixAdmin = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/admin/adminParking.php?option=';
	} else {
		var urlPrefixAdmin = 'api/admin/adminParking.php';
        var urlOptionPrefixAdmin = 'api/admin/adminParking.php?option=';
	}

	var getOwnerData = function() {
        var option = 'get owner data';
        $http.get(urlOptionPrefixAdmin + option + '&id=' +$scope.id).success(function(response) {
            // console.log(response)
            $scope.name = response.name;
            $scope.email = response.ownerEmail;
        })
    }
    getOwnerData();

    $scope.admin = {};
    $scope.changePassword = function(admin) {
        admin.option = 'change admin password';
        admin.id = $scope.id;
        if (admin.newPassword == null) {
            alert('Senha não informada');
        } else {
            $http.put(urlPrefixAdmin, admin).success(function(response) {
                $scope.admin = '';
                // console.log(response)
                if (response.status == 1) {
                    alert(response.msg);
                }
            })
        }
    }
}]);