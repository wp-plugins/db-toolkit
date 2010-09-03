<?php
	switch($Types[1]){
		case 'phpcodeblock':
			$Out .= '<pre name="code" class="php">'.$Data[$Field].'</pre>';
			$_SESSION['dataform']['OutScripts'] .= "
				dp.SyntaxHighlighter.ClipboardSwf = 'system/dais/plugins/data_form/fieldtypes/text/scripts/clipboard.swf';
				dp.SyntaxHighlighter.HighlightAll('code');
			";
			break;
		case 'textarealarge':
			$Out .= nl2br($Data[$Field]);
			break;
		case 'url':
			$Out .= '<a href="'.$Data[$Field].'" target="_blank" >'.$Data[$Field].'</a>';
			break;
		default:
			$Out .= nl2br($Data[$Field]);
			break;
	};
//$Out = nl2br($Data[$Field]);
?>