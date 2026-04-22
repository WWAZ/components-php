<?php

namespace wwaz\Components\Fragment\Html;

class Table extends HtmlTag
{
    protected $isContainer = true;

    protected $properties = [
      'hasTitleRow' => 'boolVal|default:false',
    ];

    protected function markup()
    {

        $m[] = '<table' . $this->htmlAttributes() . '>';

        foreach ($this->getContent() as $index => $contents) {

            if ($this->isTrObject($contents)) {
                $m[] = $contents->toHtml();

            } elseif (is_array($contents)) {

                $m[] = '<tr>';

                foreach ($contents as $i => $c) {
                    if ($this->isTdObject($c) || $this->isThObject($c)) {

                        $m[] = $c->toHtml();
                    } else {
                        $t = new Td([
                          'contains' => $c,
                        ]);
                        $m[] = $t->toHtml();
                    }
                }

                $m[] = '</tr>';

            } else {

                $m[] = '</tr>';

                if ($this->isTdObject($contents) || $this->isThObject($contents)) {
                    $m[] = $contents->toHtml();
                } else {
                    $t = new Td([
                      'contains' => $contents,
                    ]);
                    $m[] = $t->toHtml();
                }

                $m[] = '</tr>';
            }
        }

        $m[] = '</table>';

        return implode("\n", $m);
    }

    /**
     * Returns true when given object is Td Object.
     *
     * @param mixed $contains
     * @return boolean
     */
    protected function isTrObject($contains)
    {
        if (is_object($contains) && get_class($contains) === __NAMESPACE__ . '\\Tr') {
            return true;
        }
        return false;
    }


    /**
     * Returns true when given object is Td Object.
     *
     * @param mixed $contains
     * @return boolean
     */
    protected function isTdObject($contains)
    {
        if (is_object($contains) && get_class($contains) === __NAMESPACE__ . '\\Td') {
            return true;
        }
        return false;
    }

    /**
     * Returns true when given object is Th Object.
     *
     * @param mixed $contains
     * @return boolean
     */
    protected function isThObject($contains)
    {
        if (is_object($contains) && get_class($contains) === __NAMESPACE__ . '\\Th') {
            return true;
        }
        return false;
    }

}
