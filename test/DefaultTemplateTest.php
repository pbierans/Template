<?php

namespace PatrickBierans\Template;

use PatrickBierans\Container\SolidContainer;
use PHPUnit\Framework\TestCase;

class DefaultTemplateTest extends TestCase {

    public function testIntegration(): void {
        $m = 'Hello World!';

        $template = new DefaultTemplate();

        $template->setMagicCallContainer(
            new SolidContainer([
                'getMessage' => [$this, 'sayMessage']
            ])
        );
        $template->setMagicGetContainer(
            new SolidContainer([
                'message' => $m
            ])
        );
        $this->assertTrue($template->hasMagicCall('getMessage'), 'we have our callable inside the template instance');
        $this->assertTrue($template->hasMagicGet(['message']), 'we have our message inside the template instance');
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($m, $template->getMessage($template), 'our callback got the message back');
    }

    /**
     * @param DefaultTemplate $template
     *
     * @return array|mixed|string
     */
    public function sayMessage(DefaultTemplate $template) {
        /** @noinspection PhpUndefinedFieldInspection */
        return $template->message;
    }
}
