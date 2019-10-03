<?php

namespace wpf\base;

use InvalidArgumentException;

/**
 * Class ViewContext.
 *
 * @package wpf\base
 */
abstract class ViewContext extends Component implements IViewContext
{
    protected $template = null;

    /**
     * ViewContext constructor.
     *
     * @param array $conf
     */
    public function __construct(array $conf = [])
    {
        parent::__construct($conf);

        // TODO Нужно ли избавляться от вложенного View объекта, или убрать
    // $this->view = view();
    }

    /**
     * @return string
     */
    public function render(): string
    {
        if (!$this->template) {
            $class = static::class;
            throw new InvalidArgumentException(
        __("Template is not defined in '$class'. If you want to render context without template, you need to redefine render() method in your child class without parent method calling.", 'wpf'));
        }
        $html = $this->before();
        $html .= render($this->template, $this->getAttributes(), $this->views_dir ?? null);
        $html .= $this->after();

        return $html;
    }

    /**
     * Выполняется перед загрузкой действия.
     */
    public function before(): string
    {
        return '';
    }

    /**
     * Выполняется после загрузки действия.
     */
    public function after(): string
    {
        return '';
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * If object called as a function,
     * render() method will have been printed.
     */
    public function __invoke()
    {
        echo $this->render();
    }
}
