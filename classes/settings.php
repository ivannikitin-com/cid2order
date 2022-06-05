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
        'integrations' => array(
            'woocommerce' => array(
                'enabled' => true,
                'save'    => array(
                    'order_notes'  => true 
                )
            ),
            'cf7' => array(
                'enabled' => true
            )            
        ),

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

        // Страница настроек плагина
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );

        // Инициализация админки
        add_action( 'admin_init', array( $this, 'admin_init' ) );
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

    /** 
     * Параметры для API настроек
     */
    const SECTION_ID = 'cid2order_section_';
    const OPTION_GROUP = 'cid2order_option_group';
    const OPTION_NAME = 'cid2order_settings';


    /**
     * Создание страницы настроек плагина
     */
    public function add_settings_page() {
        add_options_page( 
            __('Настройки плагина Cid2Order', CID2ORDER ), 
            __('Cid2Order', CID2ORDER ), 
            'manage_options', 
            CID2ORDER, 
            array( $this, 'show_settings_page' ) );
    }

    /**
     * Вывод страницы настроек 
     */
    public function show_settings_page() {
        @include( CID2ORDER_DIR . '/views/settings_page.php' );
    }

    /**
     * Инициализация админки, регистрация страницы настроек
     */
    public function admin_init() {
        // Регистрация
        register_setting( self::OPTION_GROUP, self::OPTION_NAME, array( $this, 'sanitize_callback' ) );

        // Секция основных настроек
        add_settings_section( 
            self::SECTION_ID . 'main',                  // Идентификатор секции
            __('Основные настройки', CID2ORDER ),       // Заголовок секции
            '',                                         // Функция описания секции
            CID2ORDER );                                // Страница на которой выводить секцию

        // Поле Куки
        add_settings_field( 
            'cookies',                                  // Ярлык (slug) опции, используется как идентификатор поля
            __('Куки отслеживания', CID2ORDER ),        // Название поля
            array( $this, 'field_callback' ) ,          // Функция вывода поля
            CID2ORDER,                                  // Страница меню в которую будет добавлено поле
            self::SECTION_ID . 'main',                  // Название секции настроек, в которую будет добавлено поле
            array(                                      // Дополнительные параметры для функции вывода
                'param' => 'cookies',
                'type'  => 'text'
            )                         
        );
    }

    /**
     * Инициализация админки, регистрация страницы настроек
     */
    public function field_callback( $args ) {
        $value = $this->get( $args[ 'param' ] );
        echo '<input type="' . $args[ 'type' ] . '" name="option_name[input]" value="' .  esc_attr( $value ) . '" />';
    }


    /**
     * Метод проверки и очистки данных 
     */
    public function sanitize_callback( $options ) {
        // очищаем...
        return $options;
    }




}