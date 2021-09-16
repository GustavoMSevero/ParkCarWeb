var app = angular.module("parkcar", ["ngRoute"]);

app.config(['$routeProvider', function($routeProvider){

	$routeProvider

	.when("/", {
        templateUrl: "views/login.html",
    })

    .when("/register", {
        templateUrl: "views/register.html",
    })

    .when("/editUserAdmin/:idUserAdmin", {
        templateUrl: "views/account/admin/editUserAdmin.html",
    })

    .when("/registerParking", {
        templateUrl: "views/account/parking/registerParking.html",
    })

    .when("/editParking/:idParking", {
        templateUrl: "views/account/parking/editParking.html",
    })

    .when("/editTimeAndPrices/:idParkingTimeAndPrices/:idSubparking", {
        templateUrl: "views/account/parking/editTimeAndPrices.html",
    })

    .when("/account", {
        templateUrl: "views/account/account.html",
    })

    .when("/financial", {
        templateUrl: "views/account/parking/financial.html",
    })

    .when("/historic", {
        templateUrl: "views/account/parking/historic.html",
    })

    .when("/address", {
        templateUrl: "views/account/parking/address.html",
    })

    .when("/vaccants", {
        templateUrl: "views/account/parking/vaccants.html",
    })

    .when("/timeAndPrices/:idParking", {
        templateUrl: "views/account/parking/timeAndPrices.html",
    })

    .when("/timeAndPricesMotorcycle/:idParking", {
        templateUrl: "views/account/parking/timeAndPricesMotorcycle.html",
    })

    .when("/parkingOwner", {
        templateUrl: "views/account/parking/parkingOwner.html",
    })

    .when("/ownerArea", {
        templateUrl: "views/account/parking/ownerArea.html",
    })

    .when("/financialAdmin", {
        templateUrl: "views/account/admin/financial.html",
    })

    .when("/information", {
        templateUrl: "views/account/admin/information.html",
    })

    .when("/historicAdmin", {
        templateUrl: "views/account/admin/historicAdmin.html",
    })

    .when("/registerUserAdmin", {
        templateUrl: "views/account/admin/registerUserAdmin.html",
    })

    .when("/checkParkings", {
        templateUrl: "views/account/admin/checkParkings.html",
    })

    .when("/checkClients", {
        templateUrl: "views/account/admin/checkClients.html",
    })

    .when("/checkVehicles", {
        templateUrl: "views/account/admin/checkVehicles.html",
    })



}]);
