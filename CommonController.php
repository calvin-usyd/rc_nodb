<?php
//this controller is separate from the another for security reason to control path authorization.

$whitelist = array(
    '1217.0.0.1',
    '::1'
);

// display error info when in localhost (not in production)
if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
    // not valid
	ini_set('display_errors', 'On');
	ini_set('html_errors', 0);
}

$process = $_GET['p'];

if ($process == 'vregister'){
	$register = new register();
	$register->checkReg('d_voter');
	
}elseif ($process == 'vlogin'){
	$login = new login();
	$login->process('d_voter');
	
}elseif ($process == 'register'){
	$register = new register();
	$register->checkReg('d_student');
	
}elseif ($process == 'login'){
	$login = new login();
	$login->process('d_student');
	
}elseif ($process == 'upload'){
	$si = new studentIndex();
	$si->upload();
	
}elseif ($process == 'json'){
	$si = new studentIndex();
	$si->jsonDetail();

}elseif ($process == 'logout'){
	$login = new login();
	$login->clearSession();
	
}elseif ($process == 'panel'){
	$posters = new posters();
	$posters->loadPanel();
	
}elseif ($process == 'UndergraduateCE' || $process == 'PostgraduateCE' || $process == 'PostgraduatePM' || $process == 'UndergraduatePM'){
	$posters = new posters();
	$posters->loadImages($process);
	
}elseif ($process == 'populateVotesUndergraduateCE'){
	$posters = new posters();
	$posters->populateVotes('UndergraduateCE');
	
}elseif ($process == 'populateVotesPostgraduateCE'){
	$posters = new posters();
	$posters->populateVotes('PostgraduateCE');
	
}elseif ($process == 'populateVotesUndergraduatePM'){
	$posters = new posters();
	$posters->populateVotes('UndergraduatePM');
	
}elseif ($process == 'populateVotesPostgraduatePM'){
	$posters = new posters();
	$posters->populateVotes('PostgraduatePM');
	
}elseif ($process == 'loadImages2014'){
	$posters = new posters();
	$posters->loadImages2014();
	
}elseif ($process == '2015PostgraduateCE'){
	$posters = new posters();
	$posters->loadImagesYearType('2015', 'PostgraduateCE');
	
}elseif ($process == '2015Undergraduate'){
	$posters = new posters();
	$posters->loadImagesYearType('2015', 'Undergraduate');
	
}elseif ($process == '2016PostgraduateCE'){
	$posters = new posters();
	$posters->loadImagesYearType('2016', 'PostgraduateCE');
	
}elseif ($process == '2016UndergraduateCE'){
	$posters = new posters();
	$posters->loadImagesYearType('2016', 'UndergraduateCE');
	
}elseif ($process == '2016PostgraduatePM'){
	$posters = new posters();
	$posters->loadImagesYearType('2016', 'PostgraduatePM');
	
}elseif ($process == '2016UndergraduatePM'){
	$posters = new posters();
	$posters->loadImagesYearType('2016', 'UndergraduatePM');
	
}elseif ($process == 'posterDetail'){
	$posters = new posters();
	$posters->posterDetail();
	
}elseif ($process == 'search'){
	$posters = new posters();
	$posters->search();
	
}elseif ($process == 'submitVoteUndergraduateCE'){
	$submitVote = new submitVote();
	$submitVote->process('UndergraduateCE');
	
}elseif ($process == 'submitVotePostgraduateCE'){
	$submitVote = new submitVote();
	$submitVote->process('PostgraduateCE');
	
}elseif ($process == 'submitVoteUndergraduatePM'){
	$submitVote = new submitVote();
	$submitVote->process('UndergraduatePM');
	
}elseif ($process == 'submitVotePostgraduatePM'){
	$submitVote = new submitVote();
	$submitVote->process('PostgraduatePM');
	
}elseif ($process == 'rep'){
	$report = new Report();
	$report->analyse();
	
}elseif ($process == 'posterTitle'){
	$report = new Report();
	$report->getAllPosterTitle();
	
}

?>