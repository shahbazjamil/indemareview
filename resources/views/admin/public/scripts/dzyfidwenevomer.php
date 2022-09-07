<?php
/**
 * Created by PhpStorm.
 *
 * @category        Mohid
 * @author          Khurram Shahzad
 * @copyright       2012 DeenTek Solutions
 * @link            http://www.deentek.com
 * @version         4.0
 * @Date            12/31/2019
 * @Time            11:42 AM
 */

$targetFolder = '/filemanager/';

if (!empty($_POST)) {
	$sub_folder = $_POST['sub_folder'];
	$file_name = $_POST['file_name'];

	$targetPath = getcwd();
    $targetPath .= $targetFolder;
    $targetPath = str_replace('scripts/','',$targetPath);
	$targetPath = $targetPath.$sub_folder.'/';

	$targetFile = $targetPath.$file_name;

    $flag = false;

	if (file_exists($targetFile)) {
        unlink($targetFile);

        $flag = true;
	}

	if ($flag) {
        $output = [
            'code' => '200',
            'message' => 'Success'
        ];
    } else {
        $output = [
            'code' => '400',
            'message' => 'Error'
        ];
    }

    echo json_encode($output);
}
?>