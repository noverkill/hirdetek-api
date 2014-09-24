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
  }).state('editHirdetes', { //state for updating a movie
    url: '/hirdetes/:id/edit',
    templateUrl: 'partials/hirdetes-edit.html',
    controller: 'HirdetesEditController'
  }).state('newHirdetes', { //state for adding a new movie
    url: '/hirdetes/new',
    templateUrl: 'partials/hirdetes-add.html',
    controller: 'HirdetesCreateController'
  });

}).run(function($state) {

  $state.go('hirdetesek'); //make a transition to movies state when app starts

});

hirdetekApp.service( 'HirdetesService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8080/hirdetes/:id', { id: '@id'}, {
      'query': { 
        method: 'GET', 
        isArray: false 
      },    
      'update': {
        method: 'PUT'
      }
    }
  );
}]);

hirdetekApp.service('popupService',function($window){
    this.showPopup=function(message){
        return $window.confirm(message);
    }
});

hirdetekApp.controller('HirdetesListCtrl', [ '$scope', 'HirdetesService', function ($scope, HirdetesService) {

	$scope.maxSize = 5;
	$scope.itemsPerPage = 25;
	$scope.currentPage = 1;
	
  $scope.setPage = function (pageNo) {
    $scope.currentPage = pageNo;
  };

  $scope.pageChanged = function() {
  	HirdetesService.query({page: $scope.currentPage}, function(response) { 	
  		$scope.hirdetesek = response._embedded.hirdetes;
  		$scope.totalItems = response.total_items; 	
  	});    
  };

  $scope.pageChanged();

}]);

hirdetekApp.controller('HirdetesViewController', function($scope, $stateParams, HirdetesService) {

	$scope.hirdetes = HirdetesService.get({ id: $stateParams.id }); 

});

hirdetekApp.controller('HirdetesEditController', function($scope, $state, $stateParams, HirdetesService, popupService) {
  
  $scope.updateHirdetes = function() { //Update the edited movie. Issues a PUT to /api/movies/:id
    $scope.hirdetes.$update(function() {
      $state.go('hirdetesek'); // on success go back to home i.e. movies state.
    });
  };

  $scope.deleteHirdetes = function() { // Delete a movie. Issues a DELETE to /api/movies/:id
    if (popupService.showPopup('Really delete this?')) {
      $scope.hirdetes.$delete(function() {
        $state.go('hirdetesek'); // on success go back to home i.e. movies state.
      });
    }
  };

  $scope.loadHirdetes = function() { //Issues a GET request to /api/movies/:id to get a movie to update
    $scope.hirdetes = HirdetesService.get({ id: $stateParams.id });
  };
 
  $scope.loadHirdetes(); // Load a movie which can be edited on UI
});


hirdetekApp.controller('HirdetesCreateController', function($scope, $state, $stateParams, HirdetesService) {
  
  $scope.hirdetes = new HirdetesService();  //create new movie instance. Properties will be set via ng-model on UI
 
  $scope.addHirdetes = function() { //create a new movie. Issues a POST to /api/movies
    $scope.hirdetes.$save(function() {
      $state.go('hirdetesek'); // on success go back to home i.e. movies state.
    });
  };

});