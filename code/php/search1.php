<?php
require_once("xapian.php");
require_once("parsecsv.php");

## Start of example code.
function search($dbpath, $querystring, $offset = 0, $pagesize = 10)
{
	// offset - defines starting point within result set
	// pagesize - defines number of records to retrieve
	
	// Open the database we're going to search.
	$db = new XapianDatabase($dbpath);

    // Set up a QueryParser with a stemmer and suitable prefixes
    $queryparser = new XapianQueryParser();
    $queryparser->set_stemmer(new XapianStem("english"));
    $queryparser->add_prefix("title", "S");
    $queryparser->add_prefix("description", "XD");
	
    // And parse the query
    $query = $queryparser->parse_query($querystring);

    // Use an Enquire object on the database to run the query
    $enquire = new XapianEnquire($db);
    $enquire->set_query($query);

	// Retrieve the matches and compute start and end points
	$matches = $enquire->get_mset($offset, $pagesize);
	$start = $matches->begin();
	$end = $matches->end();
	$index = 0;

	while (!($start->equals($end)))
	{
		// retrieve the document and its data
		$doc = $start->get_document();
		$fields = json_decode($doc->get_data());
		$position = $offset + $index + 1;
	
		// display the results
		print "{$position}: ".$start->get_docid()." ".$fields->TITLE."\n";

		// increment MSet iterator and our counter
		$start->next();
		$index++;
	}
}
## End of example code.

search($argv[1], $argv[2]);
?>