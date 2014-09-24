var hirdetekUserApp = angular.module('hirdetekUserApp', ['ngResource', 'ui.bootstrap', 'ui.router']);
 
hirdetekUserApp.config(function($stateProvider) {
  
  $stateProvider.state('users', { // state for showing all movies
    url: '/',
    templateUrl: 'partials/users.html',
    controller: 'UserListCtrl'
  }).state('viewUser', { //state for showing single movie
    url: '/user/:id/view',
    templateUrl: 'partials/user-view.html',
    controller: 'UserViewController'    
  }).state('editUser', { //state for updating a movie
    url: '/user/:id/edit',
    templateUrl: 'partials/user-edit.html',
    controller: 'UserEditController'
  }).state('newUser', { //state for adding a new movie
    url: '/user/new',
    templateUrl: 'partials/user-add.html',
    controller: 'UserCreateController'
  });

}).run(function($state) {

  $state.go('users'); //make a transition to movies state when app starts

});

hirdetekUserApp.service( 'UserService', [ '$resource', function( $resource ) {
  return $resource( 'http://localhost:8080/user/:id', { id: '@id'}, {
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

hirdetekUserApp.service('popupService',function($window){
    this.showPopup=function(message){
        return $window.confirm(message);
    }
});

hirdetekUserApp.controller('UserListCtrl', [ '$scope', 'UserService', function ($scope, UserService) {

	$scope.maxSize = 5;
	$scope.itemsPerPage = 25;
	$scope.currentPage = 1;
	
  $scope.setPage = function (pageNo) {
    $scope.currentPage = pageNo;
  };

  $scope.pageChanged = function() {
  	UserService.query({page: $scope.currentPage}, function(response) { 	
  		$scope.users = response._embedded.users;
  		$scope.totalItems = response.total_items; 	
  	});    
  };

  $scope.pageChanged();

}]);

hirdetekUserApp.controller('UserViewController', function($scope, $stateParams, UserService) {

	$scope.user = UserService.get({ id: $stateParams.id }); 

});

hirdetekUserApp.controller('UserEditController', function($scope, $state, $stateParams, UserService, popupService) {
  
  $scope.updateUser = function() { //Update the edited movie. Issues a PUT to /api/movies/:id
    $scope.user.$update(function() {
      $state.go('users'); // on success go back to home i.e. movies state.
    });
  };

  $scope.deleteUser = function() { // Delete a movie. Issues a DELETE to /api/movies/:id
    if (popupService.showPopup('Really delete this?')) {
      $scope.user.$delete(function() {
        $state.go('users'); // on success go back to home i.e. movies state.
      });
    }
  };

  $scope.loadUser = function() { //Issues a GET request to /api/movies/:id to get a movie to update
    $scope.user = UserService.get({ id: $stateParams.id });
  };
 
  $scope.loadUser(); // Load a movie which can be edited on UI
});


hirdetekUserApp.controller('UserCreateController', function($scope, $state, $stateParams, UserService) {
  
  $scope.user = new UserService();  //create new movie instance. Properties will be set via ng-model on UI
 
  $scope.addUser = function() { //create a new movie. Issues a POST to /api/movies
    $scope.user.$save(function() {
      $state.go('users'); // on success go back to home i.e. movies state.
    });
  };

});