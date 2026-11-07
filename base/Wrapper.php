<?php

namespace wpf\base;

/**
 * Class Wrapper
 * @package wpf\base
 */
class Wrapper extends ViewContextDecorator
{
    protected string|null $template = NULL;
    protected array $vars = [];

    /**
     * @param string $template
     * @return void
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $content = $this->wrapped->render();
        if (!$this->template) {
            return $content;
        }
        $this->vars = array_merge_recursive($this->vars, $this->wrapped->wrapper_vars);
        $this->vars['content'] = $content;

        return render($this->template, $this->vars);
    }
}