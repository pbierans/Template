<?php

namespace PatrickBierans\Template;

use PatrickBierans\Directory\ImageDirectory;

/**
 * Adding some features:
 * - multi page rendering feature based on a config
 * - image gallery from a folder
 * @todo convert into Callables?
 */
class CustomTemplate extends DefaultTemplate {

    /**
     * @throws \Exception
     */
    public function includePages(): void {
        $currentPage = $this->__get('currentPage');
        $menu = $this->__get('menu'); // injected via setVariables() aka setMagicGetContainer()

        $first = true;
        if (\is_array($menu)) {
            foreach ($menu as $id => $label) {
                $pagefile = $id;
                $exp = \explode('.', $id);
                if (!\in_array(end($exp), ['php', 'html'])) {
                    $pagefile .= '.html';
                }
                $current = ($currentPage === $id) ? 'current' : '';
                if ($first) {
                    $current .= ' first';
                }
                echo "<div id='page_$id' class='page $current'>";
                $this->inc($pagefile);
                echo '</div>';
                $first = false;
            }
        }
    }

    /**
     * @param string|null $path
     *
     * @throws \Exception
     */
    public function images($path = null): void {
        if ($path === null) {
            $path = 'img/' . $this->__get('currentPage') . '/';
        }

        $ImageDirectory = new ImageDirectory($path);
        $this->MagicGetContainer->set(['ImageDirectory'], $ImageDirectory);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $Tpl = $this; // used as an "API" inside the template files

        $this->inc('../gallery/a.php');

        foreach ($ImageDirectory as $Image) {
            $this->MagicGetContainer->set(['Image'], $Image);
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->inc('../gallery/i.php');
        }
        $this->inc('../gallery/z.php');
    }
}