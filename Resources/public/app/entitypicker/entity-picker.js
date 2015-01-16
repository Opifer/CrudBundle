angular.module('OpiferEntityPicker', ['ui.bootstrap.typeahead'])

    .directive('entityPicker', function() {

        var tpl =
            '<input type="text" ng-model="search" typeahead="object.name for object in getObject($viewValue)" typeahead-on-select="onSelect($item, $model, $label)" typeahead-loading="loadingLocations" class="form-control">' +
            '<i ng-show="loadingLocations" class="glyphicon glyphicon-refresh"></i>';

        return {
            restrict: 'E',
            transclude: true,
            scope: {
                subject: '=',
                configuration: '='
            },
            template: tpl,
            controller: function($scope, $http, $attrs) {

                // Get the object by search term
                $scope.getObject = function(term) {
                    return $http.get(Routing.generate($scope.configuration.searchRoute(), [], true), {
                        params: {
                            term: term
                        }
                    }).then(function(response){
                        return response.data.map(function(item){
                            return item;
                        });
                    });
                };

                // Select the object
                $scope.onSelect = function(item, model, label) {
                    if (angular.isUndefined($scope.subject.right.value)) {
                        $scope.subject.right.value = [];
                    }

                    if ($scope.subject.right.value.indexOf(item.id) == -1) {
                        $scope.subject.right.value.push(item.id);
                        $scope.configuration.add(item);
                    }

                    $scope.search = null;
                };
            }
        };
    })
;
