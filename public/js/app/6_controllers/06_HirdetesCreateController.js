
hirdetekApp.controller('HirdetesCreateController', function($rootScope, $scope, $state, $stateParams, $anchorScroll, HirdetesService) {

  $rootScope.loadRovatok();
  $rootScope.loadRegiok();

  $scope.error = 0;

  $scope.hirdetes = new HirdetesService();
  $scope.hirdetes.lejarat = 365;

  if($rootScope.user.isLogged()) {
    var user = $rootScope.user.getUser().details;
    $scope.hirdetes.nev = user.nev;
    $scope.hirdetes.email = user.email;
  }

  $scope.createHirdetes = function(formValid) {

    if(! formValid) return;

    $anchorScroll();

    $scope.hirdetesBusy = $scope.hirdetes.$save(function(response) {
        //console.log(response);
        $scope.response = response;
        if(response.success) {
          $rootScope.feladasId = response.id;
          $rootScope.kod = response.kod;
          $state.go('hirdetes-feladva',{id:response.id});
          //$scope.hirdetes = {};
        } else {
          //console.log("not response.success");
          $scope.error = 1;
          //$scope.hirdetes = response.data;
          $anchorScroll();
        }
        //$state.go('hirdetesek');
      });
  };
});
