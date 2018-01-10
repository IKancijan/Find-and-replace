<!DOCTYPE html>
<html>
<head>
    <title>Find and replace</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style type="text/css">
        xmp{white-space: pre-wrap}
    </style>
</head>
<body>
<div class="container">

    <h1>Find and replace</h1><br>
    <small>By: I. Kancijan</small><br>
    <small>v0.0.1 alpha</small><br>
    <hr>

    <form id="far" name="far" action="" method="post">
        <p>Find:</p>
        <textarea name="find"></textarea>
        <p>Replace:</p>
        <textarea name="replace"></textarea>
        <p><input type="submit" value="Submit"></p>
    </form>

<?php

$output = '';
$error_log = '-';

function show_files(){

    $path = realpath(dirname(__FILE__));
    if ($handle = opendir($path)) {

        $output .= '<p><h3>List of files:</h3></p><p><ol style="width:1000px">';

        while (false !== ($file = readdir($handle))) {

            $old = array();
            $new = array();
            $path_to_file = $path.DIRECTORY_SEPARATOR.$file;

            if ('.' === $file || '..' === $file || basename(__FILE__) === $file || substr($file,-strlen(".php")) === ".php") continue;
            if ('error_log' === $file) {

                $error_log = file_get_contents($path_to_file);
                continue;
            }

            // do something with the file

            if(substr($file,-strlen(".html")) === ".html" && is_file($file)){
            
                $output .= '<li><p><a href="'.$file.'">'.$file.'</a></p>';
            }
        }
        closedir($handle);
        $output .= '</ol></p>';
    }
    echo $output;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(!empty($_POST['find']) && !empty($_POST['replace'])){


        $find = $_POST['find'];
        $replace = $_POST['replace'];
        $path = realpath(dirname(__FILE__));
        
        $output .= '<p>Find: <xmp>"'.$find.'"</xmp> and replace: <xmp>"'.$replace.'"</xmp></p>';

        if ($handle = opendir($path)) {

            $output .= '<p><h3>List of changes:</h3></p><p><ol style="width:1000px">';

            while (false !== ($file = readdir($handle))) {

                $old = array();
                $new = array();
                $path_to_file = $path.DIRECTORY_SEPARATOR.$file;

                if ('.' === $file || '..' === $file || basename(__FILE__) === $file || substr($file,-strlen(".php")) === ".php") continue;
                if ('error_log' === $file) {

                    $error_log = file_get_contents($path_to_file);
                    continue;
                }

                // do something with the file
                

                if(substr($file,-strlen(".html")) === ".html" && is_file($file)){
                
                    $output .= '<li><p><a href="'.$file.'">'.$file.'</a></p>';
                    $line_number_old = 0;
                    foreach(file($file) as $line) {
                        $line_number_old += 1;

                        if(strpos($line, $find)) $old[] = array($line_number_old, '<xmp>'.$line.'</xmp>');
                            //$output .= '<tr><td>'.$line_number_old.':</td><td><xmp>'.$line.'</xmp></td></tr>';
                        
                        //echo $line_number.'<xmp>'.$line.'</xmp><hr><br>';
                    }

                    $file_contents = file_get_contents($file);
                    $file_contents = str_replace($find,$replace,$file_contents, $count);
                    file_put_contents($path_to_file,$file_contents);

                    if($count !== 0){

                        $line_number_new = 0;
                        foreach(file($file) as $line) {
                            $line_number_new += 1;

                            if(strpos($line, $replace)) $new[] = array($line_number_new, '<xmp>'.$line.'</xmp>');
                                //$output .= $line_number_new.':<hr><xmp>'.$line.'</xmp><hr><br>'; 
                            //echo $line_number.'<xmp>'.$line.'</xmp><hr><br>';
                        }
                    

                        $output .= '<div class="col-md-6"><table class="table table-responsive"><tr><th>Line</th><th>Old</th></tr>';
                        foreach($old as $row){
                            $output .= '<tr>';

                            foreach($row as $cell) {
                               $output .= '<td>' . $cell . '</td>';
                            }
                            $output .= '</tr>';
                        }
                        $output .= '</table></div>';
                        $output .= '<div class="col-md-6"><table class="table table-responsive"><tr><th>Line</th><th>New</th></tr>';
                        foreach($new as $row){
                            $output .= '<tr>';

                            foreach($row as $cell) {
                               $output .= '<td>' . $cell . '</td>';
                            }
                            $output .= '</tr>';
                        }
                        $output .= '</table></div>';
                    }
                    $output .= '<p>Number of changes: '.$count.'</p></li>';

                    
                }

            }
            closedir($handle);
            $output .= '</ol></p>';
        }
    }else{
        $output .= '<h1>Fill up textareas!</h1>';
        show_files();
    }

}else{
   show_files();
}
echo $output;
?>
<hr>
<p>
   <small>Error log:</small> <br>
<?php 

    echo $error_log;

?>

</p>
</div>
</body>
</html>