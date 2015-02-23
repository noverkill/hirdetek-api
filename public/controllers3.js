var oldTime = 0;
showTime();

var hirdetekApp = angular.module('hirdetekApp',[]);

hirdetekApp.controller('HirdetesListCtrl', [ '$scope', '$http', function ($scope, $http) {

   $scope.pageChanged = function(p) {
      $scope.hirdetesek = $scope.hirdets.slice(p,p+5);
  }

  $http.get('http://hirdetek-api.com/hirdetes?ord=feladas&ordir=DESC&page=1&regio=0&rovat=0&search=')
  .success(function(response){
    //console.log(response);
    $scope.hirdets = response._embedded.hirdetes;
    $scope.totalItems = response.total_items;
    $scope.pageChanged(1);

    showTime();
  });

}]);

function showTime() {

    var date = new Date();
    var newTime = date.getTime();

    console.log(newTime);
    console.log('oldTime: ' + oldTime);

    if(oldTime>0) {
      var elapsed = newTime - oldTime;
      console.log('Elapsed: ' + elapsed);
    }

    oldTime = newTime;
}

