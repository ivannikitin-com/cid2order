<?php
/**
 * Класс Cid2order_plugin
 * Основной класс плагина.
 */
class Cid2order_plugin {

    /**
     * @var mixed
     * Параметры плагина в виде ассоциативного массива
     */
    private $settings;

    /**
     * Конструктор плагина
     */ 
    public function __construct() {
        // Настройки плагина
        $this->settings = new Cid2order_settings();

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
    public function plugins_loaded() {
        // Локализация
        load_plugin_textdomain( CID2ORDER, false, CID2ORDER_DIR . '/lang' );
    }
    
    /**
     * Хук init
     */
    public function wp_init() {
        // Проверка наличия WC        
        if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
        {
            add_action( 'admin_notices', array( $this, 'show_notice_no_wc' ) );
            return;
        }
    }

    /**
     * Предупреждение об отсутствии WooCommerce
     */
    public function show_notice_no_wc() {
        echo '<div class="notice notice-warning no-woocommerce"><p>';
        printf( 
            esc_html__( 'Для работы плагина "%s" требуется установить и активировать плагин WooCommerce.', CID2ORDER ), 
            CID2ORDER  
        );
        _e( 'В настоящий момент все функции плагина деактивированы.', CID2ORDER );
        echo '</p></div>';
    } 

    /**
     * Функция выполняется на странице Thank you page WooCommerce
     * @param int    $order_id    Идентификатор заказа
     */ 
    public function thankyou_page( $order_id ) {
        // Если по какой-то причине $order_id нет, ничего не делаем
        if ( empty( $order_id ) ) return;

        $cid_str = '';

        // Читаем требуемые куки
        foreach ($this->settings->get( 'cookies' ) as $cookie) {
            // Если текущего куки нет, переходим к следующему
            if ( !isset( $_COOKIE[ $cookie ] ) || empty( $_COOKIE[ $cookie ]  ) ) continue;

            $value = sanitize_text_field( $_COOKIE[ $cookie ] );
            $cid_str .= '<br>' . $cookie . ': ' . $value;

            // Сохраняем мета-поле
            update_post_meta( $order_id, $cookie, $value );
        }

         // При необходимости сохраняем данные в заметку к заказу
        if ( $this->settings->get( 'save' )[ 'order_notes' ] && !empty( $cid_str ) ) {
            $order = new WC_Order( $order_id );
            $order->add_order_note( __('Client IDs', CID2ORDER ) . $cid_str );
        }
    }

    /**
     * Функция Добавляет новую колонку в список заказов
     * @param mixed    $columns    Массив колонок
     */ 
    public function custom_shop_order_column( $columns ) {
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
     * @param string    $column        ID колонки
     * @param int       $post_id    ID заказа
     */ 
    function custom_orders_list_column_content( $column, $post_id ) {
        if ( 'cid' == $column) {

            $column_values = array();

            // Читаем требуемые поля -- названия  куки
            foreach ( $this->settings->get( 'cookies' ) as $cookie ) {
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