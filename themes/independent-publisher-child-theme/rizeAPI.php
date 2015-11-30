<?php
header('Content-Type: application/json');
	$item1 = array(
		"id" => "7",
		"type" => "person", 
		"name"=>"Anne Amuzu", 
		"image" => "https://pbs.twimg.com/profile_images/1692599621/jn__7_of_7__400x400.jpg",
		"twitter" => "Ewoenam",
		"linkedIn" => "",
		"description" => ""
	);

	$item2 = array(
		"id" => "11",
		"type" => "company", 
		"name" => "Nandi Mobile", 
		"image" => "https://pbs.twimg.com/profile_images/1879209071/nandimobile_Logo_400x400.jpg", 
		"twitter" => "nandimobile", 
		"linkedIn" => "",
		"description" => "Infoline service allows local hospitals, churches, NGOs and other businesses to track customer comments, send bulk messages to members using SMS or market specific elements of their organization to potential clients.", 
		"facebook" => "https://www.facebook.com/Nandimobile"
	);
	$item3 = array(
		"id" => "16",
		"type" => "company", 
		"name" => "Meltwater Entrepreneurial School of Technology", 
		"image" => "https://pbs.twimg.com/profile_images/595568237359697920/LAOG2PeI_400x400.png",
		"twitter" => "MESTghana",
		"linkedIn" => "",
		"description" => ""
	);
	$item4 = array(
		"id" => "34",
		"type" => "person", 
		"name" => "Edward Tagoe", 
		"image" => "https://pbs.twimg.com/profile_images/617062478510444544/91yvJhOu_400x400.jpg",
		"twitter" => "ttaaggooee",
		"linkedIn" => "",
		"description" =>""
	);

	$items = array($item1,$item2,$item3,$item4);

	if (isset ($_GET['id'])) {
		$output=array();
		foreach($items as $item) {
			if ($_GET['id'] == $item['id']) {
				$output=$item;
			}
		}
	} else {
		$output=$items;
	}

	echo json_encode($output);

?> 