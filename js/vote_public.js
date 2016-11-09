var graduateTypeVal = graduateType.getAttribute('value');

angular.module( "voteApp", ['ngAnimate', 'ngSanitize', 'mgcrea.ngStrap'])

.config(function($modalProvider) {
  angular.extend($modalProvider.defaults, {
    html: true
  });
})
.controller('voteController', ['$scope', '$http', '$modal', function($scope, $http, $modal) {

  $scope.authFail = 0;
  $scope.authSucc = 0;
  $scope.posters = [];
  $scope.allData = [];
  $scope.currPage = 1;
  $scope.ttlItem = 9;
  $scope.currPos = 0;
  
  ($scope.allPoster = function(){
	  $scope.searchKey = '';
	  
		$http.get('panel/controller.php?p='+graduateTypeVal).success(function(data) {
		  /*if (data['code'] == 'fail'){
			$scope.authFail = 1;
			$scope.authSucc = 0;
			$scope.authMsg = data['msg'];
			window.location.replace('/rc/panel/login.html');
		  }else{*/
			$scope.authFail = 0;
			$scope.authSucc = 1;
			//console.log(data);
			
			$scope.allData = data;
			$scope.totalRec = data.length;
			
			$scope.ttlItem = 9;
			$scope.ttlItem = ($scope.totalRec - $scope.ttlItem > 0) ? $scope.ttlItem : $scope.totalRec ;
			$scope.currPos = $scope.ttlItem;
			
			$scope.paging(1);
			
			//20 per page, total page = total_item / 20
			var ttlPage = data.length / $scope.ttlItem;
			var offset = (data.length % $scope.ttlItem) > 0 ? 1 : 0;
			ttlPage = ttlPage + offset;
			
			$scope.pagingArray = [];
			
			for (x=1; x<=ttlPage; x++){
			  $scope.pagingArray[x-1] = x;
			}
			//console.log($scope.ttlItem);
			//for (i=0; i<$scope.ttlItem; i++){
				//$scope.posters.push($scope.allData[i]);
			//}

			//console.log($scope.currPos);
		  //}
		});
	})();
	
	$scope.search = function(){
		$http.post('panel/controller.php?p=search', {key:$scope.searchKey, type:graduateTypeVal}).success(function(data) {
		  if (data['code'] == 'fail'){
			$scope.authFail = 1;
			$scope.authSucc = 0;
			$scope.authMsg = data['msg'];
			window.location.replace('/rc/panel/login.html');
		  }else{
			$scope.authFail = 0;
			$scope.authSucc = 1;
			
			$scope.allData = data;
			$scope.totalRec = data.length;
			
			$scope.ttlItem = 9;
			$scope.ttlItem = ($scope.totalRec - $scope.ttlItem > 0) ? $scope.ttlItem : $scope.totalRec ;
			$scope.currPos = $scope.ttlItem;
			
			$scope.paging(1);
			
			//20 per page, total page = total_item / 20
			var ttlPage;
			
			if ($scope.ttlItem > data.length){
				ttlPage = 1;
			}else{
				ttlPage = data.length / $scope.ttlItem;
			}

			var offset = (data.length % $scope.ttlItem) > 0 ? 1 : 0;
			ttlPage = ttlPage + offset;
			
			$scope.pagingArray = [];
			
			for (x=1; x<=ttlPage; x++){
			  $scope.pagingArray[x-1] = x;
			}
			//console.log($scope.ttlItem);
			//for (i=0; i<$scope.ttlItem; i++){
				//$scope.posters.push($scope.allData[i]);
			//}

			//console.log($scope.currPos);
		  }
		});
	}
	
	$scope.paging = function(num){
	  var end = num * $scope.ttlItem;
	  var start = end - $scope.ttlItem;
	  
	  $scope.posters = [];
	  $scope.currentPaging = num;
	  
	  if (end >$scope.totalRec ){
		end = $scope.totalRec;
	  }
	  
	  for (i=start; i<end; i++){
		$scope.posters.push($scope.allData[i]);
	  }
	}
	
  $scope.prevPage = function(){
	startItem = $scope.currPos - $scope.ttlItem;
	$scope.currPos = (startItem - $scope.ttlItem > 0) ? startItem - $scope.ttlItem : 0;
	//console.log(startItem);
	//console.log($scope.currPos);
	$scope.posters = [];
	
	for (i=$scope.currPos; i<startItem; i++){
		//console.log(i);
		$scope.posters.push($scope.allData[i-1]);
	}
	$scope.currPos = $scope.currPos + $scope.ttlItem;
  }
  
  $scope.nextPage = function(){
	startItem = $scope.currPos;
	localCurrPos = (($scope.ttlItem + startItem) < $scope.totalRec) ? startItem + $scope.ttlItem : $scope.totalRec;
	 
	//console.log(startItem);
	//console.log($scope.currPos);
	//console.log($scope.totalRec);
	$scope.posters = [];
	for (i=startItem; i<localCurrPos; i++){
		$scope.posters.push($scope.allData[i-1]);
	}
	$scope.currPos = $scope.currPos + $scope.ttlItem;
  }
  
  $scope.detail = function(pdfUrl){
	projectN = '';
	supervisor = '';
	studentN1 = '';
	studentN2 = '';
	studentN3 = '';

	$http.post('panel/controller.php?p=posterDetail', {pdfUrl:pdfUrl}).success(function(data){
		if (data['code'] && data['code'] == 'fail'){
			$scope.detailMsg = data['msg'];
			
		}else{
			if (data[0]){
				data = data[0];
				projectN = data['project'];
				supervisor = data['supervisor'];
				studentN1 = data['student1'];
				studentN2 = data['student2'];
				studentN3 = data['student3'];
				//console.log(pdfUrl);
				//console.log(data);
			}
			//if (projectN && studentN1 && studentN2 && studentN3){
			if (projectN){
				$scope.modal = {
					title: ' Detail', 
					content: 'Project Name: <b>'+projectN+'</b><br/>Supervisor: <b>'+supervisor+'</b><br/>Student 1: <b>'+studentN1+'</b><br/>Student 2: <b>'+studentN2+'</b><br/>Student 3: <b>'+studentN3+'</b><br/>'
				};
				
			}else{
				$scope.modal = {title: ' Detail', content: 'Detail not found!'};
			}
		}
	});
  }
  
  $scope.votes = [];
  $scope.submitBtn = 0;
	
  $scope.addVote = function(poster){
	//if ($scope.votes.length < 3){
	  //check duplicate if at least 1 element in array
	  if ($scope.votes.length == 0){
		pushVoteArray(poster);
	  }else{
	   for (i = 0; i < $scope.votes.length; i++){
		duplicate = false;
		
		if ($scope.votes[i].imageName == poster.imageName){
		  alert('You have already selected this poster! Please select another poster.');
		  duplicate = true;
		  break;
		}
	   }
	   
	   if (!duplicate){
		  pushVoteArray(poster);
	   }
	  }
	//}else{
	  //alert('You have reached maximum of 3 selection limit.');
	//}
	
	toggleSubmitVote();
  };
  
  pushVoteArray = function(poster){
    $scope.votes.push({imageId: poster.imageId, imageName:poster.imageName, imageUrl:poster.imageUrl, pdfUrl:poster.pdfUrl});
  }
  
  toggleSubmitVote = function(){
	if ($scope.votes.length >= 1){
	  $scope.submitBtn = 1;
	}else{
	  $scope.submitBtn = 0;
	}
  }
  
  $scope.delVote = function(imageId){
    for (i = 0; i < $scope.votes.length; i++){
	  if ($scope.votes[i].imageId == imageId){
	    $scope.votes.splice(i, 1);
		break;
	  }
	}
	
	toggleSubmitVote();
  };
  
  $http.get('panel/controller.php?p=populateVotes', {}).success(function($data){
	//console.log($data);
	if ($data){
	  if ($data['vote1']){
		var imageId 	= $data['imageId1'];
		var imageName 	= $data['vote1'].split("/").pop().replace('_', ' ').replace('-', ' ');
		var imageUrl 	= $data['vote1'];
		var pdfUrl 		= $data['vote1'].replace('jpg', 'pdf');
		$scope.votes.push({imageId: imageId, imageName:imageName, imageUrl:imageUrl, pdfUrl:pdfUrl});
	  }
	  if ($data['vote2']){
		var imageId = $data['imageId2'];
		var imageName = $data['vote2'].split("/").pop().replace('_', ' ').replace('-', ' ');
		var imageUrl = $data['vote2'];
		var pdfUrl = $data['vote2'].replace('jpg', 'pdf');
		$scope.votes.push({imageId: imageId, imageName:imageName, imageUrl:imageUrl, pdfUrl:pdfUrl});
	  }
	  if ($data['vote3']){
		var imageId = $data['imageId3'];
		var imageName = $data['vote3'].split("/").pop().replace('_', ' ').replace('-', ' ');
		var imageUrl = $data['vote3'];
		var pdfUrl = $data['vote3'].replace('jpg', 'pdf');
		$scope.votes.push({imageId: imageId, imageName:imageName, imageUrl:imageUrl, pdfUrl:pdfUrl});
	  }
	}
  });

  $scope.viewSelection = 0;
  $scope.SelectionView = 'Open selection view';
  
  $scope.toggleViewSelection = function(){
	//$scope.viewSelection = ($scope.viewSelection == 1) ? 0 : 1;
	
	if ($scope.viewSelection == 1){
		$scope.SelectionView = 'Open selection view';
		$scope.viewSelection = 0;
	}else{
		$scope.SelectionView = 'Close selection view';
		$scope.viewSelection = 1;
	}
  }
  
  $scope.closeSubmitResult = function(){
	$scope.submitResult = '';
  }
  
  // The function that will be executed on button click (ng-click="search()")
  $scope.submitResult = '';
  $scope.submitVote = function() {
	var maxPosters = 3;
	
	if (graduateTypeVal == 'Undergraduate'){
		maxPosters = 5;
	}
	
	if ($scope.votes.length > maxPosters){
	  alert('You have seleted more than 5 posters! Please remove some posters from your selection.');
	  return;
	}
	
	$dataObj = {
		"imageId1" : $scope.votes[0] ? $scope.votes[0].imageId : ""
		,"imageId2" : $scope.votes[1] ? $scope.votes[1].imageId : ""
		,"imageId3" : $scope.votes[2] ? $scope.votes[2].imageId : ""
		,"imageId4" : $scope.votes[3] ? $scope.votes[3].imageId : ""
		,"imageId5" : $scope.votes[4] ? $scope.votes[4].imageId : ""
		,"vote1" : $scope.votes[0] ? $scope.votes[0].imageUrl : ""
		,"vote2" : $scope.votes[1] ? $scope.votes[1].imageUrl : ""
		,"vote3" : $scope.votes[2] ? $scope.votes[2].imageUrl : ""
		,"vote4" : $scope.votes[3] ? $scope.votes[3].imageUrl : ""
		,"vote5" : $scope.votes[4] ? $scope.votes[4].imageUrl : ""
	};
	//console.log($dataObj);
	$http.post('panel/controller.php?p=submitVote', $dataObj).success(function(data, status) {
		//console.log(data);
		$scope.status = status;
		//$scope.data = data;
		$scope.submitResult = data['msg'];
	})
	.
	error(function(data, status) {
		$scope.data = data || "Request failed";
		$scope.status = status;			
	});
  };
}]);