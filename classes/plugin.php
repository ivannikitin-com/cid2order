<?php
/**
 * Класс Cid2order_plugin
 * Основной класс плагина. 
 * Является singleton, то есть обращение из любого места должно быть таким Plugin::get()
 */
class Cid2order_plugin {

    /**
	 * @var mixed
     * Параметры плагина в виде ассоциативного массива
	 */
	public $settings;

    /**
     * Параметры по умолчанию
     * Используем статичную переменную, так как константы класса не могут быть массивом
     */
    public static $DEFAULTS = array(
        'cookies'   =>  array( '_ga', '	_ym_uid' )
    );

	/**
	 * Конструктор плагина
     */ 
	public function __construct()
	{
		// Хуки
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'init', array( $this, 'wp_init' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'thankyou_page' ) );
        add_filter( 'manage_edit-shop_order_columns', array( $this, 'custom_shop_order_column' ), 20 );
        add_action( 'manage_shop_order_posts_custom_column', array( $this, 'custom_orders_list_column_content' ), 20, 2 );
	}

	/**
	 * Плагины загружены
	 */
	public function plugins_loaded()
	{
		// Локализация
		load_plugin_textdomain( CID2ORDER, false, basename( dirname( __FILE__ ) ) . '/lang' );
	}
	
	/**
	 * Хук init
	 */
	public function wp_init()
	{
		// Проверка наличия WC		
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
		{
			add_action( 'admin_notices', array( $this, 'show_notice_no_wc' ) );
			return;
		}

        // загрузка параметров плагина
        $this->load_settings( self::$DEFAULTS );
	}

	/**
	 * Предупреждение об отсутствии WooCommerce
	 */
	public function show_notice_no_wc()
	{
		echo '<div class="notice notice-warning no-woocommerce"><p>';
		printf( 
			esc_html__( 'Для работы плагина "%s" требуется установить и активировать плагин WooCommerce.', CID2ORDER ), 
			CID2ORDER  
		);
		_e( 'В настоящий момент все функции плагина деактивированы.', CID2ORDER );
		echo '</p></div>';
	}    

	/**
	 * Загрузка параметров плагина
	 * @param mixed	$defaults	Параметры по умолчанию
     */ 
	public function load_settings( $defaults )
	{
        // TODO: Сделать страницу параметров
        $this->settings = $defaults;
    }

    /**
	 * Функция выполняется на странице Thank you page WooCommerce
     * @param int	$order_id	Идентификатор заказа
     */ 
	public function thankyou_page( $order_id )
	{
        // Если по какой-то причине $order_id нет, ничего не делаем
        if ( empty( $order_id ) ) return;

        // Читаем требуемые куки
        foreach ($this->settings->cookies as $cookie) {
            // Если текущего куки нет, переходим к следующему
            if ( !isset( $_COOKIE[ $cookie ] ) || empty( $_COOKIE[ $cookie ]  ) ) continue;

            // Сохраняем мета-поле
            update_post_meta( $order_id, $cookie, sanitize_text_field( $_COOKIE[ $cookie ] ) );
        }
    }

    /**
	 * Функция Добавляет новую колонку в список заказов
     * @param mixed	$columns	Массив колонок
     */ 
	public function custom_shop_order_column( $columns )
	{
        $new_columns = array();

        // Добавляем колонку ПОСЛЕ order_total
        foreach( $columns as $key => $column){
            $new_columns[$key] = $column;
            if( $key ==  'order_total' ){
                // Новая колонка
                $new_columns['cid'] = __('Client IDs', CID2ORDER );
            }
        }
        return $new_columns;
    }

    /**
	 * Функция выводит данные в новую колонку в списке заказов
     * @param string	$column	    ID колонки
     * @param int   	$post_id	ID заказа
     */ 
    function custom_orders_list_column_content( $column, $post_id )
    {
        if ( 'cid' == $column) {

            $column_values = array();

            // Читаем требуемые поля -- названия  куки
            foreach ( $this->settings->cookies as $cookie ) {
                $value = get_post_meta( $post_id, $cookie, true );
                if ( ! empty( $value ) ) {
                    $column_values[] = $cookie . ': ' . $value;
                }
            }            

            // Выводим полученные значения
            if ( count( $column_values ) > 0 ) {
                echo implode( '<br>',  $column_values );
            }
           
        }
    }
}