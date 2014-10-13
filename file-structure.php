<?php


function create_book($content, $options) {
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
			
			<item id="cover-image" href="images/cover.png" media-type="image/png" /><!-- Update this sometime -->
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


