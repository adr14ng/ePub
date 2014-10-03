<?php

function print_content() {
	$csun = array('presidents-message', 'introduction', 'academic calendar', 'the-california-state-university', 
		'office-of-the-chancellor', 'campuses-the-california-state-university', 'the-california-state-university-international-programs',
		'university-administration', 'colleges-degrees-and-accreditation', 'library', 'information-technology-it', 'tseng')

	$undergrad_progs = array('undergraduate-admission-requirements', 'undergraduate-services', 'student-services-centerseop-satellites',
		'pre-professional-advisement-pre-law', 'pre-professional-advisement', 'academic-advisement');
		
	$student_services = array('student-services');

	$special_programs = array('special-programs');

	$file_names = array();


	//csun*
	ob_start();
	
	print_header('CSUN')
	print_pages($csun)
	print_footer();
	
	$content = ob_get_contents();
	$f = fopen($file_names[0].'.xhtml', "w");
	fwrite($f, $content);
	fclose($f);
	
	//undergrad_progs*
	ob_start();
	
	print_header('Undergraduate Programs')
	print_ugp($undergrad_progs)
	print_footer();
	
	$content = ob_get_contents();
	$f = fopen($file_names[1].'.xhtml', "w");
	fwrite($f, $content);
	fclose($f);

	//student_services*
	ob_start();
	
	print_header('Student Services')
	print_pages($student_services)
	print_footer();
	
	$content = ob_get_contents();
	$f = fopen($file_names[2].'.xhtml', "w");
	fwrite($f, $content);
	fclose($f);

	//special_programs*
	ob_start();
	
	print_header('Special Programs')
	print_pages($special_programs)
	print_footer();
	
	$content = ob_get_contents();
	$f = fopen($file_names[3].'.xhtml', "w");
	fwrite($f, $content);
	fclose($f);

	//general education*
		//info*
		//courses*

	//grad_studies*

	//courses of study
		//course of study*
		//college*
		//course policies*

	//policies*

	//faculty*

	//emeriti*
	
	return $file_names;
}

function print_header($title){ ?>
<?xml version='1.0' encoding='utf-8'?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>
			<?php echo $title; ?>
		</title>
	</head>
	<body>
	<h1><?php echo $title; ?></h1>
<?php }

function print_footer() { ?>

	</body>
</html>
<?php }

	