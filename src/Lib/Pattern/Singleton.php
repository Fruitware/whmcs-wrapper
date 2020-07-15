<?php

namespace Fruitware\WhmcsWrapper\Lib\Pattern;

/**
 * DESCRIPTION:
 *        Singleton Pattern Realization
 * SYNOPSIS:
 *        1) YourClass extends Fruitware\WhmcsWrapper\Lib\Pattern
 *        2) $obYourClass = YourClass::init();
 *        Вот и всё. Подобный вызов всегда будет возвращать один и тот же экзземпляр класса.
 */
abstract class Singleton
{
    /**
     * Обязательный для реализации закрытый извне конструктор класса. Не должен принимать параметры.
     */
    protected function __construct()
    {
    }

    /**
     * Статическая функция инициализации получения экземпляра класса
     * @return $this Объект класса, наследующего Fruitframe_Pattern_Singleton
     */
    public static function init(): self
    {
        static $instance;
        if (!is_object($instance)) {
            $className = get_called_class();
            $instance = new $className();
        }
        return $instance;
    }
}