<?php

/**
 * Config for namespace 'global'.
 */

return [


  'wrapComponent' => [
    /**
     * Wrap components?
     * When true, components will be wrapped
     * by div containing wrapComponentClass name.
     *
     * @var bool
     */
    'wrap' => true,

    /**
     * Tag, the component will be wrapped with.
     *
     * @var string
     */
    'tag' => 'div',

    /**
     * Name of class, which will be added to
     * wrapped components div.
     * When null namespace of current component
     * will automatically be added.
     *
     * @var string|null
     */
    'class' => null
  ],


  'docking' => [
    /**
     * Component docking point:
     * When true, all components will get wrapped
     * by a tag, marking a componment,
     * that may be removed or where
     * new components my be added.
     *
     * @var bool
     */
    'wrap' => false,

    /**
     * Tag name, that will be used
     * to mark a docking point.
     *
     * Will render e.g. <div>[component]</div>
     *
     * @var string
     */
    'tag' => 'div',

    /**
     * Data attribute name, that will be used
     * to mark a docking point.
     *
     * Will render e.g. <div data-cdp="1">[component]</div>
     *
     * @var string
     */
    'class' => 'cdp' // 'component docking point'
  ]

];
