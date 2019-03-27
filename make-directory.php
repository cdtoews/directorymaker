<?php 
	
if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}
  
  
  
require_once('vendor/autoload.php');

$columns_per_page = 2;
$font_size = 8;


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
$carriage_return = "<br>"; // possibly <br>

$column_width = ($paper_width - ($side_margins * 2)) / ($columns_per_page * 2);

//let's make trial pages to see how many will be taken up by directory content 
$pageLayout = array($paper_width, $paper_height);
$trial_pdf = new TCPDF("", PDF_UNIT, $pageLayout, true, 'UTF-8', false);
$trial_pdf->SetFont('helvetica', '', $font_size);
$trial_pdf->SetMargins($side_margins, $top_margin, $side_margins, true);
$trial_pdf->SetAutoPageBreak(TRUE, $bottom_margin);
$trial_pdf->AddPage();

$current_column = 1;
$content_fpage_number = 1; //if this is odd, we are on the right hand side 


$dir_data = array();// array of pages => array of columns => array of entries
$page_data = array();
$column_data = array();
//now we will loop through directory and put into pages/columns 
$row = 1;
if (($handle = fopen("content/directory.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle)) !== FALSE) {
    $num = count($data);
    $this_entry = "";
    if($row > 1){ //ignore the first row of headers 
      for ($c=0; $c < $num; $c++) { //ignore blank cells 
          if ($data[$c] !== ""){
            $this_entry .= $data[$c] . $carriage_return;
          }
      }
      //we have a single entry 
      //determine $column_x
      //are we on an odd page
      $column_x = 0;
      if($content_fpage_number % 2 == 0){ 
        //even fpage number we are the left hand side of paper page
        $column_x = $side_margins + ($column_width * ($current_column - 1));
      } 
      else{ 
        //we are on the right hand side of paaper page 
        $column_x = ($paper_width / 2) + ($middle_margin /2) + ($column_width * ($current_column - 1));
      } 
      $column_y = $top_margin;
      
      $start_page = $trial_pdf->getPage();
      $trial_pdf->startTransaction();
      $trial_pdf->MultiCell($column_width, 1, $this_entry , 0, 'J', 0, 2, $column_x, '', true , 0, true, true, 0, 'T', true);
		  $end_page = $trial_pdf->getPage();
      
      // check if we went over the edge of the pages
      if ($end_page == $start_page) {
  			//if we are still onthe same page, commit 
  			$trial_pdf->commitTransaction();
        //add $this_entry to $dir_data
        $column_data[] = $this_entry;
  		}else{ //we would have popped to a new folded page 
  			$trial_pdf = $trial_pdf->rollbackTransaction();
        $page_data[$current_column] = $column_data;
  			$current_column++;
  			$column_data = array();
  			if($current_column > $columns_per_page){ //last column on the page 
  				//add $page_data to $dir_data
          $dir_data[$content_fpage_number] = $page_data;
          //reset $page_data
          $page_data = array();
                    
          $current_column = 1;
          //new fpage 
          $content_fpage_number++;
          //if next fpage is even, next fpage is on next paper page 
          if($content_fpage_number % 2 == 0){ 
            //even fpage number, we need a new paper page
            $trial_pdf->AddPage();
            
          }
          
          
  			}//end of if($current_column > $columns_per_page){
  			
        //reset X and Y to next column 
        if($content_fpage_number % 2 == 0){ 
          //even fpage number we are the left hand side of paper page
          $column_x = $side_margins + ($column_width * ($current_column - 1));
        } 
        else{ 
          //we are on the right hand side of paaper page 
          $column_x = ($paper_width / 2) + ($middle_margin /2) + ($column_width * ($current_column - 1));
        } 
        $column_y = $top_margin;
  			$trial_pdf->SetXY($column_x,$column_y, true);
  			//write the text on the new column [and page ]
        $trial_pdf->MultiCell($column_width, 1, $this_entry , 0, 'J', 0, 2, $column_x, $column_y, true , 0, true, true, 0, 'T', true);
  			//add $this_entry to new $column_data
        $column_data[] = $this_entry;
  		}
      
      
      
      //end of checking if we went over the edge of hte page 
      
    }// end of if($row > 1)
    
  //  echo "\n";
    $row++;
  }// end of while (($data = fgetcsv($handle)) !== FALSE) {
  //we need to add last entry data to column, add column data to page, add page data to dirdata 
  //do we have any columndata? we could have ended on end of page
  if(sizeof($column_data) > 0){
    //we need to add column data to page, add page data to dirdata 
    $page_data[$current_column] = $column_data;
    $dir_data[$content_fpage_number] = $page_data;
    //note, last column is not on pdf 
  }
  
  
  echo "total number of rows: " . $row . "\n";
  fclose($handle);
  //ob_end_clean();
   $trial_pdf->Output(__DIR__."/phone_list_".date('d-M-Y-H-s').".pdf", 'F');
  //$trial_pdf->Output('phone_list.pdf', 'F');
  write_log($dir_data);
}


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
