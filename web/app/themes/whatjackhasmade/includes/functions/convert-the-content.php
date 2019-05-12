<?php
/* THIS BEAUTY CONVERTS GUTENBERG BLOCKS TO JSON FOR THE API */
function convert_content($content)
{
    $content = str_replace('https://wjhm.noface.app/', '/', $content);
    $content = str_replace('http://local-whatjackhasmade.co.uk/', '/', $content);
    $ACFTitles = getACFTitles($content, 'field_', '"');

    foreach ($ACFTitles as $key => $value) {
        $content = str_replace($key, $value, $content);
    }

    $content = parse_blocks($content);

    $content = getACFImages($content);

    foreach ($content as &$block) {
        if ($block['attrs']) {
            if ($block['blockName'] === "acf/testimonials"):
                $testimonials = $block['attrs']['data']['testimonials'];

                if (is_array($testimonials) || is_object($testimonials)):
                    foreach ($testimonials as $value) {
                        $valueObject = json_encode($value, true);
                        $valueObject = json_decode($valueObject, true);
                        $testimonialObjects[] = $valueObject;
                    }

                    $block['attrs']['data']['testimonials'] = $testimonialObjects;
                endif;
            endif;
        }
    }

    unset($block);

    return $content;
}
