<?php
/*** set the content type header ***/
header("Content-type: text/css");

/***
query the database for the background color
provided you have a valid MySQL connection or whatever you are using.
 ***/
$background_color = 'red';

/** set the paragraph color ***/
$para_color = '#0000ff';

/*** set the heading size ***/
$heading_size = '2em';

/*** set the heading color ***/
$heading_color = '#c0c0c0';
?>

html{
background: <?php echo $background_color; ?>
}

p{
color: <?php echo $para_color;  ?>;
font-weight: bold;
font-size: 1.2em;
text-align: left;
}

h1{
color: <?php echo $heading_color; ?>;
font-size = <?php echo $heading_size; ?>;
text-align: centre;
}