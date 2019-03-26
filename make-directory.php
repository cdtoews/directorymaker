<?php 

$columns_per_page = 2;


//pre and post pages are content/pre1.html, pre2.html ...|  post1.html, post2.html...
//let's find number of pre pages 
$pre_pages = 0;
while(true){
  if(!file_exists("content/pre" . ($pre_pages + 1) . ".html")){
    break;
  }
  $pre_pages++;
}
echo "prepages: " . $pre_pages . "\n";

$post_pages = 0;
while(true){
  if(!file_exists("content/post" . ($post_pages + 1) . ".html")){
    break;
  }
  $post_pages++;
}
echo "postpages: " . $post_pages . "\n";




$dir_pages = 7;

$mm_conv = 25.4; // mm per inch 
$side_margins = .75 * $mm_conv;
$top_margin = .75 * $mm_conv;
$bottom_margin = .75 * $mm_conv;
$middle_margin = 1.25 * $mm_conv;
$paper_width = 11 * $mm_conv;
$paper_height = 8.5 * $mm_conv;
$page_height = $paper_height;
$page_width = $paper_width / 2 ;

$column_width = ($paper_width - ($side_margins * 2)) / ($columns_per_page * 2);



$content_fpages = $pre_pages + $dir_pages + $post_pages; 
$pieces_of_paper = ceil($content_fpages / 4);
$total_fpages = $pieces_of_paper * 4;
$notes_fpages = $total_fpages - $content_fpages;



echo "content folded pages =" . $content_fpages . "\n";
echo "pieces of paper =" . $pieces_of_paper . "\n";
echo "total folded pages = " . $total_fpages . "\n";
echo "notes pages= " . $notes_fpages . "\n";

/*
$row = 1;
if (($handle = fopen("content/directory.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle)) !== FALSE) {
    $num = count($data);
    //echo "<p> $num fields in line $row: <br /></p>\n";
    if($row > 1){
      for ($c=0; $c < $num; $c++) {
          if ($data[$c] !== ""){
            echo $data[$c] . "\n";
          }
          
      }
    }
    
    echo "\n";
    $row++;
  }
  echo "total number of rows: " . $row . "\n";
  fclose($handle);
}
*/
