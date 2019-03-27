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

if ( ! function_exists('get_xy')) {
   function get_xy ( $fpage_number, $current_column = 1 )  {
     global $side_margins,  $column_width, $paper_width, $middle_margin, $top_margin;
     if($fpage_number % 2 == 0){ 
       //even fpage number we are the left hand side of paper page
       $column_x = $side_margins + ($column_width * ($current_column - 1));
     }else{ 
       //we are on the right hand side of paaper page 
       $column_x = ($paper_width / 2) + ($middle_margin /2) + ($column_width * ($current_column - 1));
     } 
     $column_y = $top_margin;
     return array($column_x, $column_y);
   }
}

  
  
$content_directory = __DIR__."/content/";
require_once('vendor/autoload.php');

$columns_per_page = 2;
$font_size = 8;


//pre and post pages are content/pre1.html, pre2.html ...|  post1.html, post2.html...
//let's find number of pre pages 
$pre_pages = 0;
while(true){
  if(!file_exists($content_directory .  "pre" . ($pre_pages + 1) . ".html")){
    break;
  }
  $pre_pages++;
}
// echo "prepages: " . $pre_pages . "\n";

$post_pages = 0;
while(true){
  if(!file_exists($content_directory . "post" . ($post_pages + 1) . ".html")){
    break;
  }
  $post_pages++;
}
// echo "postpages: " . $post_pages . "\n";






$mm_conv = 25.4; // mm per inch 
$side_margins = .75 * $mm_conv;
$top_margin = .5 * $mm_conv;
$bottom_margin = .25 * $mm_conv;
$middle_margin = 1.25 * $mm_conv;
$paper_width = 11 * $mm_conv;
$paper_height = 8.5 * $mm_conv;
$page_height = $paper_height;
$page_width = $paper_width / 2 ;
$carriage_return = "<br>"; // possibly <br>

$column_width = ($paper_width - ($side_margins * 2) - $middle_margin) / ($columns_per_page * 2);
$html_page_width = ($paper_width - ($side_margins * 2) - $middle_margin) / 2;

class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			$header_text = "";

			if ($header_text != "") {
				// Set font
				$this->SetFont('helvetica', 'B', 15);

				$this->Cell(0, 15, $header_text, 0, false, 'C', 0, '', 0, false, 'M', 'B');
			}
		}
	}//end of class 



//let's make trial pages to see how many will be taken up by directory content 
$pageLayout = array($paper_width, $paper_height);
$trial_pdf = new MYPDF("", PDF_UNIT, $pageLayout, true, 'UTF-8', false);
$trial_pdf->SetFont('helvetica', '', $font_size);
$trial_pdf->SetMargins($side_margins, $top_margin, $side_margins, true);
$trial_pdf->SetAutoPageBreak(TRUE, $bottom_margin);
$trial_pdf->AddPage();

$current_column = 1;
$dir_fpage_number = 1; //if this is odd, we are on the right hand side 


$dir_data = array();// array of pages => array of columns => array of entries
$page_data = array();
$column_data = array();
//now we will loop through directory and put into pages/columns 
$row = 1;
if (($handle = fopen($content_directory . "directory.csv", "r")) !== FALSE) {
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
      $xy = get_xy($dir_fpage_number,$current_column );
      // $column_x = 0;
      // if($dir_fpage_number % 2 == 0){ 
      //   //even fpage number we are the left hand side of paper page
      //   $column_x = $side_margins + ($column_width * ($current_column - 1));
      // }else{ 
      //   //we are on the right hand side of paaper page 
      //   $column_x = ($paper_width / 2) + ($middle_margin /2) + ($column_width * ($current_column - 1));
      // } 
      // $column_y = $top_margin;
      
      $start_page = $trial_pdf->getPage();
      $trial_pdf->startTransaction();
      $trial_pdf->MultiCell($column_width, 1, $this_entry , 0, 'J', 0, 2, $xy[0], '', true , 0, true, true, 0, 'T', true);
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
          $dir_data[$dir_fpage_number] = $page_data;
          //reset $page_data
          $page_data = array();
                    
          $current_column = 1;
          //new fpage 
          $dir_fpage_number++;
          //if next fpage is even, next fpage is on next paper page 
          if($dir_fpage_number % 2 == 0){ 
            //even fpage number, we need a new paper page
            $trial_pdf->AddPage();
            
          }
          
          
  			}//end of if($current_column > $columns_per_page){
  			
        //reset X and Y to next column 
        if($dir_fpage_number % 2 == 0){ 
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
    $dir_data[$dir_fpage_number] = $page_data;
    //note, last column is not on pdf 
  }
  
  
  // echo "total number of rows: " . $row . "\n";
  fclose($handle);
  
  //actually make the trial pdf
  //$trial_pdf->Output($content_directory . "trial-phone_list_".date('d-M-Y-H-s').".pdf", 'F');

  //write_log($dir_data);
  $dir_pages = $dir_fpage_number;
  // write_log("dir folded pages total" . $dir_pages);
}


