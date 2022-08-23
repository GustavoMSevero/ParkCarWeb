app.controller("loginCtrl", ['$scope', '$http', '$window', '$location', '$rootScope', function ($scope, $http, $window, $location, $rootScope) {

	if(location.hostname == 'localhost'){
		var urlPrefixAdmin = 'http://localhost:8892/Projects/Web/ParkCarWeb/api/admin/admin.php';
		var urlPrefixParking = 'http://localhost:8892/Projects/Web/ParkCarWeb/api/admin/adminParking.php';
	} else {
		var urlPrefixAdmin = 'api/admin/admin.php';
		var urlPrefixParking = 'api/admin/adminParking.php';
	}

	$scope.login = function(admin) {
		admin.option = 'login';
		if (admin.typeUser == null || admin.email == null || admin.password == null) {
			return;
		}
		// console.log(admin)
		if (admin.typeUser == 'parking') {
			try {
				$http.post(urlPrefixParking, admin).then(function(response) {
					// console.log(response)
					if (response.data.status == 0) {
						alert(response.data.msg);
					} else {
						localStorage.setItem('parkcar_id', response.data.id);
						localStorage.setItem('parkcar_name', response.data.name);
						localStorage.setItem('parkcar_typeUser', admin.typeUser);
						$location.path('/account');
					}
				});
			} catch (error) {
				console.log(error);
			}
			
		} else {
			// console.log('admin area')
			$http.post(urlPrefixAdmin, admin).then(function(response) {
				// console.log(response.data)
				if (response.data.status == 0) {
					alert(response.data.msg);
				} else {
					localStorage.setItem('parkcar_id', response.data.idUserAdmin);
					localStorage.setItem('parkcar_name', response.data.name);
					localStorage.setItem('parkcar_typeUser', admin.typeUser);
					$location.path('/account');
				}
			});
		}

	}
	
}]);