<?php

class AdministroParsedown extends Parsedown {

    var $administro;
    var $pageId;

    public function __construct($administro, $pageId) {
        $this->administro = $administro;
        $this->pageId = $pageId;

        $this->InlineTypes['{'][]= 'Dropdown';
        $this->inlineMarkerList .= '{';
    }

    protected function inlineDropdown($excerpt)
    {
        if (preg_match('/^{dropdown}{(.+)}/', $excerpt['text'], $matches))
        {
            $dropdown = "<form action='".$this->administro->baseDir."form/dropdown' method='post' class='file-dropdown'><select name='file'>";
            foreach(explode(",", $matches[1]) as $option) {
                $ddv = trim($option);
                $d = explode(":", $ddv);
                if(count($d) !== 2) continue;
                $dropdown.= "<option value='".$d[1]."'>".$d[0]."</option>";
            }
            $dropdown .= "</select><input type='hidden' name='page' value='".$this->pageId."'>";
            $dropdown .= "<input type='submit' value='View'></form>";
            return array(
                // How many characters to advance the Parsedown's
                // cursor after being done processing this tag.
                'extent' => strlen($matches[0]),
                'element' => array(
                    'name' => 'span',
                    'text' => $dropdown
                ),
            );
        }
    }

    protected function inlineImage($Excerpt) {
        if ( ! isset($Excerpt['text'][1]) or $Excerpt['text'][1] !== '[') {
            return;
        }
        $Excerpt['text']= substr($Excerpt['text'], 1);
        $Link = $this->inlineLink($Excerpt);
        if ($Link === null) {
            return;
        }

        $prefix = '';
        if($this->pageId !== false) {
            $prefix = $this->administro->baseDir . 'file/' . $this->pageId . '/';
        }

        $Inline = array(
            'extent' => $Link['extent'] + 1,
            'element' => array(
                'name' => 'img',
                'attributes' => array(
                    'src' => $prefix.$Link['element']['attributes']['href'],
                    'alt' => $Link['element']['text'],
                ),
            ),
        );
        $Inline['element']['attributes'] += $Link['element']['attributes'];
        unset($Inline['element']['attributes']['href']);
        return $Inline;
    }

    protected function inlineLink($Excerpt) {
        $Element = array(
            'name' => 'a',
            'handler' => 'line',
            'text' => null,
            'attributes' => array(
                'href' => null,
                'title' => null,
            ),
        );
        $extent = 0;
        $remainder = $Excerpt['text'];
        if (preg_match('/\[((?:[^][]|(?R))*)\]/', $remainder, $matches))
        {
            $Element['text'] = $matches[1];
            $extent += strlen($matches[0]);
            $remainder = substr($remainder, $extent);
        }
        else
        {
            return;
        }
        if (preg_match('/^[(]((?:[^ ()]|[(][^ )]+[)])+)(?:[ ]+("[^"]*"|\'[^\']*\'))?[)]/', $remainder, $matches))
        {
            $Element['attributes']['href'] = $matches[1];
            if (isset($matches[2]))
            {
                $Element['attributes']['title'] = substr($matches[2], 1, - 1);
            }
            $extent += strlen($matches[0]);
        }
        else
        {
            if (preg_match('/^\s*\[(.*?)\]/', $remainder, $matches))
            {
                $definition = strlen($matches[1]) ? $matches[1] : $Element['text'];
                $definition = strtolower($definition);
                $extent += strlen($matches[0]);
            }
            else
            {
                $definition = strtolower($Element['text']);
            }
            if ( ! isset($this->DefinitionData['Reference'][$definition]))
            {
                return;
            }
            $Definition = $this->DefinitionData['Reference'][$definition];
            $Element['attributes']['href'] = $Definition['url'];
            $Element['attributes']['title'] = $Definition['title'];
        }
        $Element['attributes']['href'] = str_replace(array('&', '<'), array('&amp;', '&lt;'), $Element['attributes']['href']);

        $newLink = $Element['attributes']['href'];
        if(strlen($newLink) >= 1 && substr($newLink, 0, 1) == ";") {
            // Prepend the page file path
            $Element['attributes']['href'] = $this->administro->baseDir . 'file/' . $this->pageId . '/' . substr($newLink, 1);
        }
        if(strlen($newLink) >= 1 && substr($newLink, 0, 1) == ":") {
            // Prepend the relative path
            $Element['attributes']['href'] = $this->administro->baseDir . substr($newLink, 1);
        }

        return array(
            'extent' => $extent,
            'element' => $Element,
        );
    }

}
