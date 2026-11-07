<?php

namespace wpf\base;

/**
 * Class CompositeWidget
 * @package wpf\base
 */
abstract class CompositeWidget
    extends Widget
{
    private array $_widgets = [];

    /**
     * @return $this
     */
    public function getComposite(): static
    {
        return $this;
    }

    /**
     * @return array
     */
    public function widgets(): array
    {
        return $this->_widgets;
    }

    /**
     * @param Widget $widget
     * @return void
     */
    public function addWidget(Widget $widget): void
    {
        if (in_array($widget, $this->_widgets, TRUE)) {
            return;
        }
        $this->_widgets[] = $widget;
    }

    /**
     * @param Widget $widget
     * @return void
     */
    public function removeWidget(Widget $widget): void
    {
        $this->_widgets = array_udiff($this->_widgets, array($widget), function ($a, $b) {
            return ($a !== $b);
        });
    }

    /**
     * @param Widget[] $widgets
     */
    public function addWidgets(array $widgets): void
    {
        foreach ($widgets as $widget) {
            $this->addWidget($widget);
        }
    }

    /**
     * @param string $template
     * @param array $excludes Comma separated serial numbers of widgets. [0,..]
     * @return void
     */
    public function wrapWidgets(string $template, array $excludes = []): void
    {
        $i = 0;
        foreach ($this->_widgets as $widget) {
            if (!in_array($i, $excludes)) {
                $wrapped = new Wrapper($widget);
                $wrapped->setTemplate($template);
                $this->_widgets[$i] = $wrapped;
            }
            $i++;
        }
    }

    /**
     * Render all widgets
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