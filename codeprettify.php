<?php

/**
 * @package codeprettify
 *
 * @copyright (C) 2022 Tech Space Hub.
 * @license GNU General Public License version 3 or later
 */

defined("_JEXEC") or die("Restricted access");

jimport("joomla.plugin.plugin");

class plgContentcodeprettify extends JPlugin
{
	function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}

	function onContentPrepare($context, $article, $params, $page = 0)
	{
		plgContentcodeprettify::onPrepareContent($article, $params, 0);
	}

	function onPrepareContent(&$article, &$params, $limitstart)
	{

		$regexp = "#{codeprettify}(.*?){\/codeprettify}#is";

		if ($this->params->get("enabled", "1") == "0") {
			$article->text = preg_replace($regexp, "", $article->text);
		} else {
			$artParams = array(
				"bcolor"                  =>      "#00b4cc"
			);

			$pluginParams = $this->getPluginParams($artParams);

			if (preg_match_all($regexp, $article->text, $matches, PREG_SET_ORDER) > 0) {
				$this->addResources();
				$i = 0;
				foreach ($matches as $match) {
					if (@$match) {
						$adjustedMatch = $this->adjustMatch($match[1]);
						$adjustedMatch = parse_str($adjustedMatch, $clientParams);
						$finalParams = $this->getClientParams($clientParams, $pluginParams);
						$article->text = preg_replace($regexp, $this->getCanvasCode($i, $finalParams, $match[1]), $article->text, 1);
						$i++;
						$finalParams = $pluginParams;
					}
				}
			}
		}
	}

	function adjustMatch($text)
	{
		$adjustedMatch = ltrim($text);
		$adjustedMatch = rtrim($adjustedMatch);
		$adjustedMatch = str_replace('&', '|||', $adjustedMatch);
		$adjustedMatch = str_replace(' =', '=', $adjustedMatch);
		$adjustedMatch = str_replace('= ', '=', $adjustedMatch);
		$adjustedMatch = str_replace('="', '=', $adjustedMatch);
		$adjustedMatch = str_replace('= ', '=', $adjustedMatch);
		$adjustedMatch = str_replace('" ', '"', $adjustedMatch);
		$adjustedMatch = str_replace(' "', '"', $adjustedMatch);
		$adjustedMatch = str_replace('"', '&', $adjustedMatch);
		$adjustedMatch = str_replace(' ', '+', $adjustedMatch);
		$adjustedMatch = rtrim($adjustedMatch, '&');
		return $adjustedMatch;
	}

	function getCanvasCode($id, $finalParams, $article)
	{
		$showlinenumbers = $this->params->get("showlinenumbers", "1");
		if($showlinenumbers){ $showlinenumbers='linenums'; }
		$codeprettify = '<pre class="prettyprint '.$showlinenumbers.'" id="quine">'.$article.'</pre>';

		return $codeprettify;
	}
	function addResources()
	{
		$codeprettifytheme = $this->params->get("codeprettifytheme", "prettify.css");
		$document = JFactory::getDocument();
		$document->addStyleSheet( JURI::root() . 'plugins/content/codeprettify/src/'.$codeprettifytheme );		
		$document->addStyleSheet( JURI::root() . 'plugins/content/codeprettify/src/custom.style.css' );
		$document->addScript( JURI::root() . 'plugins/content/codeprettify/src/run_prettify.js' );
	}

	function onAfterRender()
	{
		$app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			return true;
		}
		$buffer = JFactory::getApplication()->getBody();
		$insert = '<script src="'.JURI::root().'plugins/content/codeprettify/src/custom.script.js"></script>';
		$buffer= str_ireplace('</body>',$insert.'</body>',$buffer);
		JFactory::getApplication()->setBody($buffer);

		return true;
	}

	function getPluginParams($params)
	{
		$pluginParams = array();
		foreach ($params as $key => $value) {
			$pluginParams[$key] = $value;
		}
		return $pluginParams;
	}

	function getClientParams($params, $finalParams)
	{
		foreach ($params as $key => $value) {
			if (@$params[$key]) $finalParams[$key] = htmlspecialchars($params[$key], ENT_QUOTES);
		}
		return $finalParams;
	}
}
