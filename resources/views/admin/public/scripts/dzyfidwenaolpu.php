<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

$targetFolder = '/filemanager/';

if (!empty($_FILES)) {
	$sub_folder = $_POST['sub_folder'];

	$get_image_data = !empty($_POST['get_image_data']) ? $_POST['get_image_data'] : FALSE;
	$image_data = array();
    
	$tempFile = preg_replace('/\s+/', '', $_FILES['Filedata']['tmp_name']);
	$targetPath = getcwd();
	$targetPath .= $targetFolder;
	$targetPath = str_replace('scripts/','',$targetPath);

	$fileName = rand_string(5).'_'.preg_replace('/\s+/', '', $_FILES['Filedata']['name']);
	$targetPath = $targetPath.$sub_folder.'/';

	$targetFile = $targetPath.$fileName;
    
	$fileTypes = array('JPG','jpg','jpeg','JPEG','gif','GIF','png','PNG','pdf','PDF','xls','xlsx','doc','docx');
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
	}
    
    // Getting Image Dimensions
    if (file_exists($targetFile)) {
        $mimeTypes = array('image/jpeg','image/png','image/gif');
        $image_dimension = getimagesize($targetFile);
        if (!empty($image_dimension['mime']) AND in_array($image_dimension['mime'], $mimeTypes)) {
            if (!empty($image_dimension[0])) {
                $image_data['width'] = $image_dimension[0];
                $image_data['height'] = $image_dimension[1];
            }
        }
    }
    
	$msg = '';
	////////////////////////////////////////////////////
    switch ($_FILES['Filedata']['error'])
    {     
        case 0:
             //$msg = "No Error"; // comment this out if you don't want a message to appear on success.
             break;
        case 1:
              $msg = "The file is bigger than this PHP installation allows";
              break;
        case 2:
              $msg = "The file is bigger than this form allows";
              break;
        case 3:
              $msg = "Only part of the file was uploaded";
              break;
        case 4:
             $msg = "No file was uploaded";
              break;
        case 6:
             $msg = "Missing a temporary folder";
              break;
        case 7:
             $msg = "Failed to write file to disk";
             break;
        case 8:
             $msg = "File upload stopped by extension";
             break;
        default:
            $msg = "unknown error ".$_FILES['Filedata']['error'];
            break;
    }

    if ($msg) {
        $output = [
            'code' => '400',
            'msg' => $msg,
            'file_name' => '',
            'image_data' => '',
            'error' => $_FILES['Filedata']['error']
        ];
    } else {
        if ($get_image_data AND !empty($image_data)) {
            $output = [
                'code' => '200',
                'msg' => '',
                'file_name' => $fileName,
                'image_data' => $image_data,
                'error' => ''
            ];
        } else {
            $output = [
                'code' => '200',
                'msg' => '',
                'file_name' => $fileName,
                'image_data' => '',
                'error' => ''
            ];
        }
    }

    echo json_encode($output);
}


	function rand_string( $length ) {
		$str = '';
		$chars = "0123456789";	
	
		$size = strlen( $chars );
		for( $i = 0; $i < $length; $i++ ) {
			$str .= $chars[ rand( 0, $size - 1 ) ];
		}
	
		return 'FILE-'.$str;
	}	

?>