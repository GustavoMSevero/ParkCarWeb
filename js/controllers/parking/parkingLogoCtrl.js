app.controller("parkingLogoCtrl", ['$scope', '$http', '$location', '$routeParams', function ($scope, $http, $location, $routeParams) {

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

    $scope.logout = function() {
        localStorage.clear();
        $location.path('/');
    }
    

	if(location.hostname == 'localhost'){
        var urlUploadLogoParking = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/uploadLogoParking.php?idParking=';
        var urlGetLogoParking = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/getLogoParking.php';
	} else {
        var urlUploadLogoParking = 'api/parking/uploadLogoParking.php?idParking=';
        var urlGetLogoParking = 'api/parking/getLogoParking.php?idParking=';
	}



    var formData = new FormData();
        var idParking = $scope.id;

        $scope.uploadLogo = function(){
        $scope.input.click();
        }

        $scope.input = document.createElement("INPUT");
        $scope.input.setAttribute("type", "file");
        $scope.input.addEventListener('change', function(){
        formData.append('file_jpg', $scope.input.files[0]);
            $.ajax({
            url: urlUploadLogoParking + idParking,
            data: formData,
            type: 'POST',
            contentType: false,
            processData: false
            })
            .then(function(response) {
                // console.log(response);
                getParkingLogo();

            }, function(error) {
                console.log(JSON.stringify(error));
            });
        

        });

    var getParkingLogo = function() {;
        $http.get(urlGetLogoParking + idParking).success(function(data) {
            // console.log(data)
            $scope.image = data.local;
        })
    }
    getParkingLogo();
	
	
}]);