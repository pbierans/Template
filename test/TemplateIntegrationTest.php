<?php

namespace PatrickBierans\Template;

use PatrickBierans\Container\SolidContainer;
use PatrickBierans\Container\VariableContainer;
use PatrickBierans\Template\CustomTemplate as Template;
use PatrickBierans\TemplateFilter\Trim;
use PHPUnit\Framework\TestCase;

class TemplateIntegrationTest extends TestCase {

    /**
     * @throws \Exception
     */
    public function testIntegration(): void {

        $template = new Template();

        $config = include __DIR__ . '/config/mode/context/index.php';

        $template->setVariables(new VariableContainer($config));

        $template->setFilters(new SolidContainer([
            'trim' => [Trim::class, 'apply']
        ]));

        $template->setRootDir(__DIR__);
        $template->addVariable(['pageDir'], __DIR__ . '/pages/');
        $template->setRequest([
            'page' => 'undefined-file.php'
        ]);

        ob_start();
        $template->addVariable(['somevariable'], '     some trimable value    ');
        $template->includePages();
        $output = ob_get_clean();

        $this->assertEquals("<div id='page_index.php' class='page current first'>some trimable value</div>", $output);

        ob_start();
        $template->addVariable(['noisetext'], '-might-be-noisy');
        $template->inc('snippets/noisy.php');
        $output = ob_get_clean();

        $this->assertEquals('some trimable value-might-be-noisy', $output);
    }
}