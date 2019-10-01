<?php

namespace UI;

use UI\UIBuild;

abstract class Control
{
    protected $instance = null;
    protected $attr = [];
    protected $build = null;

    /**
     * @var \UI\UI
     */
    protected static $ui;
    public static $idKey = 'id';

    public function __construct(UIBuild $build, array $attr)
    {
        $this->build = $build;
        if (is_null(self::$ui)) {
            self::$ui = $build->getUI();
        }
        $this->instance = $this->newControl();
        $this->attr = $attr;
        self::$ui->appendNodes($this,  $this->attr[self::$idKey] ?? $this->getHandle());
    }

    abstract public function newControl();

    public function getAttr()
    {
        return $this->attr;
    }

    public function getUI()
    {
        return self::$ui;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getUIInstance()
    {
        return $this->instance;
    }

    public function getHandle()
    {
        return $this->uiControlHandle();
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __call($func, $args)
    {
        switch (count($args)) {
            case 0:
                return self::$ui->$func($this->instance);
            case 1:
                return self::$ui->$func($this->instance, $args[0]);
            case 2:
                return self::$ui->$func($this->instance, $args[0], $args[1]);
            case 3:
                return self::$ui->$func($this->instance, $args[0], $args[1], $args[2]);
            case 4:
                return self::$ui->$func($this->instance, $args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array([self::$ui, $func], $args);
        }
    }

    public function show()
    {
        $this->controlShow();
    }

    public function hide()
    {
        $this->controlHide();
    }

    public function enable()
    {
        $this->controlEnable();
    }

    public function disbale()
    {
        $this->controlDisable();
    }
    public function destroy()
    {
        $this->controlDestroy();
    }

    public function parent()
    {
        return $this->controlParent();
    }

    public function setParent($parent)
    {
        $this->controlSetParent($parent);
    }

    public function isVisible()
    {
        return $this->controlVisible();
    }

    public function isEnabled()
    {
        return $this->controlEnabled();
    }

    public function getTopLevel()
    {
        return $this->controlToplevel();
    }

    public function bindEvent($event, array $callable)
    {
        $this->$event($this->instance, function (...$params) use ($callable) {
            $func = $callable[0];
            $data = $callable[1] ?? null;
            try {
                switch (count($params)) {
                    case 0:
                        $func($data);
                        break;
                    case 1:
                        $func($data);
                        break;
                    case 2:
                        $func($params[0], $data);
                        break;
                    case 3:
                        $func($params[0], $params[1], $data);
                        break;
                    case 4:
                        $func($params[0], $params[1], $params[2], $data);
                        break;
                    default:
                        array_pop($params);
                        $params[] = $data;
                        call_user_func_array($func, $params);
                        break;
                }
            } catch (\Exception $e) {
                echo $e;
            } catch (\Error $e) {
                echo $e;
            }
        }, null);
    }
}