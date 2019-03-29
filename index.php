<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Church Directory Maker</title>



<script>
function showhide(id) {

  var checkBox = document.getElementById(id + "_check");
  var text = document.getElementById(id + "_tr");
  if (checkBox.checked == true){
    text.style.display = "";
  } else {
     text.style.display = "none";
  }
}



 var samples = {
   "notes": <?php echo json_encode(file_get_contents("samples/notes.html")); ?>,
   "contacts": <?php echo json_encode(file_get_contents("samples/contacts.csv")); ?>,
   "pre1": <?php echo json_encode(file_get_contents("samples/pre1.html")); ?>,
    "pre2": <?php echo json_encode(file_get_contents("samples/pre2.html")); ?>,
      "post1": <?php echo json_encode(file_get_contents("samples/post1.html")); ?>
 };


function loaddata(filename,textid){
  var textbox = document.getElementById(textid);
  textbox.value = samples[filename];
  
}


</script>
</head>
<body>
 <div align="center"><br>
<b><font size="-1">use defaults and sample data in directory,
notes, pre1, pre2, post1 to get</font><font size="+3"><br>
<a href="sample-directory.pdf">This Sample output</a></font></b> <br>
<a href="#directions">Directions</a>
</div>


<form class="form-horizontal" action="/make-directory.php" method="post">
<fieldset>

<!-- Form Name -->
<legend>Directory Maker</legend>
<table border="0" cellspacing="7" cellpadding="7">
<!-- Text input-->
<tr>
<tr><div class="form-group">

<td>  <label class="col-md-4 control-label" for="paper_width">Paper Width</label>  </td>
<td>  <div class="col-md-4">
  <input id="paper_width" name="paper_width" type="text" value="11" class="form-control input-md" required=""></td>
    
  </div>
</div>
</tr>


<!-- Text input-->
<tr><div class="form-group">
<td> 
  <label class="col-md-4 control-label" for="paper_height">Paper Height</label>  </td>
<td>  
  <div class="col-md-4">
  <input id="paper_height" name="paper_height" type="text" value="8.5" class="form-control input-md" required=""></td>
    
  </div>
</div>
</tr>

<!-- Text input-->
<tr><div class="form-group">
<td> 
  <label class="col-md-4 control-label" for="columns_per_page">Columns per page</label>  </td>
<td>  
  <div class="col-md-4">
  <input id="columns_per_page" name="columns_per_page" type="text" value="2" class="form-control input-md" required=""></td>
    
  </div>
</div>
</tr>

<!-- Multiple Radios -->
<tr><div class="form-group">
<td> 
  <label class="col-md-4 control-label" for="entry_font_name">Font</label></td>
  <td>
  <div class="col-md-4">
  <div class="radio">
    <label for="entry_font_name-0">
      <input type="radio" name="entry_font_name" id="entry_font_name-0" value="helvetica" checked="checked">
      helvetica
    </label>
	</div>
  <div class="radio">
    <label for="entry_font_name-1">
      <input type="radio" name="entry_font_name" id="entry_font_name-1" value="courier">
      courier
    </label>
	</div>
  <div class="radio">
    <label for="entry_font_name-2">
      <input type="radio" name="entry_font_name" id="entry_font_name-2" value="times">
      times
    </label>
	</div>
  </div>
</div>
</tr>

<!-- Text input-->
<tr><div class="form-group">
<td> 
  <label class="col-md-4 control-label" for="font_size">Font Size</label>  </td>
<td>  
  <div class="col-md-4">
  <input id="font_size" name="font_size" type="text" value="8" class="form-control input-md" required=""></td>
    
  </div>
</div>
</tr>

<!-- Text input-->
<tr><div class="form-group">
<td> 
  <label class="col-md-4 control-label" for="top_margin">Top Margin</label>  </td>
<td>  
  <div class="col-md-4">
  <input id="top_margin" name="top_margin" type="text" value=".5" class="form-control input-md" required=""></td>
    
  </div>
</div>
</tr>

<!-- Text input-->
<tr><div class="form-group">
<td> 
  <label class="col-md-4 control-label" for="bottom_margin">Bottom Margin</label>  </td>
<td>  
  <div class="col-md-4">
  <input id="bottom_margin" name="bottom_margin" type="text" value=".5" class="form-control input-md" required=""></td>
    
  </div>
</div>
</tr>

<!-- Text input-->
<tr><div class="form-group">
<td> 
  <label class="col-md-4 control-label" for="side_margins">Side Margins</label>  </td>
