<?php


function create_book($content, $options) {
	global $book_dir;

	//copy mimetype
	copy('./book/mimetype.zip' ,$book_dir.'/mimetype.zip');
	
	//create META-INF
	mkdir($book_dir.'/META-INF', 0775);
	//create OEBPS & images
	mkdir($book_dir.'/OEBPS/images', 0775, true);
	
	//create content
	$files = print_content($content);
	
	//copy style.css
	copy('./book/OEBPS/style.css' ,$book_dir.'/OEBPS/style.css');
	//copy page-template.xpgt
	copy('./book/OEBPS/page-template.xpgt' ,$book_dir.'/OEBPS/page-template.xpgt');
	
	//create container.xml
	container();
	//create content.opf
	contentOPF($files, $options);
	//create toc.ncx
	tocNCX($files, $options);

}

function contentOPF($contents, $options) {
	global $book_dir;
	$dir = $book_dir.'/OEBPS/';
	ob_start();
	echo  '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
	<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="BookID" version="2.0" >
		<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">
			<dc:title><?php echo $options['title']; ?></dc:title>
			<dc:creator opf:role="aut"><?php echo $options['creator']; ?></dc:creator>
			<dc:language><?php echo $options['language']; ?></dc:language>
			<dc:rights><?php echo $options['rights']; ?></dc:rights>
			<dc:publisher><?php echo $options['publisher']; ?></dc:publisher>
			<dc:identifier id="BookID" opf:scheme="UUID"><?php echo $options['bookid']; ?></dc:identifier>
			<meta name="cover" content="cover-image" />
		</metadata>
		<manifest>
			<item id="ncx" href="toc.ncx" media-type="application/x-dtbncx+xml" />
			<item id="style" href="style.css" media-type="text/css" />
			<item id="pagetemplate" href="page-template.xpgt" media-type="application/vnd.adobe-page-template+xml" />
			
		<?php foreach($contents as $section) : ?>
			<item id="<?php echo $section['file']; ?>" href="<?php echo $section['file']; ?>.xhtml" media-type="application/xhtml+xml" />
			
			<?php if(isset($section['subpages'])): foreach($section['subpages'] as $subsection) : ?>
				<item id="<?php echo $subsection['file']; ?>" href="<?php echo $subsection['file']; ?>.xhtml" media-type="application/xhtml+xml" />
			<?php endforeach; endif; ?>
		<?php endforeach; ?>
			
			<item id="cover-image" href="<?php echo $options['cover']; ?>" media-type="<?php echo $options['cover-type']; ?>" /><!-- Update this sometime -->
		</manifest>
		<spine toc="ncx">
		<?php foreach($contents as $section) : ?>
			<itemref idref="<?php echo $section['file']; ?>" />
			
			<?php if(isset($section['subpages'])): foreach($section['subpages'] as $subsection): ?>
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

function container() {
	global $book_dir;
	$dir = $book_dir.'/META-INF/';
	ob_start();
	echo '<?xml version="1.0"?>'."\n";
?>
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
	global $book_dir;
	$dir = $book_dir.'/OEBPS/';
	$count = 0;
	
	ob_start();
	echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
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
			<navPoint id="<?php echo $section['file'].'-'.$count; ?>" playOrder="<?php echo $count; ?>">
				<navLabel><text><?php echo $section['title']; ?></text></navLabel>
				<content src="<?php echo $section['file']; ?>.xhtml"/>
				
				<?php if(isset($section['sublinks'])): foreach($section['sublinks'] as $sublink):
					$count++; ?>
					<navPoint id="<?php echo $sublink['file'].'-'.$count; ?>" playOrder="<?php echo $count; ?>">
						<navLabel><text><?php echo $sublink['title']; ?></text></navLabel>
						<content src="<?php echo $section['file'].'.xhtml#'.$sublink['file']; ?>"/>
						
						<?php if(isset($sublink['sublinks'])): foreach($sublink['sublinks'] as $subsublink):
							$count++; ?>
							<navPoint id="<?php echo $subsublink['file'].'-'.$count; ?>" playOrder="<?php echo $count; ?>">
								<navLabel><text><?php echo $subsublink['title']; ?></text></navLabel>
								<content src="<?php echo $section['file'].'.xhtml#'.$subsublink['file']; ?>"/>
							</navPoint>
						<?php endforeach; endif; ?>
						
					</navPoint>
				<?php endforeach; endif; ?>
				
				<?php if(isset($section['subpages'])): foreach($section['subpages'] as $subsection):
					$count++; ?>
					<navPoint id="<?php echo $subsection['file'].'-'.$count; ?>" playOrder="<?php echo $count; ?>">
						<navLabel><text><?php echo $subsection['title']; ?></text></navLabel>
						<content src="<?php echo $subsection['file']; ?>.xhtml"/>
						
						<?php if(isset($subsection['sublinks'])): foreach($subsection['sublinks'] as $sublink): if(isset($sublink['title'])) :
							$count++; ?>
							<navPoint id="<?php echo $sublink['file'].'-'.$count; ?>" playOrder="<?php echo $count; ?>">
								<navLabel><text><?php echo $sublink['title']; ?></text></navLabel>
								<content src="<?php echo $subsection['file'].'.xhtml#'.$sublink['file']; ?>"/>
							</navPoint>
						<?php endif; endforeach; endif; ?>
						
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


