this is to create Phone directories

To use:
* have a subfolder named 'content'
* put a csv with data named 'directory.csv'
* first row is ignored, assumed to be headers
* Every non-empty cell in row is added as a line for entry 
* to have pages of html before entries you can have content/pre1.html, content/pre2.html, etc...
* if you want pages of html after engtries you can have content/post1.html, content/post2.html, etc..
* You need to have an html file content/notes.html 
** This is what will fill pages after entries and before postX.html pages 

Some things to notes
* in html pages, don't put images to percentages, TCPDF seems to not like that
