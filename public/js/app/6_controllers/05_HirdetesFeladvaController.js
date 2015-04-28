
hirdetekApp.controller('HirdetesFeladvaController', function($scope, $rootScope, $state, $stateParams, HirdetesService, KepService) {

  //console.log('HirdetesFeladvaController');
  //console.log($stateParams.id);

  $scope.hirdetes = {id: $stateParams.id};

  var myDropzone = new Dropzone("div#myDropzone", {
    url: "/hirdetes?id=" + $stateParams.id + "&kod=" + $rootScope.kod,
    maxFiles: 6,
    acceptedFiles: 'image/jpeg, image/gif, image/png',
    dictRemoveFile: '',
    dictMaxFilesExceeded: 'Max 6 picture can be uploaded',
    headers: {'Authorization': 'Bearer ' + $rootScope.user.getTk()},
    init: function() {
      this.on("addedfile", function(file) {
        // Create the remove button
        var removeButton = Dropzone.createElement("<button class='btn btn-dropzone'>Kép törlés</button>");

        // Capture the Dropzone instance as closure.
        var _this = this;
        var _file = file;

        // Listen to the click event
        removeButton.addEventListener("click", function(e) {
          // Make sure the button click doesn't submit the form:
          e.preventDefault();
          e.stopPropagation();

          var image_id = angular.fromJson(_file.xhr.response).image_id;

          //console.log(image_id);

          KepService.delete({id: image_id}, function(response) {
              //console.log(response);
              //$scope.response = response;

              // Remove the file preview.
              _this.removeFile(file);
          });
        });
        file.previewElement.appendChild(removeButton);
      });
    }
  });
});
