<?php
/**
 * Класс Cid2order_settings
 * Обеспечивает чтение и сохранение настроек плагина. 
 */
class Cid2order_settings {
    /**
     * Параметры по умолчанию
     * Используем статичную переменную, так как константы класса не могут быть массивом
     */
    private static $DEFAULTS = array(
        'cookies' => array( '_ga', '_ym_uid' ),
        'save'    => array(
            'order_notes'  => true 
        )
    );

    /**
     * @var mixed
     * Параметры плагина в виде ассоциативного массива
     */
    private $settings;
    
    /**
     * Конструктор класса, инициализация
     */ 
    public function __construct() {
        // Инициализация массива настроек
        $this->settings = array();
    }

    /**
     * Чтение параметра настроек
     * @param string    $param    Имя параметра
     * @return mixed    Возвращает значение параметра или null
     */
    public function get( $param ) {
        // Если массив настроек пустой, значит нужно загрузить настройки
        if ( 0 == count( $this->settings ) ) $this->load_settings( self::$DEFAULTS );

        // Проверяем наличие параметра
        if ( !array_key_exists( $param, $this->settings ) ) return null;

        // Возвращает параметр
        return $this->settings[ $param ];
    }

    /**
     * Загрузка параметров плагина
     * @param mixed    $defaults    Параметры по умолчанию
     */ 
    private function load_settings( $defaults ) {
        $this->settings = $defaults;
    }


}