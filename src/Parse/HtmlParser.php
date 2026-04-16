<?php

namespace wwaz\Components\Parse;

use DOMDocument;
use wwaz\Components\Helper\Arrays\Flatten;

/**
 * Parses HTML documents.
 *
 */
class HtmlParser
{
    /**
     * HTML markup.
     *
     * @var string
     */
    protected $html;


    /**
     * Constructor.
     *
     * @param string $html
     */
    public function __construct($html)
    {
        $this->html = $html;
    }


    /**
     * Returns parsed data.
     *
     * @param none
     * @return array
     */
    public function toData()
    {
        return $this->parse();
    }


    /**
     * Inits parsing.
     *
     * @param none
     * @return array
     */
    protected function parse()
    {

        // Suppress DOMDocument error messages
        libxml_use_internal_errors(true);

        @$doc = new DOMDocument();

        $doc->loadHTML($this->html);

        $body = $this->getDomDocumentBody($doc);

        if ($body) {
            $data = $this->parseDocument($body)['content'];
            return count($data) === 1 ? $data[0] : $data;
        }
    }


    /**
     * Returns DOMDocument's body.
     *
     * @param DOMDocument
     * @return DOMElement
     */
    protected function getDomDocumentBody(DOMDocument $doc)
    {
        $body = $doc->getElementsByTagName('body');
        if ($body && 0 < $body->length) {
            return $body->item(0);
        }
    }


    /**
     * Parses html recursively.
     *
     * @param mixed $doc (mixed because recursive – DOMElement, DOMText ...)
     * @return array
     */
    protected function parseDocument($doc)
    {

        $array = [];

        if ($doc->hasAttributes()) {
            foreach ($doc->attributes as $attribute) {
                $array['attributes'][$attribute->name] = $attribute->value;
            }
        }

        if ($doc->nodeType == XML_TEXT_NODE || $doc->nodeType == XML_CDATA_SECTION_NODE) {
            // Handle text node

            $value = $doc->nodeValue;
            if (!empty($value) && !preg_match("/\n+/", $value)) {
                $array = $value;
            }

        } else {
            // Handle all other nodes

            $array['type'] = 'fragment.html.' . $doc->nodeName;

            if ($doc->hasChildNodes()) {

                $children = $doc->childNodes;

                for ($i = 0; $i < $children->length; $i++) {

                    $child = $this->parseDocument($children->item($i));

                    //Don't keep textnode with only spaces and newline
                    if (!empty($child)) {
                        $array['content'][] = $child;
                    }
                }
            }
        }
        return $array;
    }

}
