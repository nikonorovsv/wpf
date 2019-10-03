<?php

namespace wpf\base;

/**
 * Class CompositeWidget.
 *
 * @package wpf\base
 */
abstract class CompositeWidget extends Widget
{
    private $_widgets = [];

    /**
     * @return $this
     */
    public function getComposite()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function widgets()
    {
        return $this->_widgets;
    }

    /**
     * @param Widget $widget
     */
    public function addWidget(Widget $widget)
    {
        if (in_array($widget, $this->_widgets, true)) {
            return;
        }
        $this->_widgets[] = $widget;
    }

    /**
     * @param Widget $widget
     */
    public function removeWidget(Widget $widget)
    {
        $this->_widgets = array_udiff($this->_widgets, [$widget], function ($a, $b) {
            return $a !== $b;
        });
    }

    /**
     * @param array $widgets
     */
    public function addWidgets(array $widgets)
    {
        foreach ($widgets as $widget) {
            $this->addWidget($widget);
        }
    }

    /**
     * @param string $template
     * @param array  $excludes Comma separated serial numbers of widgets. [0,..]
     */
    public function wrapWidgets(string $template, array $excludes = [])
    {
        $i = 0;
        foreach ($this->_widgets as $widget) {
            if (!in_array($i, $excludes)) {
                $wrapped = new Wrapper($widget);
                $wrapped->setTemplate($template);
                $this->_widgets[$i] = $wrapped;
            }
            ++$i;
        }
    }

    /**
     * Render all widgets.
     */
    public function render(): string
    {
        $out = $this->before();
        foreach ($this->_widgets as $widget) {
            $out .= $widget->render();
        }
        $out .= $this->after();

        $this->content = $out;

        return $this->template ? render($this->template, $this->getAttributes()) : $out;
    }
}
