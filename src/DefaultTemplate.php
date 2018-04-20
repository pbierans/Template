<?php

namespace PatrickBierans\Template;

use PatrickBierans\Container\SolidContainer;
use PatrickBierans\Container\VariableContainer;
use PatrickBierans\ContainerIntegration\MagicCallIntegration;
use PatrickBierans\ContainerIntegration\MagicGetIntegration;

/**
 * A small template thing for html and php. Nothing fancy.
 * Using the trait setters first is important. The other Setters will rely on it:
 *         $tpl->setVariables($Env); // => MagicGetIntegration
 *         $tpl->setFilters($Filter); // => MagicCallIntegration
 *         $tpl->setRequest($Request);
 *               ->setCurrentPage(); // $this->currentPage
 *                 ->setPageTitle() // $this->pageTitle
 *               ->setCurrentContext(); // $this->currentContext
 *         $tpl->setRootDir($rootDir); // $this->rootDir
 *         $tpl->setMode($mode);
 * All included php template files have one variable set: $Tpl
 * @see DefaultTemplateTest
 */
class DefaultTemplate {

    use MagicGetIntegration; // Variables

    use MagicCallIntegration; // Filters

    /**
     * @var SolidContainer $Request
     */
    protected $Request;

    /**
     * @param VariableContainer $Variables
     */
    public function setVariables(VariableContainer $Variables): void {
        $this->setMagicGetContainer($Variables);
    }

    /**
     * @param array $keys
     * @param $value
     */
    public function addVariable(array $keys, $value): void {
        $this->MagicGetContainer->set($keys, $value);
    }

    /**
     * @param SolidContainer $Filters
     */
    public function setFilters(SolidContainer $Filters): void {
        $this->setMagicCallContainer($Filters);
    }

    /**
     * @param array $Request
     *
     * @throws \Exception
     */
    public function setRequest(array $Request): void {
        $this->Request = new SolidContainer($Request);
        $this->setCurrentPage($this->Request->__get('page') ?? 'index');
        $this->setCurrentContext($this->Request->__get('context') ?? '');
    }

    /**
     * @param string|array $currentPage
     *
     * @throws \Exception
     */
    public function setCurrentPage($currentPage): void {
        $menu = $this->__get('menu');
        if ($menu === null || !isset($menu[$currentPage])) {
            $currentPage = $this->__get('defaultPage');
        }
        $this->MagicGetContainer->set(['currentPage'], $currentPage);
        $this->setPageTitle($menu[$currentPage]);
    }

    /**
     * @param string $pageTitle
     */
    public function setPageTitle($pageTitle): void {
        $this->MagicGetContainer->set(['pageTitle'], $pageTitle);
    }

    /**
     * @param string $currentContext
     */
    protected function setCurrentContext($currentContext): void {
        $this->MagicGetContainer->set(['currentContext'], $currentContext);
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir($rootDir): void {
        $this->MagicGetContainer->set(['rootDir'], $rootDir);
    }

    /**
     * @param string $mode
     */
    public function setMode($mode): void {
        $this->MagicGetContainer->set(['currentMode'], $mode);
    }

    /**
     * @param string $file Dateiname im Ordner js/ ohne Dateiendung ".js"
     */
    public function js($file): void {
        $c = file_get_contents('js/' . $file . '.js');
        echo '<script type="text/javascript" data-file="' . $file . '">';
        echo $c;
        echo '</script>' . "\n";
    }

    /**
     * @param string $file Dateiname im Ordner css/ ohne Dateiendung ".css"
     * @param null $media
     */
    public function css($file, $media = null): void {
        $c = file_get_contents('css/' . $file . '.css');
        if ($media !== null) {
            $media = ' media="' . $media . '"';
        } else {
            $media = '';
        }
        echo '<style type="text/css" data-file="' . $file . '" ' . $media . '>';
        echo $c;
        echo '</style>' . "\n";
    }

    /**
     * @param string $file Dateiname im Ordner tpl/ mit Dateiendung (html/php)
     *
     * @throws \Exception
     */
    public function inc($file): void {
        $pageDir = $this->__get('pageDir'); // <- variable via config
        $pagefile = $pageDir . $file;

        // quasi als "API" im DefaultTemplate nutzbar:
        /** @noinspection PhpUnusedLocalVariableInspection */
        $Tpl = &$this;

        if (file_exists($pagefile)) {
            /** @noinspection PhpIncludeInspection */
            \ob_start();
            include $pagefile;
            $code = \ob_get_clean();
            echo trim($code);
        } else {
            echo "<pre>$pagefile not found</pre>";
        }
    }

    /**
     *
     */
    public function dump(): void {
        echo '<h1>Dumping ' . __CLASS__ . '</h1>';
        echo '<h3>Request:</h3>';
        $this->Request->dump();
        echo '<h3>MagicGetContainer / Properties:</h3>';
        $this->MagicGetContainer->dump();
        echo '<h3>MagicCallContainer / Methods:</h3>';
        $this->MagicCallContainer->dump();
    }

}