<td>  
  <div class="col-md-4">
  <input id="side_margins" name="side_margins" type="text" value=".5" class="form-control input-md" required=""></td>
    
  </div>
</div>
</tr>

<!-- Text input-->
<tr><div class="form-group">
<td> 
  <label class="col-md-4 control-label" for="middle_margin">Middle Margin</label>  </td>
<td>  
  <div class="col-md-4">
  <input id="middle_margin" name="middle_margin" type="text" value="1.5" class="form-control input-md" required=""></td>
    
  </div>
</div>
</tr>

<!-- Textarea csv -->
<tr><div class="form-group">
<td>   <label class="col-md-4 control-label" >Directory CSV</label>
<br><button  type=button onclick="loaddata('contacts','directory_csv')">Load Sample Data</button></td>
<td>  <div class="col-md-4">                    
    <textarea  class="form-control" id="directory_csv" name="directory_csv"  rows="20" cols="100"></textarea>
  </div></div></td></tr>


<!-- Checkbox notes -->
<tr><div class="form-group">
<td>   <label class="col-md-4 control-label" >Enable Notes<br>html page<br>for extra pages</label></td>
<td>  <div class="col-md-4">  <div class="checkbox">
    <label >
      <input type="checkbox" name="notes_check" id="notes_check" value="1" onclick="showhide('notes')">
    </label>	</div>  </div></div></td>
</tr>

<!-- Textarea notes -->
<tr style="display:none" id="notes_tr"><div class="form-group">
<td>   <label class="col-md-4 control-label" >Notes HTML</label>
<br><button  type=button onclick="loaddata('notes','notes_text')">Load Sample Data</button>
</td>
<td>  <div class="col-md-4">                    
    <textarea  class="form-control" id="notes_text" name="notes_text"  rows="20" cols="100"></textarea>
  </div></div></td></tr>



<!-- SECTION FOR PRE HTML-->

<!-- Checkbox pre1 -->
<tr><div class="form-group">
<td> 
  <label class="col-md-4 control-label" >Enable Pre 1</label></td>
<td>  <div class="col-md-4">
  <div class="checkbox">
    <label for="pre1_enable-0">
      <input type="checkbox" name="pre1_check" id="pre1_check" value="1" onclick="showhide('pre1')">
      
    </label>
	</div>
  </div>
</div></td>
</tr>

<!-- Textarea pre 1-->
<tr style="display:none" id="pre1_tr"><div class="form-group">
<td> 
  <label class="col-md-4 control-label" for="pre1">Pre 1 HTML</label>
<br><button  type=button onclick="loaddata('pre1','pre1_text')">Load Sample Data</button></td>
<td>  <div class="col-md-4">                     
    <textarea  class="form-control" id="pre1_text" name="pre1_text"  rows="20" cols="100"></textarea>
  </div>
</div></td>
</tr>



<!-- Checkbox pre 2 -->
<tr><div class="form-group">
<td>   <label class="col-md-4 control-label" for="pre2_enable">Enable Pre 2</label></td>
<td>  <div class="col-md-4">  <div class="checkbox">
    <label for="pre1_enable-0">
      <input type="checkbox" name="pre2_check" id="pre2_check" value="1" onclick="showhide('pre2')">
    </label>	</div>  </div></div></td>
</tr>

