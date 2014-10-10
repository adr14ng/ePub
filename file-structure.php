<?php


function create_book() {
	list($content, $options) = default_values();
	
	$files = print_content($content);
	
	//create mimetype
	mimetype();
	//create container.xml
	container();
	//create content.opf
	contentOPF($files, $options);
	//create toc.ncx
	tocNCX($files, $options);
}

function default_values() {
	$content = array(
		'cover' => array( 'title' => 'Univeristy Catalog'),
		'toc' => array('title' => "Table of Contents"),
		'csun' => array(
					'title' => "CSUN",
					'pages' => array(
							0 	=> 29206,
							1 	=> 29208,
							//2 => , academic calendar
							3 	=> 27367,
							4 	=> 27377,
							5 	=> 27378,
							6 	=> 27382,
							7 	=> 29210,
							8 	=> 29212,
							9 	=> 29221,
							10 	=> 29223,
							11 	=> 35172
						),
				),
		'undergrad' => array(
					'title' => 'Undergraduate Programs',
					'pages' => array(
							0 	=> 29215,
							1 	=> 32736,
							2 	=> 29227,
							3 	=> 34971,
							4 	=> 34967,
							5 	=> 29225,
						),
					'policies' => true,
					'proglist' => true
				),
		'student-services' => array(
					'title' => 'Student Services',
					'pages' => array(
							0 	=> 29219
						),
				),
		'special-programs' => array(
					'title' => 'Special Programs',
					'pages' => array(
							0 	=> 29229
						),
				),
		'gened' => array(
					'title'	=> 'General Education',
					'pages' => array(
							0 	=> 28561,
							1 	=> 29162,
							2 	=> 29160
						),
					'category' => true,
					'upper' => true,
					'ic' => true
				),
		'grad' => array(
					'title' => 'Research and Graduate Studies',
					'pages' => array(
							0 	=> 186
						),
					'proglist' => true,
				),
		'credential' => array(
					'title' => 'Credential Office',
					'pages' => array(
							0 	=> 28825
						),
					'proglist' => true,
				),
		'study' => array(
					'title' => 'Courses of Study',
					'listTitle' => 'Colleges, Departments and Programs',
					'categories' => array(
							'acct' 	=> 17,
							'afric' => 3,
							'afrs' 	=> 60,
							'ais' 	=> 4,
							'anth' 	=> 5,
							'art' 	=> 6,
							'aas' 	=> 7,
							'asian' => 8,
							'at' 	=> 452,
							'biol' 	=> 9,
							'gbus' 	=> 25,
							'blaw' 	=> 18,
							'calif' => 10,
							'cas' 	=> 12,
							'chem' 	=> 11,
							'chs' 	=> 13,
							'cadv' 	=> 31,
							'ctva' 	=> 32,
							'cecm' 	=> 50,
							'cd' 	=> 37,
							'coms' 	=> 36,
							'comp' 	=> 38,
							'bus' 	=> 457,	//college
							'deaf' 	=> 40,
							'econ' 	=> 23,
							'elps' 	=> 41,
							'epc' 	=> 42,
							'ece' 	=> 43,
							'eed' 	=> 44,
							'cecs' 	=> 100,	//college
							'engl' 	=> 101,
							'eoh' 	=> 102,
							'fcs' 	=> 47,
							'fin' 	=> 24,
							'gws' 	=> 88,
							'geog' 	=> 90,
							'geol' 	=> 103,
							'hhd' 	=> 53,	//college
							'hsci'	=> 55,
							'hist' 	=> 64,
							'humsex'=> 428,
							'coh' 	=> 429,	//college
							'huma' 	=> 489
							'js' 	=> 94,
							'jour' 	=> 95,
							'kin' 	=> 105,
							'lrs' 	=> 107,
							'ling' 	=> 108,
							'mgt' 	=> 27,
							'msem' 	=> 69,
							'mkt' 	=> 28,
							'math' 	=> 70,
							'educ' 	=> 167,	//college
							'meis' 	=> 74,
							'amc' 	=> 168,	//college
							'me' 	=> 73,
							'mcll' 	=> 75,
							'mus' 	=> 83,
							'nurs' 	=> 56,
							'phil' 	=> 61,
							'pt' 	=> 96,
							'phys' 	=> 120,
							'pols' 	=> 63,
							'psy' 	=> 121,
							'mpa' 	=> 122,
							'qs' 	=> 123,
							'rtm' 	=> 124,
							'rs' 	=> 91,
							'csm' 	=> 169,
							'sed' 	=> 125,
							'csbs' 	=> 166,	//college
							'swrk' 	=> 132,
							'soc' 	=> 126,
							'sped' 	=> 127,
							'som' 	=> 29,
							'th' 	=> 129,
							'univ' 	=> 130,	//need special case
							'urbs' 	=> 131,
						),
					'policies' => true
				),
		'policies' => array(
					'title' => 'Policies',
					'categories' => array(
							'enrollment-regulations'			=> 134,
							'fees' 								=> 139,
							'privacy-and-student-information'	=> 157,
							'nondiscrimination-policy' 			=> 158,
							'student-conduct' 					=> 159,
							'admission-procedures-and-policies' => 160,
							'university-regulations' 			=> 161,
							'other-policies' 					=> 162
						),
				),
		'faculty' => array('title' => 'Faculty and Administration'),
		'emeriti'  => array('title' => "Emeriti"),
	);
		
	$options = array(
		'title' => 'CSUN Catalog',
		'creator' => 'Undergraduate Studies',
		'language' => 'en-US',
		'rights' => '',
		'publisher' => 'California State Univeristy, Northridge',
		'bookid' => '20142015CSUN'
	);
		
	return array($content, $options);
}

