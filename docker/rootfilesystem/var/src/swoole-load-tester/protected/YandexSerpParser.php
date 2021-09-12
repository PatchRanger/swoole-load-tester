<?php
declare(strict_types=1);

namespace app;

/**
 * @link https://gist.github.com/stormdi/b27c9636203814344e21d7fca8d810c3
 */
class YandexSerpParser
{
    public static function parseYandexSerp(string $body): \Generator
    {
        // composer require voku/simple_html_dom
        $dom = \voku\helper\HtmlDomParser::str_get_html($body);
        $parsedElements = $dom->findMulti('.serp-item');

        foreach ($parsedElements as $element) {
            $elementDataAttributes = $element->getAllAttributes();
            $directMarks = array_filter(array_keys($elementDataAttributes), fn($key) => str_contains($key, 'data-') && mb_strlen($key) === 9 && $key !== 'data-fast');

            $linkElement = $element->findOne('a');
            $link = $linkElement->getAttribute('href');

            if (str_starts_with($link, 'https://yandex.ru/turbo/') || mb_strpos($link, 'turbopages') !== false) {
                $turboLink = $linkElement->getAttribute('data-counter');
                $turboLink = json_decode(htmlspecialchars_decode($turboLink))[1] ?? $link;
                $link = $turboLink;
            }

            if (
                count($directMarks) >= 2
                || str_starts_with($link, '//')
                || str_contains($link, 'yabs.yandex.ru/count')
                || str_contains($link, 'yandex.com/images')
            ) {
                continue;
            }

            yield $link;
        }
    }
}