<!-- Textarea pre   2 -->
<tr style="display:none" id="pre2_tr"><div class="form-group">
<td>   <label class="col-md-4 control-label" for="pre2">Pre 2 HTML</label>
<br><button  type=button onclick="loaddata('pre2','pre2_text')">Load Sample Data</button></td>
<td>  <div class="col-md-4">                    
    <textarea  class="form-control" id="pre2_text" name="pre2_text"  rows="20" cols="100"></textarea>
  </div></div></td></tr>


  
  <!-- Checkbox pre  3 -->
  <tr><div class="form-group">
  <td>   <label class="col-md-4 control-label" for="pre3_enable">Enable Pre 3</label></td>
  <td>  <div class="col-md-4">  <div class="checkbox">
      <label for="pre1_enable-0">
        <input type="checkbox" name="pre3_check" id="pre3_check" value="1" onclick="showhide('pre3')">
      </label>	</div>  </div></div></td>
  </tr>

  <!-- Textarea pre   3 -->
  <tr style="display:none" id="pre3_tr"><div class="form-group">
  <td>   <label class="col-md-4 control-label" for="pre2">Pre 3 HTML</label></td>
  <td>  <div class="col-md-4">                    
      <textarea  class="form-control" id="pre3_text" name="pre3_text"  rows="20" cols="100"></textarea>
    </div></div></td></tr>

    
    <!-- SECTION FOR POST HTML-->

    <!-- Checkbox POST -->
    <tr><div class="form-group">
    <td> 
      <label class="col-md-4 control-label" for="post1_enable">Enable Post 1</label></td>
    <td>  <div class="col-md-4">
      <div class="checkbox">
        <label for="post1_enable-0">
          <input type="checkbox" name="post1_check" id="post1_check" value="1" onclick="showhide('post1')">
          
        </label>
    	</div>
      </div>
    </div></td>
    </tr>

    <!-- Textarea POST 1-->
    <tr style="display:none" id="post1_tr"><div class="form-group">
    <td> 
      <label class="col-md-4 control-label" for="post1">Post 1 HTML</label>
    <br><button  type=button onclick="loaddata('post1','post1_text')">Load Sample Data</button></td>
    <td>  <div class="col-md-4">                     
        <textarea  class="form-control" id="post1_text" name="post1_text"  rows="20" cols="100"></textarea>
      </div>
    </div></td>
    </tr>



    <!-- Checkbox POST 2 -->
    <tr><div class="form-group">
    <td>   <label class="col-md-4 control-label" for="post2_enable">Enable Post 2</label></td>
    <td>  <div class="col-md-4">  <div class="checkbox">
        <label for="post1_enable-0">
          <input type="checkbox" name="post2_check" id="post2_check" value="1" onclick="showhide('post2')">
        </label>	</div>  </div></div></td>
    </tr>

    <!-- Textarea POST   2 -->
    <tr style="display:none" id="post2_tr"><div class="form-group">
    <td>   <label class="col-md-4 control-label" for="post2">Post 2 HTML</label></td>
    <td>  <div class="col-md-4">                    
        <textarea  class="form-control" id="post2_text" name="post2_text"  rows="20" cols="100"></textarea>
      </div></div></td></tr>


      
      <!-- Checkbox POST  3 -->
      <tr><div class="form-group">
      <td>   <label class="col-md-4 control-label" for="post3_enable">Enable Post 3</label></td>
      <td>  <div class="col-md-4">  <div class="checkbox">
          <label for="post1_enable-0">
            <input type="checkbox" name="post3_check" id="post3_check" value="1" onclick="showhide('post3')">
          </label>	</div>  </div></div></td>
      </tr>

      <!-- Textarea POST   3 -->
      <tr style="display:none" id="post3_tr"><div class="form-group">
      <td>   <label class="col-md-4 control-label" for="post2">Post 3 HTML</label></td>
      <td>  <div class="col-md-4">                    
          <textarea  class="form-control" id="post3_text" name="post3_text"  rows="20" cols="100"></textarea>
        </div></div></td></tr>




<!-- Button -->
<tr><div class="form-group">
<td> 
  
  <div class="col-md-4">
    <button id="submit" name="submit" class="btn btn-primary">Submit</button>
  </div>
</div>
</td>
</tr>
</table>
</fieldset>
</form>

Keeping the default values,<br>
and loading sample data for <br>
<ul>
<li>Contacts csv</li>
<li>Notes<br>
</li>
<li>Pre1</li>
<li>Pre2</li>
<li>post2</li>
</ul>
<p>You will end up with <a href="sample-directory.pdf">THIS</a> pdf<br>
</p>

<!--            DIRECTIONS                 -->

