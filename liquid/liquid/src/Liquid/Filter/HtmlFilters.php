<?php
namespace Liquid\Filter;

class HtmlFilters {

    public static function img_tag($url, $alt = "", $cls = "") {
        return "<img src=\"$url\" alt=\"$alt\" class=\"$cls\"/>";
    }

    public static function script_tag($url) {
        return "<script src=\"$url\" type=\"text/javascript\"></script>";
    }

    public static function stylesheet_tag($url, $media = "all") {
        return "<link href=\"$url\" rel=\"stylesheet\" type=\"text/css\"  media=\"$media\"  />";
    }

    public static function escape_script($html) {
        $preg = "/<script(.*?)>(.*?)<\/script>/is";
        while (preg_match($preg, $html)) {
            $html = preg_replace($preg, '&lt;script${1}&gt;${2}&lt;/script&gt;', $html);
        }
        return $html;
    }

}

$html = <<<EOF
<div>
abc
<script type="javascript">
test1<script>test2</script>
</script>
</div>
<script
test
</script>
EOF;

//echo HtmlFilters::escape_script($html);