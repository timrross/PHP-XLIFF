# PHP XLIFF Parser #

A PHP library designed to help with reading and writing XLIFF documents. For more information on the current (as of April 2015) XLIFF specification, see:

http://docs.oasis-open.org/xliff/xliff-core/v2.0/os/xliff-core-v2.0-os.html

## Usage Example ##

```
<?php

require dirname( __DIR__ ) . '/vendor/autoload.php';

echo 'Generating new XLIFF document:' . PHP_EOL;

// create a new top level xliff document element
$xliff = new \Timrross\Xliff\Document();

// set source and target locale ("lang") attributes
$xliff->set_source_locale( 'en_US' );
$xliff->set_target_locale( 'id_ID' );

// create child file, unit, segment, and source elements and set text in the source element
$xliff->file(true)->unit(true)->segment(true)->source(true)->set_text_content( 'Hello world!' );

// reuse the same file, unit, segments but add a new target element; set text in the target element
$xliff->file()->unit()->segment()->target(true)->set_text_content( 'Hola mundo!' );

// convert to XML
$dom = $xliff->to_DOM();
$xml = $dom->saveXML( $dom->documentElement );

echo $xml . PHP_EOL;
```