function contentOPF($contents, $options) {
	$dir = './book/OEBPS/';
	ob_start();
?>
	<?xml version="1.0" encoding="UTF-8"??>
	<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="BookID" version="2.0" >
		<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">
			<dc:title><?php echo $options['title']; ?></dc:title>
			<dc:creator opf:role="aut"><?php echo $options['creator']; ?></dc:creator>
			<dc:language><?php echo $options['language']; ?></dc:language>
			<dc:rights><?php echo $options['rights']; ?></dc:rights>
			<dc:publisher><?php echo $options['publisher']; ?></dc:publisher>
			<dc:identifier id="BookID" opf:scheme="UUID"><?php echo $options['bookid']; ?></dc:identifier>
		</metadata>
		<manifest>
			<item id="ncx" href="toc.ncx" media-type="application/x-dtbncx+xml" />
			<item id="style" href="style.css" media-type="text/css" />
			<item id="pagetemplate" href="page-template.xpgt" media-type="application/vnd.adobe-page-template+xml" />
			
		<?php foreach($contents as $section) : ?>
			<item id="<?php echo $section['file']; ?>" href="<?php echo $section['file']; ?>.xhtml" media-type="application/xhtml+xml" />
			
			<?php if($section['subpages']): foreach($section['subpages'] as $subsection) : ?>
				<item id="<?php echo $subsection['file']; ?>" href="<?php echo $subsection['file']; ?>.xhtml" media-type="application/xhtml+xml" />
			<?php endforeach; endif; ?>
		<?php endforeach; ?>
			
			<item id="imgl" href="images/sample.png" media-type="image/png" /><!-- Update this sometime -->
		</manifest>
		<spine toc="ncx">
		<?php foreach($contents as $section) : ?>
			<itemref idref="<?php echo $section['file']; ?>" />
			
			<?php if($section['subpages']): foreach($section['subpages'] as $subsection): ?>
				<itemref idref="<?php echo $subsection['file']; ?>" />
			<?php endforeach; endif; ?>
			
		<?php endforeach; ?>
		</spine>
	</package>
<?php
	$content = ob_get_contents();		//save output
	ob_end_clean();						//discard buffer
		
	$f = fopen($dir.'content.opf', "w");
	fwrite($f, $content);
	fclose($f);
}

function mimetype() {
	$dir = './book/';
	
	$f = fopen($dir.'mimetype', "w");
	fwrite($f, 'application/epub+zip');
	fclose($f);
}

function container() {
	$dir = './book/META-INF/';
	ob_start();
?>
	<?xml version="1.0"?>
	<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">
		<rootfiles>
			<rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>
	   </rootfiles>
	</container>
<?php
	$content = ob_get_contents();		//save output
	ob_end_clean();						//discard buffer
		
	$f = fopen($dir.'container.xml', "w");
	fwrite($f, $content);
	fclose($f);
}

function tocNCX($contents, $options) {
	$dir = './book/OEBPS/';
	$count = 0;
	ob_start();

?>
	<?xml version="1.0" encoding="UTF-8"?>
	<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">

		<head>
			<meta name="dtb:uid" content="<?php echo $options['bookid']; ?>"/>
			<meta name="dtb:depth" content="1"/>
			<meta name="dtb:totalPageCount" content="0"/>
			<meta name="dtb:maxPageNumber" content="0"/>
		</head>

		<docTitle>
			<text><?php echo $options['title']; ?></text>
		</docTitle>

		<navMap>
		<?php foreach($contents as $section) : 
			$count++; ?>
			<navPoint id="<?php echo $section['file']; ?>" playOrder="<?php echo $count; ?>">
				<navLabel><text><?php echo $section['title']; ?></text></navLabel>
				<content src="<?php echo $section['file']; ?>.xhtml"/>
				
				<?php if(issest($section['subpages'])): foreach($section['subpages'] as $subsection):
					$count++; ?>
					<navPoint id="<?php echo $subsection['file']; ?>" playOrder="<?php echo $count; ?>">
						<navLabel><text><?php echo $subsection['title']; ?></text></navLabel>
						<content src="<?php echo $subsection['file']; ?>.xhtml"/>
					</navPoint>
				<?php endforeach; endif; ?>
				
			</navPoint>
		<?php endforeach; ?>
		</navMap>
	</ncx>
<?php
	$content = ob_get_contents();		//save output
	ob_end_clean();						//discard buffer
		
	$f = fopen($dir.'toc.ncx', "w");
	fwrite($f, $content);
	fclose($f);
}


