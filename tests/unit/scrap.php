<?php
/**
 * Created by PhpStorm.
 * User: milko
 * Date: 22/12/2017
 * Time: 14:22
 */

$string = <<<'EOT'
# pippo
"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n"
"%100<10 || n%100>=20) ? 1 : 2);\n"
"X-Generator: Virtaal 0.7.1\n"

#. Name for ABW
msgid "Aruba"
msgstr "Aruba"

#. Name for AFG
msgid "Afghanistan"
msgstr "Afganistanas"

#. Official name for AGO
#, fuzzy
msgid "Republic of Angola"
msgstr "Repubblika Dominikana"

#. Name for CPV
#, fuzzy
#| msgid "Cape Verde"
msgid "Cabo Verde"
msgstr "Կաբո-Վերդե"

#. Name for CUB
msgid "Cuba"
msgstr "Կուբա"

EOT;

$pattern = '/^#\.\s(.+)\n^msgid\s"(.+)"\n^msgstr\s"(.+)"/m';

$count = preg_match_all($pattern, $string, $matches);
print_r("$count\n");
print_r($matches);
