<?php 
// Check if the form is submitted
$mm_conv = 25.4; // mm per inch 

if ( ! isset( $_POST['submit'] ) ) {
  echo "<h1>Something went horribly wrong...</h1><br> Fly, you fools!";
  exit;
}

//assume here that we have a post submission
$columns_per_page = $_REQUEST['columns_per_page'];//2;
$font_size = $_REQUEST['font_size'];//8;

$side_margins =  $_REQUEST['side_margins'] * $mm_conv;//.75
$top_margin = $_REQUEST['top_margin']  * $mm_conv;//.5
$bottom_margin = $_REQUEST['bottom_margin'] * $mm_conv;//.25
$middle_margin = $_REQUEST['middle_margin'] * $mm_conv;//1.25
$paper_width = $_REQUEST['paper_width'] * $mm_conv;//11
$paper_height = $_REQUEST['paper_height'] * $mm_conv;//8.5

$notes_text = "";
$pre1_text = "";
$pre2_text = "";
$pre3_text = "";
$post1_text = "";
$post2_text = "";
$post3_text = "";



if ($_REQUEST['notes_check'] == 1){
  $notes_text = $_REQUEST['notes_text'];
}

$pre_htmls = array();
$pre_prefixes = array('pre1', 'pre2', 'pre3');

foreach ($pre_prefixes as $pre) {
  if ($_REQUEST[$pre . '_check'] == 1){
    $pre_htmls[] = $_REQUEST[$pre . '_text'];
  }
}
$pre_pages = sizeof($pre_htmls);

$post_htmls = array();
$post_prefixes = array('post1', 'post2', 'post3');

foreach ($post_prefixes as $post) {
  if ($_REQUEST[$post . '_check'] == 1){
    $post_htmls[] = $_REQUEST[$post . '_text'];
  }
}
$post_pages = sizeof($post_htmls);


require_once('vendor/autoload.php');
$content_directory = __DIR__."/content/";
$entry_font_name = $_REQUEST['entry_font_name'];//courier times helvetica




$page_height = $paper_height;
$page_width = $paper_width / 2 ;
$carriage_return = "<br>"; 

$column_width = ($paper_width - ($side_margins * 2) - $middle_margin) / ($columns_per_page * 2);
$html_page_width = ($paper_width - ($side_margins * 2) - $middle_margin) / 2;

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

// //pre and post pages are content/pre1.html, pre2.html ...|  post1.html, post2.html...
// //let's find number of pre pages 
// $pre_pages = 0;
// while(true){
//   if(!file_exists($content_directory .  "pre" . ($pre_pages + 1) . ".html")){
//     break;
//   }
//   $pre_pages++;
// }

// //find number of post pages 
// $post_pages = 0;
// while(true){
//   if(!file_exists($content_directory . "post" . ($post_pages + 1) . ".html")){
//     break;
//   }
//   $post_pages++;
// }

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
//and populate array of entries for each column 
$pageLayout = array($paper_width, $paper_height);

$trial_pdf = new MYPDF("", PDF_UNIT, $pageLayout, true, 'UTF-8', false);
$trial_pdf->SetFont($entry_font_name, '', $font_size);
$trial_pdf->SetMargins($side_margins, $top_margin, $side_margins, true);
$trial_pdf->SetAutoPageBreak(TRUE, $bottom_margin);
$trial_pdf->AddPage();

$current_column = 1;
$dir_fpage_number = 1; //if this is odd, we are on the right hand side 


$dir_data = array();// array of pages => array of columns => array of entries
$page_data = array();
$column_data = array();
//now we will loop through directory and put into pages/columns 

//need to read request variable here
$full_csv = $_REQUEST['directory_csv'];

$csvrowsarray = explode("\n", $full_csv);
// echo "full_csv:<br>";
// print_r($full_csv);
// echo "<br><br><br>====================================================rows array:<br>";
// print_r($csvrowsarray);
// exit;


$row = 1;
if (sizeof($csvrowsarray ) > 0) {
  foreach ($csvrowsarray as $csvrow)  {
    
    $this_entry = "";
    if($row > 1){ //ignore the first row of headers 
      $rowarray = str_getcsv($csvrow);
      foreach ($rowarray as $each_cell) {
        if($each_cell !== ""){
          // echo $each_cell . "<br>";
          $this_entry .= $each_cell . $carriage_return;
        }
      }
    
      
      $xy = get_xy($dir_fpage_number,$current_column );
    
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
  		}else{ 
        //we would have popped to a new folded page 
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
          $dir_fpage_number++;
          //if next fpage is even, next fpage is on next paper page 
          if($dir_fpage_number % 2 == 0){ 
            //even fpage number, we need a new paper page
            $trial_pdf->AddPage();
          }
          
  			}//end of if($current_column > $columns_per_page){
  			
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
    }

    $row++;
  }// end of while (($data = fgetcsv($handle)) !== FALSE) {
  
  //do we have any columndata? we could have ended on end of page
  if(sizeof($column_data) > 0){
    //we need to add column data to page, add page data to dirdata 
    $page_data[$current_column] = $column_data;
    $dir_data[$dir_fpage_number] = $page_data;
  
  }
  

  
  //actually make the trial pdf
  //$trial_pdf->Output($content_directory . "trial-phone_list_".date('d-M-Y-H-s').".pdf", 'F');

  $dir_pages = $dir_fpage_number;
}


$content_fpages = $pre_pages + $dir_pages + $post_pages; 
$pieces_of_paper = ceil($content_fpages / 4);
$total_fpages = $pieces_of_paper * 4;
$printed_pages = $pieces_of_paper * 2;
$notes_fpages = $total_fpages - $content_fpages;


// for debugging
// echo "content folded pages =" . $content_fpages . "\n";
// echo "pieces of paper =" . $pieces_of_paper . "\n";
// echo "total folded pages = " . $total_fpages . "\n";
// echo "notes pages= " . $notes_fpages . "\n";


// ---------------------------------------------------------------------
//               Figure printing page order
// ---------------------------------------------------------------------

//first let's put all the pages in order
$fpage_pointers_in_order = array();
$folded_page_number = 1;

//add pre pages
foreach ($pre_htmls as $pre_html) {
  $fpage_pointers_in_order[] = array("html" => $pre_html , "pagenum" > $folded_page_number++);
} 
  

//add directory data
foreach ($dir_data as $each_dir_page) {
  $fpage_pointers_in_order[] = array("dir" => $each_dir_page, "pagenum" > $folded_page_number++);
}

//add notes pages
for ($x = 1; $x <= $notes_fpages; $x++) {
  $fpage_pointers_in_order[] = array("html" => $notes_text, "pagenum" > $folded_page_number++);
}

//add post pages
foreach ($post_htmls as $post_html) {
  $fpage_pointers_in_order[] = array("html" => $post_html , "pagenum" > $folded_page_number++);
} 


//  now to resort into printing order 
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
$pdf->SetFont($entry_font_name, '', $font_size);
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
  if(array_key_exists("html",$ordered_page )){
    $page_html = $ordered_page["html"];
  }elseif(array_key_exists("dir",$ordered_page )){
    $page_dir_array = $ordered_page["dir"];  
  }else{
    write_log("something went wrong, we don't know what type of fpage this is");
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
  }
  $fpage_counter++;
}

$pdf->Output($content_directory . "phone_list_".date('d-M-Y-H-i').".pdf", 'I');