<div id="directions" align="left">
<h1>&nbsp;&nbsp; Directions</h1>
<p><b>Why I made this:</b><br>
Printing phone directories is a pain. I got pulled into helping<br>
with my churches phone directory printing. It was a word<br>
doc and when a new name was added, names after it had<br>
to be shifted. This was painful.<br>
</p>
<p><b>Pages:</b><br>
The assumption of this utility is that you want to fold pieces
of paper<br>
in half, and staple them together to make a booklet. You may
want<br>
a page or two prior to your directory, like a cover page, and
maybe<br>
some church info, or birthdays, or whatever. And you may want
some <br>
pages at the end for a back cover, or anything else. You may
also end<br>
up with extra space, since each piece of paper is 4 folded
pages. That's <br>
where the notes.html is used if necessary.<br>
</p>
<p><b>Page Printing Layout:<br>
</b>Let's assume you had a cover page (pre1), and a inside back
cover<br>
page (post1), and a back cover(post2), and 6 pages of entries.
You would<br>
get a PDF that was 6 pages long, and when printed double sided
(flipped<br>
on long edge), you'd get 3 pieces of paper with pages layed out
like below:<br>
</p>
<table width="100%" border="1" cellspacing="2" cellpadding="2">
<tbody>
<tr>
<td valign="top" align="center">Piece of paper 1<br>
<table width="100%" border="1" cellspacing="2"
cellpadding="2">
<tbody>
<tr>
<td valign="top">Page 12<br>
(back cover)<br>
</td>
<td valign="top">Page 1 <br>
front cover<br>
(pre1)<br>
</td>
</tr>
<tr>
<td valign="top">page 2<br>
(entries)<br>
</td>
<td valign="top">page 11<br>
inside back cover<br>
&nbsp;(post2)<br>
</td>
</tr>
</tbody>
</table>
<br>
</td>
<td valign="top">Piece of paper 2<br>
<table width="100%" border="1" cellspacing="2"
cellpadding="2">
<tbody>
<tr>
<td valign="top">page 10<br>
(notes)<br>
</td>
<td valign="top">page 3<br>
(entries)<br>
</td>
</tr>
<tr>
<td valign="top">page 4 <br>
(entries)<br>
</td>
<td valign="top">page 9<br>
(notes)<br>
</td>
</tr>
</tbody>
</table>
<br>
</td>
<td valign="top">Piece of paper 3<br>
<table width="100%" border="1" cellspacing="2"
cellpadding="2">
<tbody>
<tr>
<td valign="top">page 8<br>
&nbsp;(notes)<br>
</td>
<td valign="top">page 5 <br>
(entries)<br>
</td>
</tr>
<tr>
<td valign="top">page 6<br>
&nbsp;(entries)<br>
</td>
<td valign="top">page 7<br>
&nbsp;(entries)</td>
</tr>
</tbody>
</table>
<br>
</td>
</tr>
</tbody>
</table>
<p><br>
</p>
<p><b>How to prep your data:</b><br>
Just put it all into a CSV file. this can be done from Excel,<br>
or a google sheet. Each row needs to be an entry, and each<br>
cell will be a row in that entries text in the directory. Empty
<br>
cells are ignored, and the first row is ignored since it is<br>
normally headers<br>
</p>
<p><b>Example:<br>
</b>The following CSV:<b><br>
</b></p>
<table border="1" cellspacing="2" cellpadding="2">
<tbody>
<tr>
<td valign="top">Name<br>
</td>
<td valign="top">Address<br>
</td>
<td valign="top">Home Phone<br>
</td>
<td valign="top">Cell Phone<br>
</td>
<td valign="top">Email<br>
</td>
</tr>
<tr>
<td valign="top">Smith, John &amp; Jen<br>
</td>
<td valign="top">2 Main St . East Cupcake<br>
</td>
<td valign="top"><br>
</td>
<td valign="top">555-888-9999<br>
</td>
<td valign="top"><br>
</td>
</tr>
<tr>
<td valign="top">Jones, Jerry &amp; Joan<br>
</td>
<td valign="top">5 Elm Ave, West Somewhere<br>
</td>
<td valign="top">555-777-5252<br>
</td>
<td valign="top"><br>
</td>
<td valign="top">joneseses@gmail.com<br>
</td>
</tr>
<tr>
<td valign="top">Franklin, Jim &amp; Jan<br>
</td>
<td valign="top">12 Side Ave, Centerville<br>
</td>
<td valign="top"><br>
</td>
<td valign="top">555-444-3232<br>
</td>
<td valign="top"><br>
</td>
</tr>
</tbody>
</table>
<p><b>Would give the following entries:</b><br>
Smith, John &amp; Jen<br>
2 Main St . East Cupcake<br>
555-888-9999<br>
</p>
<p>Jones, Jerry &amp; Joan<br>
5 Elm Ave, West Somewhere<br>
555-777-5252<br>
joneseses@gmail.com<br>
</p>
<p>Franklin, Jim &amp; Jan<br>
12 Side Ave, Centerville<br>
555-444-3232<br>
</p>
<p><b>Troubleshooting:</b><br>
</p>
<ul>
<li>Your picture is not showing</li>
<ul>
<li>The PDF library I'm using doesn't seem to like if you set
the picture size to a percentage. Change your picture width
to a pixel value.<br>
</li>
</ul>
</ul>
<p>Please let me know if you use this, and any trouble you have
using/understanding<br>
and what would make this easier to use, email me at
support@yourtechguys.info<br>
Source code is available <a href="https://github.com/cdtoews/directorymaker">here</a><br>
</p>
</div>





<script>

showhide("notes");
showhide("pre1");
showhide("pre2");
showhide("pre3");
showhide("post1");
showhide("post2");
showhide("post3");

</script>
</body>
</html>
