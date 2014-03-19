<?php


ShortcodeParser::get('default')->register('googlemap', function($arguments, $address, $parser, $shortcode) {
    $iframeUrl = sprintf(
        'http://maps.google.com/maps?q=%s&amp;hnear=%s&amp;ie=UTF8&hq=&amp;t=m&amp;z=14&amp;output=embed',
        urlencode($address),
        urlencode($address)
    );
    $width = (isset($args['width']) && $args['width']) ? $args['width'] : 400;
    $height = (isset($args['height']) && $args['height']) ? $args['height'] : 400;
    return sprintf(
        '<iframe width="%d" height="%d" src="%s" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>',
        $width,
        $height,
        $iframeUrl
    );
});
// usage:
// [googlemap,width=500,height=300]97-99 Courtenay Place, Wellington, New Zealand[/googlemap]
