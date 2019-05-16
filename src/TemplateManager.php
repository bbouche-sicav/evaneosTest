<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        //var_dump($text);
        $placeHolder = array();
        preg_match_all('[[aA-zZ]*:[aA-zZ]*]', $text, $out);
        foreach( $out as &$dvar ){
            $dvar[0] = str_replace('[','', $dvar[0]);
            $dvar[0] = str_replace(']','', $dvar[0]);
            $tmp = explode(':', $dvar[0]);
            
            if( !isset($placeHolder[$tmp[0]]) ){
                $placeHolder[$tmp[0]] = array();
            }
            array_push($placeHolder[$tmp[0]], $tmp[1]);
        }
        
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        foreach( $data as $className => $object ){
            if( array_key_exists($className, $placeHolder) ){
            // is_a => replace instanceOf since not possible to past a string variable
                $check = (isset($object) and is_a($object, ucfirst($className)) )  ? $object : null;
            // Reflection Class to get properties and replace automaticly placeholder in tpl
                $reflect = new ReflectionClass($check);
                $props   = $reflect->getProperties();

                foreach ($props as $prop) {
                    if( in_array($prop->getName(), $placeHolder[$className]) ){
                        $elementFromRepository = eval( ucfirst($className).'Repository::getInstance()->getById('.$check.'->'.$prop->getName().')');
                    }
                }
            }
        }
        exit;
        
        if ($quote)
        {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);

            if(strpos($text, '[quote:destination_link]') !== false){
                $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            }

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($_quoteFromRepository),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($_quoteFromRepository),
                        $text
                    );
                }
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]',$destinationOfQuote->countryName,$text);
        }

        if (isset($destination))
            $text = str_replace('[quote:destination_link]', $usefulObject->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id, $text);
        else
            $text = str_replace('[quote:destination_link]', '', $text);

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $APPLICATION_CONTEXT->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }
}
