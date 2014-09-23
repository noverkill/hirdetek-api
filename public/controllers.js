var hirdetekApp = angular.module('hirdetekApp', ['ngResource', 'ui.bootstrap']);

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