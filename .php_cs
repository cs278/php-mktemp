
<?php

return (new Cs278\CsFixer\ConfigBuilder())
    ->useFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
    )
    ->build()
;
