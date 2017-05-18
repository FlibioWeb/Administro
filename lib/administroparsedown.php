<?php

class AdministroParsedown extends Parsedown {

    var $administro;

    public function __construct($administro) {
        $this->administro = $administro;
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
        if(isset($this->administro->currentPage)) {
            $prefix = $this->administro->baseDir . 'file/' . $this->administro->currentPage['id'] . '/';
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

}
