<?php

namespace Phalcon\Debug;

use Phalcon\Db\Profiler as Profiler;
use Phalcon\DiInterface;
use Phalcon\Escaper as Escaper;
use Phalcon\Mvc\Url as URL;
use Phalcon\Mvc\View as View;
use Phalcon\Di\InjectionAwareInterface;

/**
 * Class DebugWidget
 * @package Phalcon\Debug
 * @author Andre Figueira <andre.figueira@me.com>
 */
class DebugWidget implements InjectionAwareInterface
{
    /**
     * @var
     */
    protected $di;

    /**
     * @var mixed
     */
    protected $startTime;

    /**
     * @var mixed
     */
    protected $endTime;

    /**
     * @var int
     */
    protected $queryCount = 0;

    /**
     * @var
     */
    protected $profiler;

    /**
     * @var array
     */
    protected $viewsRendered = [];

    /**
     * @var array
     */
    protected $serviceNames = [];

    /**
     * @param DiInterface $di
     * @param array $serviceNames
     */
    public function __construct($di, $serviceNames = ['db' => ['db'], 'dispatch' => ['dispatcher'], 'view' => ['view']])
    {
        $this->di = $di;
        $this->startTime = microtime(true);
        $this->profiler = new Profiler();

        $eventsManager = $di->get('eventsManager');

        foreach ($di->getServices() as $service) {
            $name = $service->getName();
            foreach ($serviceNames as $eventName => $services) {
                if (in_array($name, $services)) {
                    $service->setShared(true);
                    $di->get($name)->setEventsManager($eventsManager);
                    break;
                }
            }
        }

        foreach (array_keys($serviceNames) as $eventName) {
            $eventsManager->attach($eventName, $this);
        }

        $this->serviceNames = $serviceNames;
    }

    /**
     * @param DiInterface $di
     */
    public function setDI(DiInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @return mixed
     */
    public function getDI()
    {
        return $this->di;
    }

    /**
     * @param $event
     * @return mixed
     */
    public function getServices($event)
    {
        return $this->serviceNames[$event];
    }

    /**
     * @param $event
     * @param $connection
     */
    public function beforeQuery($event, $connection)
    {
        $this->profiler->startProfile(
            $connection->getRealSQLStatement(),
            $connection->getSQLVariables(),
            $connection->getSQLBindTypes()
        );
    }

    /**
     * @param $event
     * @param $connection
     */
    public function afterQuery($event, $connection)
    {
        $this->profiler->stopProfile();
        $this->queryCount++;
    }

    /**
     * Gets/Saves information about views and stores truncated viewParams.
     *
     * @param unknown $event
     * @param unknown $view
     * @param unknown $file
     */
    public function beforeRenderView($event, $view, $file)
    {
        $params = [];
        $toView = $view->getParamsToView();
        $toView = !$toView ? [] : $toView;

        foreach ($toView as $k => $v) {
            if (is_object($v)) {
                $params[$k] = get_class($v);
            } elseif(is_array($v)) {
                $array = [];

                foreach ($v as $key=>$value) {
                    if (is_object($value)) {
                        $array[$key] = get_class($value);
                    } elseif (is_array($value)) {
                        $array[$key] = 'Array[...]';
                    } else {
                        $array[$key] = $value;
                    }
                }

                $params[$k] = $array;
            } else {
                $params[$k] = (string) $v;
            }
        }

        $this->viewsRendered[] = [
            'path' => $view->getActiveRenderPath(),
            'params' => $params,
            'controller' => $view->getControllerName(),
            'action' => $view->getActionName(),
        ];
    }

    /**
     * @param $event
     * @param $view
     * @param $viewFile
     */
    public function afterRender($event, $view, $viewFile)
    {
        $this->endTime = microtime(true);
        $content = $view->getContent();
        $scripts = '</head>';
        $content = str_replace('</head>', $scripts, $content);
        $rendered = $this->renderToolbar();
        $rendered .= '</body>';
        $content = str_replace('</body>', $rendered, $content);

        $view->setContent($content);
    }

    /**
     * @return string
     */
    public function renderToolbar()
    {
        $view = new View();
        $viewDir = __DIR__ . '/views/';
        $view->setViewsDir($viewDir);

        $view->setVar('debugWidget', $this);

        $content = $view->getRender('toolbar', 'index');

        forp_end();

        return $content;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return array
     */
    public function getRenderedViews()
    {
        return $this->viewsRendered;
    }

    /**
     * @return int
     */
    public function getQueryCount()
    {
        return $this->queryCount;
    }

    /**
     * @return Profiler
     */
    public function getProfiler()
    {
        return $this->profiler;
    }
}
