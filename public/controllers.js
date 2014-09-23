var hirdetekApp = angular.module('hirdetekApp', ['ngResource', 'ui.bootstrap', 'ui.router']);
 
hirdetekApp.config(function($stateProvider) {
  $stateProvider.state('hirdetesek', { // state for showing all movies
    url: '/',
    templateUrl: 'partials/hirdetesek.html',
    controller: 'HirdetesListCtrl'
  }).state('viewHirdetes', { //state for showing single movie
    url: '/hirdetes/:id/view',
    templateUrl: 'partials/hirdetes-view.html',
    controller: 'HirdetesViewController'    
  });
}).run(function($state) {
  $state.go('hirdetesek'); //make a transition to movies state when app starts
});

hirdetekApp.service( 'HirdetesService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8080/hirdetes/:id', { id: '@id'},{'query': { method: 'GET', isArray: false }} );
}]);

hirdetekApp.controller('HirdetesListCtrl', [ '$scope', 'HirdetesService', function ($scope, HirdetesService) {

	$scope.maxSize = 5;
	$scope.itemsPerPage = 25;
	$scope.currentPage = 1;
	$scope.bigCurrentPage = 1; 
	
	HirdetesService.query({page: $scope.currentPage}, function(response) {
		console.log(response);  	
		$scope.hirdetesek = response;
		$scope.totalItems = response.total_items; 	
		$scope.bigTotalItems = response.total_items; 	
	});

  $scope.setPage = function (pageNo) {
  	console.log("pageNo: " + pageNo);
    $scope.currentPage = pageNo;
  };

  $scope.pageChanged = function() {
    console.log('Page changed to: ' + $scope.currentPage);

	HirdetesService.query({page: $scope.currentPage}, function(response) {
		console.log(response);  	
		$scope.hirdetesek = response;
		$scope.totalItems = response.total_items; 	
		$scope.bigTotalItems = response.total_items; 	
	});    
  };

}]);

hirdetekApp.controller('HirdetesViewController', function($scope, $stateParams, HirdetesService) {
	$scope.hirdetes = HirdetesService.get({ id: $stateParams.id }); 
});