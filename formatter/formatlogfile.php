<?php
/**
 * Created by PhpStorm.
 * User: BrianHenryIE
 * Date: 9/11/18
 * Time: 4:26 PM
 */


// takes .xmpp.log file as input, outputs .xmpp.log.html

// deviceconsole time isn't as specific as Console

$filenames = scandir('.');


$regex = "/(?P<date>.*?\d\d:\d\d:\d\d) (?P<process>.*?)\(XMPPFrameworkLogger\.dylib\).*?: XMPPFramework (?P<action>\w.*?):(?P<data>.*)/"; //gm


foreach($filenames as $filename) {

	// TODO: don't overwrite
	if(strpos($filename, '.xmpp.log') !== false
	   && strpos($filename, '.xmpp.log.html') === false
	   && strpos($filename, '.xmpp.log.json') === false) {

		$fileContents = file($filename);

		$dataArray = array();



		foreach($fileContents as $line) {

			$matches = null;

			$lines = array();

			if(strlen($line) < 1100) {

				$lines[] = $line;

			} else {

				// NSLog can only log 1024 characters (plus time, process?)

				print("Line beginning:    " . substr($line, 0, 200) . "    :is impossibly long. Edit the log file to fix.\n");

				// TODO. Address this automatically.

			}

			foreach ($lines as $line) {

				if ( preg_match( $regex, $line, $matches ) ) {

					$parsedLine = array(
						'date'    => $matches['date'],
						'process' => $matches['process'],
						'action'  => $matches['action'],
						'data'    => trim( $matches['data'] )
					);

					if ( $matches['action'] === 'receive-partial-contd' || $matches['action'] === 'send-partial-contd' ) {

						$lastValue          = array_pop( $dataArray );
						$parsedLine['data'] = $lastValue['data'] . trim( $matches['data'] );

					}

					if ( $matches['action'] === 'receive-partial-start' || $matches['action'] === 'receive-partial-contd' ) {
						$parsedLine['action'] = 'receive';
					}

					if ( $matches['action'] === 'send-partial-start' || $matches['action'] === 'send-partial-contd' ) {
						$parsedLine['action'] = 'send';
					}

					$dataArray[] = $parsedLine;
				}
			}
		}

//		$filename.'.json';
		// print( json_encode( $dataArray ) );

		$html  = '<!DOCTYPE html>';
		$html .= '<html lang="en">';
		$html .= '<head>';
		$html .= '<meta charset="utf-8"/>';

		$html .= '<link rel="stylesheet" type="text/css" href="style.css">';

		$html .= '<div class="xmpp-stream">';

		foreach($dataArray as $message) {

			$action = $message['action'];
			$date = $message['date'];
			$data = $message['data'];


			$data = str_replace( '><', ">\n<", $data);

			// Split on \n

			$indentString = '   ';
			$indentCount = 0;

			$xmlLines = explode(PHP_EOL, $data);

			$indentedXmlLines = array();

			foreach($xmlLines as $xmlLine) {

				$xmlLine = trim($xmlLine);



				// <json>{"key":"value"}</json>

				if( substr( $xmlLine, 0, 6 ) === "<json>" && (substr( $xmlLine, -7 ) === "</json>") ){

					$indentedXmlLines[] = str_repeat($indentString, $indentCount) . '<json>';

					$jsonString = substr($xmlLine,6, strlen($xmlLine) - 6 - 7);

					$jsonString = json_encode(json_decode($jsonString), JSON_PRETTY_PRINT );

					$jsonLines = explode(PHP_EOL, $jsonString);

					foreach ($jsonLines as $jsonLine){
						$indentedXmlLines[] = str_repeat($indentString, $indentCount) . $jsonLine;
					}

					$indentedXmlLines[] = str_repeat($indentString, $indentCount) . '</json>';

					continue;
				}



				// <?xml version='1.0' encoding='UTF-8'>
				if( substr( $xmlLine, 0, 2 ) === "<?") {
					$indentedXmlLines[] = $xmlLine;
					continue;
				}

				if( substr( $xmlLine, 0, 2 ) === "</"){

					$indentCount--;
					$indentCount = max($indentCount,0);

					$indentedXmlLines[] = str_repeat($indentString, $indentCount) . $xmlLine;

					continue;
				}

				// Looks like  <stream:features>
				if(substr_count($xmlLine, '<') == 1 && substr_count($xmlLine, '>') == 1 && substr_count($xmlLine, '/>') == 0) {
					// post increment indentation by 1
					// but indent as usual
					$indentedXmlLines[] = str_repeat($indentString, $indentCount) . $xmlLine;
					$indentCount++;
					continue;

				}

				// Looks like <mechanism>DIGEST-MD5</mechanism>
				if(substr_count($xmlLine, '</') == 1 && substr_count($xmlLine, '<') == 2 && substr_count($xmlLine, '>') == 2) {
					// don't change the indentation

					// but indent as usual

					$indentedXmlLines[] = str_repeat($indentString, $indentCount) . $xmlLine;

					continue;

				}

				$indentedXmlLines[] = str_repeat($indentString, $indentCount) . $xmlLine;

			}

			$data = implode( "\n", $indentedXmlLines);

//			$data = str_replace( '><', ">\n<", $message['data']);

			// Join on \n

			// $html .= "<div class=\"message $action\"><div class=\"date\">$date</div><div class=\"xmpp-data\"><pre lang=\"xml\">".htmlentities($data)."</pre></div></div>\n";
//			$html .= "<div class=\"message $action\"><div class=\"date\">$date</div><div class=\"xmpp-data\">".htmlentities($data)."</div></div>\n";


//			$data = htmlentities($data);
			$data = "<xmp>$data</xmp>";


			$html .= "<div class=\"message\">";
			$html .= "<div class=\"$action\">";
			$html .= "<div class=\"chat-border\">";
			$html .= "<div class=\"chat\">";
			$html .= "<div class=\"date\">$date</div>";
			$html .= "<div class=\"xmpp-data\">";
			$html .= "<code class=\"prettyprint lang-xml\">".$data."</code>";
			$html .= "</div></div></div></div></div>\n";
		}

		$html .= '</div>';


//		$html .= '<script src="XmlBeautify.js"></script>';

//		$html .= '<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js?autorun=false"></script>';

		$html .= '<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js?autorun=true"></script>';

//		$html .= '<script src="script.js"></script>';


		file_put_contents($filename.'.html', $html);

	}
}