$content_fpages = $pre_pages + $dir_pages + $post_pages; 
$pieces_of_paper = ceil($content_fpages / 4);
$total_fpages = $pieces_of_paper * 4;
$printed_pages = $pieces_of_paper * 2;
$notes_fpages = $total_fpages - $content_fpages;


// 
// echo "content folded pages =" . $content_fpages . "\n";
// echo "pieces of paper =" . $pieces_of_paper . "\n";
// echo "total folded pages = " . $total_fpages . "\n";
// echo "notes pages= " . $notes_fpages . "\n";


// ---------------------------------------------------------------------
//               Figure printing page order
// ---------------------------------------------------------------------




//first let's put all the pages in order, then we can resort 
$fpage_pointers_in_order = array();
$folded_page_number = 1;
//add pre pages
for ($x = 1; $x <= $pre_pages; $x++) {
  $fpage_pointers_in_order[] = array("pre" => $x , "pagenum" > $folded_page_number++);
}

//add directory data
foreach ($dir_data as $each_dir_page) {
  $fpage_pointers_in_order[] = array("dir" => $each_dir_page, "pagenum" > $folded_page_number++);
}

//add notes pages
for ($x = 1; $x <= $notes_fpages; $x++) {
  $fpage_pointers_in_order[] = array("notes" => $x, "pagenum" > $folded_page_number++);
}

//add post pages
for ($x = 1; $x <= $post_pages; $x++) {
  $fpage_pointers_in_order[] = array("post" => $x , "pagenum" > $folded_page_number++);
}

//  now to resort into printing order 
//let's make a fpage array 
$ordered_pages = array();
//odd printed page numbers are "last -n, first +n", even page numbers are "first +n, last -n"

for ($x = 1; $x <= $printed_pages; $x++) {
  if($x % 2 == 0){
    //even page number ,"first +n, last -n"
    $ordered_pages[] = array_shift($fpage_pointers_in_order); //first entry in current array 
    $ordered_pages[] = array_pop($fpage_pointers_in_order); //last entry in current array
  }else{
    //odd page number  "last -n, first +n"
    $ordered_pages[] = array_pop($fpage_pointers_in_order); //last entry in current array
    $ordered_pages[] = array_shift($fpage_pointers_in_order); //first entry in current array 
  }
}


// ------------------------------------------------------------------------
//                      Make the PDF 
// ------------------------------------------------------------------------

$pageLayout = array($paper_width, $paper_height);
$pdf = new MYPDF("", PDF_UNIT, $pageLayout, true, 'UTF-8', false);
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetMargins($side_margins, $top_margin, $side_margins, true);
$pdf->SetAutoPageBreak(TRUE, $bottom_margin);
$pdf->AddPage();

$fpage_counter = 0;
foreach ($ordered_pages as $ordered_page) {
  //if this $fpage_counter is even, we need a new ldap_control_paged_result
  if($fpage_counter %2 == 0 && $fpage_counter > 0){
    $pdf->AddPage();
  }
  
  $page_html = "";
  $page_dir_array = array();
  if(array_key_exists("pre",$ordered_page )){
    //pre page 
    $page_html = file_get_contents($content_directory . "pre" . $ordered_page["pre"] . ".html");
    // write_log("====================pre:===================================");
    // write_log($page_html);
  }elseif(array_key_exists("post",$ordered_page )){
    // write_log("--------------ordered page that contains post---------------");
    // write_log($ordered_page);
    // write_log("--------------ordered page['post']--------------");
    // write_log($ordered_page["post"]);
    $page_html = file_get_contents($content_directory . "post" . $ordered_page["post"] . ".html");
  }elseif(array_key_exists("notes",$ordered_page )){
    $page_html = file_get_contents($content_directory . "notes.html");
  }elseif(array_key_exists("dir",$ordered_page )){
    $page_dir_array = $ordered_page["dir"];  
  }else{
    write_log("something went wrong, we don't know what type of page this is");
  }
  
  
  
  if($page_html !== ""){
    $xy = get_xy($fpage_counter, 1);
    $pdf->MultiCell($html_page_width, 1, $page_html , 0, 'J', 0, 2, $xy[0], $xy[1], true , 0, true, true, 0, 'T', true);
  }else{
    //directory page 
    $column_number = 1;
    
    foreach ($page_dir_array as $each_column) {
      $xy = get_xy($fpage_counter, $column_number);
      $pdf->SetXY($xy[0],$xy[1], true);
      foreach($each_column as $each_dir_entry){
        $pdf->MultiCell($column_width, 1, $each_dir_entry , 0, 'J', 0, 2, $xy[0], '', true , 0, true, true, 0, 'T', true);
  		  
        
      }
      $column_number++;
      
    }
    
    
    
  }//end of if/else for   if($page_html !== ""){
  
  
  
  $fpage_counter++;
}

  $pdf->Output($content_directory . "phone_list_".date('d-M-Y-H-s').".pdf", 'F');
