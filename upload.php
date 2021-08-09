<?php

if(isset($_FILES['images']))
{
	$filesName = [];
    $files = $_FILES['images']['name'];

    for($i = 0; $i < count($files); $i++)
	{

        $extension = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
		$new_name = uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['images']['tmp_name'][$i], 'images/' . $new_name);
        $filesName[] = 'images/' . $new_name;
	}

	echo json_encode($filesName);
}
?>