
var hirdetekApp = angular.module('hirdetekApp', ['ngResource', 'ui.bootstrap', 'ui.router', 'ngCookies', 'cgBusy']);

// this is shit !!! but this is the best i found so far!!!
// so stupiu and typical !!!! these little shits can't programm !!!
// i need to find or write a better one!!!
hirdetekApp.directive('validPasswordC', function () {
    return {
        require: 'ngModel',
        link: function (scope, elm, attrs, ctrl) {
            ctrl.$parsers.unshift(function (viewValue, $scope) {
                var noMatch = viewValue != scope.regForm.password.$viewValue
                ctrl.$setValidity('noMatch', !noMatch)
            })
        }
    }
})

var EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;

hirdetekApp.directive('validEmail', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$parsers.unshift(function(viewValue) {
        viewValue = angular.lowercase(viewValue);
        if (EMAIL_REGEXP.test(viewValue)) {
          // it is valid
          ctrl.$setValidity('validEmail', true);
          return viewValue;
        } else {
          // it is invalid, return undefined (no model update)
          ctrl.$setValidity('validEmail', false);
          return undefined;
        }
      });
    }
  };
});

hirdetekApp.directive('uniqueEmail', ["UserService", function (UserService) {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      scope.emailChecking = false;
      ctrl.$parsers.push(function (viewValue) {
        if (viewValue) {
          scope.emailChecking = true;
          ctrl.$setValidity('uniqueEmail', true);
          UserService.get({'email':viewValue}, function (users) {
            if (users.total_items > 0) {
              ctrl.$setValidity('uniqueEmail', false);
            } else {
              ctrl.$setValidity('uniqueEmail', true);
            }
          }).$promise.then(function(){
            scope.emailChecking = false;
          });
          return viewValue;
        }
      });
    }
  };
}]);

// postcode csv: http://www.freemaptools.com/download-uk-postcode-lat-lng.htm
// mysqlimport --ignore-lines=1 --fields-terminated-by=, --columns='id,postcodes,latitude,longitude' --local -u root -p aprohirdeto postcodes.csv
hirdetekApp.directive('validPostcode', ["UserService", function (UserService) {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      scope.postcodeChecking = false;
      ctrl.$parsers.push(function (viewValue) {
        if (viewValue) {
          scope.postcodeChecking = true;
          ctrl.$setValidity('validPostcode', true);
          UserService.get({'postcode':viewValue}, function (users) {
            console.log(users);
            if (users.total_items < 1) {
              ctrl.$setValidity('validPostcode', false);
            } else {
              ctrl.$setValidity('validPostcode', true);
            }
          }).$promise.then(function(){
            scope.postcodeChecking = false;
          });
          return viewValue;
        }
      });
    }
  };
}]);
/*
hirdetekApp.directive('uniqueEmail', ["Users", function (Users) {
  return {
    require:'ngModel',
    restrict:'A',
    link:function (scope, el, attrs, ctrl) {

      console.log('fuck');

      //TODO: We need to check that the value is different to the original

      //using push() here to run it as the last parser, after we are sure that other validators were run
      ctrl.$parsers.push(function (viewValue) {

        if (viewValue) {
          Users.query({email:viewValue}, function (users) {
            if (users.length === 0) {
              ctrl.$setValidity('uniqueEmail', true);
            } else {
              ctrl.$setValidity('uniqueEmail', false);
            }
          });
          return viewValue;
        }
      });
    }
  };
}]);
*/

//https://robots.thoughtbot.com/preload-resource-data-into-angularjs
hirdetekApp.directive("preloadResource", function() {
    return {
      link: function(scope, element, attrs) {
        scope.preloadResource = JSON.parse(attrs.preloadResource);
        element.remove();
      }
    };
  });

hirdetekApp.value('cgBusyDefaults',{
    message:'Loading...'
});
