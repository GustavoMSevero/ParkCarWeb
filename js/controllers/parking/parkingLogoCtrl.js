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
        var urlOptionUploadLogoParking = 'http://localhost:8888/Projects/Web/ParkCarWeb/api/parking/uploadLogoParking.php?option=';
	} else {
        var urlOptionUploadLogoParking = 'api/parking/parking.php?option=';
	}



    var formData = new FormData();
	var idParking = $scope.id;
    var option = 'upload logo';

	$scope.uploadLogo = function(){
	  $scope.input.click();
	}

	$scope.arquivo = '';

	$scope.input = document.createElement("INPUT");
	$scope.input.setAttribute("type", "file");
	$scope.input.addEventListener('change', function(){
	  formData.append('file_jpg', $scope.input.files[0]);

	    $.ajax({
	      url: urlOptionUploadLogoParking + option + '&idParking=' + idParking,
	      data: formData,
	      type: 'POST',
	      contentType: false,
	      processData: false
	    })
	      .then(function(response) {
	        console.log(response);
            getParkingLogo();

	  }, function(response) {
	      console.log("Error "+JSON.stringify(response));
	  });
      

    });

    var getParkingLogo = function() {
        var option = 'get parking logo';
        $http.get(urlOptionUploadLogoParking + option + '&idParking=' + idParking).success(function(data) {
            $scope.image = data.local;
        })
    }
    getParkingLogo();
	
	
}]);